<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Lamaran;
use App\Models\Medical;
use Illuminate\Support\Str;
use Storage;


class MedicalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Medical/Index');
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            'nama' => 'required',
            'hasil' => 'required',
            'tanggal' => 'required',
            'file' => 'required',
        ];

        $pesan = [
            'nama.required' => 'Nama Faskes Wajib Diisi!',
            'hasil.required' => 'Hasil Wajib Diisi!',
            'tanggal.required' => 'Tanggal Wajib Diisi!',
            'file.required' => 'File Dokumen Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }else{
            DB::beginTransaction();
            try{
                $lamaran = Lamaran::where('id', $request->lamaran_id)->first();

                $data = new Medical();
                $data->user_id = $lamaran->user_id;
                $data->lamaran_id = $request->lamaran_id;
                $data->nama = $request->nama;
                $data->tanggal = $request->tanggal;
                $data->hasil = $request->hasil;
                
                if (is_file($request->file)) {
                    $fileDir = 'document/' . $lamaran->user_id .'/' . Str::random(32) . '.' . $request->file('file')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($fileDir, fopen($request->file('file'), 'r+'));
                    $data->file = $fileDir;
                }
                $data->status = 'pending';
                $data->save();

                $lamaran->status = 'medical';
                $lamaran->save();


            }catch(\QueryException $e){
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data'
                ], 500);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Medical checkup berhasil ditambahkan',
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $data = Medical::with(['lamaran' => function($q){
            return $q->with(['user.detail', 'lowongan']);
        }])->where('id', $id)->first();
        
        return Inertia::render('Medical/Show', [
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'nama' => 'required',
            'tanggal' => 'required|date',
            'hasil' => 'required',
            'status' => 'required',
        ];

        $pesan = [
            'nama.required' => 'Nama Faskes Wajib Diisi!',
            'tanggal.required' => 'Tanggal Wajib Diisi!',
            'tanggal.date' => 'Format Tanggal Salah!',
            'hasil.required' => 'Hasil Wajib Diisi!',
            'status.required' => 'Status Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try{
            $data = Medical::findOrFail($id);
            
            // Handle file upload if new file is provided
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($data->file && Storage::disk('public')->exists($data->file)) {
                    Storage::disk('public')->delete($data->file);
                }
                
                // Store new file
                $lamaran = $data->lamaran;
                $fileDir = 'document/' . $lamaran->user_id .'/' . Str::random(32) . '.' . $request->file('file')->getClientOriginalExtension();
                Storage::disk('public')->put($fileDir, fopen($request->file('file'), 'r+'));
                $data->file = $fileDir;
            }
            
            $data->nama = $request->nama;
            $data->tanggal = $request->tanggal;
            $data->hasil = $request->hasil;
            $data->status = $request->status;
            $data->catatan = $request->catatan;
            $data->save();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data medical checkup berhasil diperbarui'
            ]);

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $data = Medical::findOrFail($id);
            
            // Delete associated file if exists
            if ($data->file && Storage::disk('public')->exists($data->file)) {
                Storage::disk('public')->delete($data->file);
            }
            
            $data->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data medical checkup berhasil dihapus'
            ]);
            
        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }

    public function state($id, Request $request)
    {

        $data = Medical::with(['user'])->where('id', $id)->first();

        if($request->state == 'valid'){
            $data->status = 'valid';
        }else{
            $data->status = 'tidak_valid';
            $data->catatan = $request->reason;
        }
        $data->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
        ]);
    }

    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        $limit = ($request->limit) ? $request->limit : 25;
        $paging = !empty($request->page) ? true : false;

        $elq = Medical::with(['lamaran' => function($q){
            return $q->with(['user', 'lowongan']);
        }])
        ->when($request->q, function($query, $search){
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%');
            });
        })
        ->with(['user'])
        ->orderBy($sort, $sortDir);
        
        if($paging){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }

        return response()->json($data);
    }
}
