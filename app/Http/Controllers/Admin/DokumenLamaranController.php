<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Lamaran;
use App\Models\DokumenLamaran;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DokumenLamaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('DokumenLamaran/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('DokumenLamaran/Form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'lamaran_id' => 'required|exists:lamaran,id',
        ];

        $pesan = [
            'lamaran_id.required' => 'Lamaran wajib diisi!',
            'lamaran_id.exists' => 'Lamaran tidak ditemukan!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $lamaran = Lamaran::findOrFail($request->lamaran_id);
            // dd($request->all());
            
            // Check if document already exists
            $existingDocument = DokumenLamaran::where('lamaran_id', $request->lamaran_id)->first();
            
            if ($existingDocument) {
                $data = $existingDocument;
            } else {
                $data = new DokumenLamaran();
                $data->lamaran_id = $request->lamaran_id;
                $data->status = 'pending';
            }

            // Handle file uploads
            $fileFields = [
                'ktp', 'kk', 'akte_lahir', 'buku_nikah', 'surat_keterangan_sehat', 
                'surat_izin_keluarga', 'ijazah', 'sertifikat_keahlian', 
                'paspor', 'surat_pengalaman', 'skck'
            ];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    // Delete old file if exists
                    if ($data->$field && Storage::disk('public')->exists($data->$field)) {
                        Storage::disk('public')->delete($data->$field);
                    }
                    
                    // Store new file
                    $fileDir = 'documents/' . $lamaran->user_id . '/' . $field . '/' . Str::random(32) . '.' . $request->file($field)->getClientOriginalExtension();
                    Storage::disk('public')->put($fileDir, fopen($request->file($field), 'r+'));
                    $data->$field = $fileDir;
                }
            }

            $data->save();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil disimpan',
                'data' => $data
            ]);

        } catch (\QueryException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = DokumenLamaran::with(['lamaran.user', 'lamaran.lowongan'])->findOrFail($id);
        
        return Inertia::render('DokumenLamaran/Show', [
            'data' => $data
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = DokumenLamaran::with(['lamaran.user', 'lamaran.lowongan'])->findOrFail($id);
        
        return Inertia::render('DokumenLamaran/Form', [
            'data' => $data,
            'editMode' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'status' => 'required|in:pending,diterima,ditolak',
            'catatan' => 'nullable|string'
        ];

        $pesan = [
            'status.required' => 'Status wajib diisi!',
            'status.in' => 'Status tidak valid!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = DokumenLamaran::findOrFail($id);
            
            $data->status = $request->status;
            $data->catatan = $request->catatan;
            $data->save();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Status dokumen berhasil diperbarui',
                'data' => $data
            ]);

        } catch (\QueryException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = DokumenLamaran::findOrFail($id);
            
            // Delete all associated files
            $fileFields = [
                'ktp', 'kk', 'akte_lahir', 'buku_nikah', 'surat_keterangan_sehat', 
                'surat_izin_keluarga', 'ijazah', 'sertifikat_keahlian', 
                'paspor', 'surat_pengalaman', 'skck'
            ];

            foreach ($fileFields as $field) {
                if ($data->$field && Storage::disk('public')->exists($data->$field)) {
                    Storage::disk('public')->delete($data->$field);
                }
            }
            
            $data->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);
            
        } catch (\QueryException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }

    /**
     * Get documents by lamaran ID
     */
    public function byLamaran($lamaranId)
    {
        $lamaran = Lamaran::with(['user', 'lowongan'])->findOrFail($lamaranId);
        $dokumen = DokumenLamaran::where('lamaran_id', $lamaranId)->first();
        
        return Inertia::render('DokumenLamaran/ByLamaran', [
            'lamaran' => $lamaran,
            'dokumen' => $dokumen
        ]);
    }

    /**
     * View document file
     */
    public function view($id)
    {
        $dokumen = DokumenLamaran::findOrFail($id);
        
        // This would need to be implemented based on your file viewing requirements
        // For now, we'll just return the document data
        return response()->json([
            'success' => true,
            'data' => $dokumen
        ]);
    }

    /**
     * Update document status
     */
    public function updateStatus(Request $request, $id)
    {
        $rules = [
            'status' => 'required|in:pending,diterima,ditolak',
            'catatan' => 'nullable|string'
        ];

        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = DokumenLamaran::findOrFail($id);
            
            $data->status = $request->status;
            $data->catatan = $request->catatan;
            $data->save();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Status dokumen berhasil diperbarui',
                'data' => $data
            ]);

        } catch (\QueryException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status'
            ], 500);
        }
    }

    /**
     * Get data for DataTable
     */
    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        $limit = ($request->limit) ? $request->limit : 25;
        $paging = !empty($request->page) ? true : false;

        $elq = DokumenLamaran::with(['lamaran.user', 'lamaran.lowongan'])
            ->when($request->search, function($query, $search) {
                $query->whereHas('lamaran.user', function($q) use ($search) {
                    $q->where('nama', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy($sort, $sortDir);
        
        if ($paging) {
            $data = $elq->paginate($limit);
        } else {
            $data = $elq->get();
        }

        return response()->json($data);
    }
}