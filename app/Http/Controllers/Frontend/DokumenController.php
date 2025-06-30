<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\DokumenLamaran;
use App\Models\Lamaran;
use App\Models\KategoriDokumen;

class DokumenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void 
     */
    public function __construct() {
        $this->middleware('auth:web');
        $this->middleware('verified');
    }

    /**
     * Show documents for specific application
     * @param int $lamaranId
     * @return Renderable
     */
    public function index($lamaranId)
    {
        $user = auth()->user();
        
        $lamaran = $user->lamaran()->with([
            'lowongan',
            'dokumen' => function($query) {
                $query->with('kategoriDokumen')->orderBy('created_at', 'desc');
            }
        ])->findOrFail($lamaranId);

        // Get required documents based on current status
        $requiredDocuments = $this->getRequiredDocuments($lamaran->status);
        
        // Get missing documents
        $uploadedKategoriIds = $lamaran->dokumen->pluck('kategori_dokumen_id');
        $missingDocuments = $requiredDocuments->whereNotIn('id', $uploadedKategoriIds);

        // Calculate completion percentage
        $totalRequired = $requiredDocuments->where('wajib', true)->count();
        $uploadedRequired = $lamaran->dokumen()
            ->whereHas('kategoriDokumen', function($q) {
                $q->where('wajib', true);
            })
            ->count();
        
        $completionPercentage = $totalRequired > 0 ? ($uploadedRequired / $totalRequired) * 100 : 0;

        return Inertia::render('User/Dokumen/Index', [
            'lamaran' => $lamaran,
            'requiredDocuments' => $requiredDocuments,
            'missingDocuments' => $missingDocuments,
            'completionPercentage' => round($completionPercentage, 1)
        ]);
    }

    /**
     * Show upload form for specific document category
     * @param int $lamaranId
     * @param int $kategoriId
     * @return Renderable
     */
    public function create($lamaranId, $kategoriId)
    {
        $user = auth()->user();
        
        $lamaran = $user->lamaran()->findOrFail($lamaranId);
        $kategori = KategoriDokumen::findOrFail($kategoriId);
        
        // Check if document already exists
        $existingDocument = $lamaran->dokumen()
            ->where('kategori_dokumen_id', $kategoriId)
            ->first();

        if ($existingDocument) {
            return redirect()->route('user.dokumen.index', $lamaranId)
                ->with('info', 'Dokumen ' . $kategori->nama_kategori . ' sudah pernah diupload.');
        }

        // Check if application status allows upload
        if (!$this->canUploadDocument($lamaran->status, $kategori->jenis_dokumen)) {
            return redirect()->route('user.dokumen.index', $lamaranId)
                ->with('warning', 'Upload dokumen tidak diizinkan untuk status saat ini.');
        }

        return Inertia::render('User/Dokumen/Create', [
            'lamaran' => $lamaran->load('lowongan'),
            'kategori' => $kategori
        ]);
    }

    /**
     * Store uploaded document
     * @param Request $request
     * @param int $lamaranId
     * @return Response
     */
    public function store(Request $request, $lamaranId)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'kategori_dokumen_id' => 'required|exists:kategori_dokumen,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB
            'nama_file' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            DB::beginTransaction();

            $lamaran = $user->lamaran()->findOrFail($lamaranId);
            $kategori = KategoriDokumen::findOrFail($request->kategori_dokumen_id);
            
            // Check if document already exists
            $existingDocument = $lamaran->dokumen()
                ->where('kategori_dokumen_id', $request->kategori_dokumen_id)
                ->first();

            if ($existingDocument) {
                return back()->with('error', 'Dokumen ' . $kategori->nama_kategori . ' sudah pernah diupload.');
            }

            // Check if can upload
            if (!$this->canUploadDocument($lamaran->status, $kategori->jenis_dokumen)) {
                return back()->with('error', 'Upload dokumen tidak diizinkan untuk status saat ini.');
            }

            // Handle file upload
            $file = $request->file('file');
            $fileName = $request->nama_file ?: $file->getClientOriginalName();
            
            // Create unique file path
            $fileExtension = $file->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $filePath = 'dokumen/' . $lamaran->id . '/' . $uniqueFileName;
            
            // Store file
            $file->storeAs('public/' . dirname($filePath), basename($filePath));

            // Create document record
            $dokumen = DokumenLamaran::create([
                'lamaran_id' => $lamaran->id,
                'kategori_dokumen_id' => $request->kategori_dokumen_id,
                'nama_file' => $fileName,
                'file_path' => $filePath,
                'status' => 'pending',
                'diupload_tanggal' => now()
            ]);

            DB::commit();

            return redirect()->route('user.dokumen.index', $lamaranId)
                ->with('success', 'Dokumen ' . $kategori->nama_kategori . ' berhasil diupload!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->with('error', 'Gagal mengupload dokumen: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Show specific document
     * @param int $lamaranId
     * @param int $id
     * @return Renderable
     */
    public function show($lamaranId, $id)
    {
        $user = auth()->user();
        
        $lamaran = $user->lamaran()->findOrFail($lamaranId);
        $dokumen = $lamaran->dokumen()->with(['kategoriDokumen'])->findOrFail($id);

        return Inertia::render('User/Dokumen/Show', [
            'lamaran' => $lamaran->load('lowongan'),
            'dokumen' => $dokumen
        ]);
    }

    /**
     * Replace existing document
     * @param Request $request
     * @param int $lamaranId
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $lamaranId, $id)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB
            'nama_file' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $lamaran = $user->lamaran()->findOrFail($lamaranId);
            $dokumen = $lamaran->dokumen()->findOrFail($id);
            
            // Check if document can be updated
            if ($dokumen->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen yang sudah disetujui tidak dapat diganti'
                ], 422);
            }

            // Delete old file
            if (Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }

            // Handle new file upload
            $file = $request->file('file');
            $fileName = $request->nama_file ?: $file->getClientOriginalName();
            
            $fileExtension = $file->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $filePath = 'dokumen/' . $lamaran->id . '/' . $uniqueFileName;
            
            // Store new file
            $file->storeAs('public/' . dirname($filePath), basename($filePath));

            // Update document record
            $dokumen->update([
                'nama_file' => $fileName,
                'file_path' => $filePath,
                'status' => 'pending',
                'catatan' => null,
                'direview_oleh' => null,
                'direview_tanggal' => null,
                'diupload_tanggal' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diperbarui',
                'dokumen' => $dokumen->fresh(['kategoriDokumen'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download document
     * @param int $lamaranId
     * @param int $id
     * @return Response
     */
    public function download($lamaranId, $id)
    {
        $user = auth()->user();
        
        try {
            $lamaran = $user->lamaran()->findOrFail($lamaranId);
            $dokumen = $lamaran->dokumen()->findOrFail($id);
            
            $filePath = 'public/' . $dokumen->file_path;
            
            if (!Storage::exists($filePath)) {
                return back()->with('error', 'File tidak ditemukan');
            }

            return Storage::download($filePath, $dokumen->nama_file);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh file: ' . $e->getMessage());
        }
    }

    /**
     * View document in browser
     * @param int $lamaranId
     * @param int $id
     * @return Response
     */
    public function view($lamaranId, $id)
    {
        $user = auth()->user();
        
        try {
            $lamaran = $user->lamaran()->findOrFail($lamaranId);
            $dokumen = $lamaran->dokumen()->findOrFail($id);
            
            $filePath = 'public/' . $dokumen->file_path;
            
            if (!Storage::exists($filePath)) {
                abort(404, 'File tidak ditemukan');
            }

            $mimeType = Storage::mimeType($filePath);
            $fileContent = Storage::get($filePath);

            return response($fileContent)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . $dokumen->nama_file . '"');

        } catch (\Exception $e) {
            abort(500, 'Gagal membuka file');
        }
    }

    /**
     * Delete document
     * @param int $lamaranId
     * @param int $id
     * @return Response
     */
    public function destroy($lamaranId, $id)
    {
        $user = auth()->user();
        
        try {
            DB::beginTransaction();
            
            $lamaran = $user->lamaran()->findOrFail($lamaranId);
            $dokumen = $lamaran->dokumen()->findOrFail($id);
            
            // Check if document can be deleted
            if ($dokumen->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen yang sudah disetujui tidak dapat dihapus'
                ], 422);
            }
            
            // Delete file from storage
            if (Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }
            
            $dokumen->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get document upload progress for application
     * @param int $lamaranId
     * @return Response
     */
    public function progress($lamaranId)
    {
        $user = auth()->user();
        $lamaran = $user->lamaran()->findOrFail($lamaranId);

        $requiredDocuments = $this->getRequiredDocuments($lamaran->status);
        $uploadedDocuments = $lamaran->dokumen;

        $progress = [];
        foreach ($requiredDocuments as $kategori) {
            $uploaded = $uploadedDocuments->firstWhere('kategori_dokumen_id', $kategori->id);
            
            $progress[] = [
                'kategori' => $kategori,
                'uploaded' => $uploaded ? true : false,
                'status' => $uploaded ? $uploaded->status : null,
                'dokumen' => $uploaded
            ];
        }

        return response()->json($progress);
    }

    /**
     * Get required documents based on application status
     */
    private function getRequiredDocuments($status)
    {
        switch($status) {
            case 'pending':
            case 'diterima':
                return KategoriDokumen::pendaftaran()->get();
            case 'medical':
                return KategoriDokumen::medical()->get();
            case 'siap':
                return KategoriDokumen::keberangkatan()->get();
            default:
                return collect();
        }
    }

    /**
     * Check if user can upload document for current status
     */
    private function canUploadDocument($lamaranStatus, $jenisdokumen)
    {
        $allowedUploads = [
            'pending' => ['pendaftaran'],
            'diterima' => ['pendaftaran'],
            'interview' => ['pendaftaran'],
            'medical' => ['pendaftaran', 'medical'],
            'pelatihan' => ['pendaftaran', 'medical'],
            'siap' => ['pendaftaran', 'medical', 'keberangkatan'],
            'selesai' => [], // No uploads allowed
            'ditolak' => [] // No uploads allowed
        ];

        return in_array($jenisDocumen, $allowedUploads[$lamaranStatus] ?? []);
    }
}