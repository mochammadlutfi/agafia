<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Lamaran;
use App\Models\Training;


class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Training/Index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Training/Form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'lamaran_id' => 'required',
            'program_id' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ];

        $pesan = [
            'lamaran_id.required' => 'Talent Wajib Diisi!',
            'program_id.required' => 'Program Training Wajib Diisi!',
            'tanggal_mulai.required' => 'Tanggal Mulai Wajib Diisi!',
            'tanggal_mulai.date' => 'Format Tanggal Mulai Tidak Valid!',
            'tanggal_selesai.required' => 'Tanggal Selesai Wajib Diisi!',
            'tanggal_selesai.date' => 'Format Tanggal Selesai Tidak Valid!',
            'tanggal_selesai.after_or_equal' => 'Tanggal Selesai Harus Setelah atau Sama Dengan Tanggal Mulai!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $user = auth()->guard('admin')->user();
                $lamaran = Lamaran::where('id', $request->lamaran_id)->first();

                $data = new Training();
                $data->lamaran_id = $request->lamaran_id;
                $data->user_id = $lamaran->user_id;
                $data->program_id = $request->program_id;
                $data->tanggal_daftar = $request->tanggal_daftar;
                $data->tanggal_mulai = $request->tanggal_mulai;
                $data->tanggal_selesai = $request->tanggal_selesai;
                $data->status = $request->status;
                $data->nilai_akhir = $request->nilai_akhir;
                $data->sertifikat_diterbitkan = $request->sertifikat_diterbitkan;
                $data->nomor_sertifikat = $request->nomor_sertifikat;
                $data->catatan = $request->catatan;
                $data->didaftarkan_oleh = auth()->guard('admin')->user()->id;
                $data->save();

                $lamaran->status = 'pelatihan';
                $lamaran->save();

            }catch(\QueryException $e){
                dd($e);
                DB::rollback();
                return back();
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Training berhasil Disimpan',
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
        
        $data = Training::with(['user', 'program', 'staff' ])->where('id', $id)->first();
        
        return Inertia::render('Training/Show', [
            'data' => $data
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $data = Training::with(['user', 'program', 'staff'])->where('id', $id)->first();
        
        return Inertia::render('Training/Form', [
            'value' => $data,
            'editMode' => true
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
            'lamaran_id' => 'required',
            'program_id' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ];

        $pesan = [
            'lamaran_id.required' => 'Talent Wajib Diisi!',
            'program_id.required' => 'Program Training Wajib Diisi!',
            'tanggal_mulai.required' => 'Tanggal Mulai Wajib Diisi!',
            'tanggal_mulai.date' => 'Format Tanggal Mulai Tidak Valid!',
            'tanggal_selesai.required' => 'Tanggal Selesai Wajib Diisi!',
            'tanggal_selesai.date' => 'Format Tanggal Selesai Tidak Valid!',
            'tanggal_selesai.after_or_equal' => 'Tanggal Selesai Harus Setelah atau Sama Dengan Tanggal Mulai!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $lamaran = Lamaran::where('id', $request->lamaran_id)->first();

                $data = Training::where('id', $id)->first();
                $data->lamaran_id = $request->lamaran_id;
                $data->user_id = $lamaran->user_id;
                $data->program_id = $request->program_id;
                $data->tanggal_mulai = $request->tanggal_mulai;
                $data->tanggal_selesai = $request->tanggal_selesai;
                $data->status = $request->status;
                $data->nilai_akhir = $request->nilai_akhir;
                $data->sertifikat_diterbitkan = $request->sertifikat_diterbitkan;
                $data->nomor_sertifikat = $request->nomor_sertifikat;
                $data->catatan = $request->catatan;
                $data->save();

                $lamaran->status = 'pelatihan';
                $lamaran->save();


            }catch(\QueryException $e){
                dd($e);
                DB::rollback();
                return back();
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Training berhasil Disimpan',
            ]);
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
            $hapus_db = Training::destroy($id);
        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data training'
            ], 500);
        }
        DB::commit();
        return redirect()->route('admin.training.index');
    }

    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        $limit = ($request->limit) ? $request->limit : 25;
        $paging = !empty($request->page) ? true : false;

        $elq = Training::when($request->search, function($query, $search){
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%');
            });
        })
        ->with(['program', 'user'])
        ->orderBy($sort, $sortDir);
        
        if($paging){
            $data = $elq->paginate($limit);
        }else{
            $data = $elq->get();
        }

        return response()->json($data);
    }

    public function exportPdf(Request $request)
    {
        $query = Training::with(['user.detail', 'lamaran.lowongan', 'program', 'staff'])
            ->when($request->status, function($query, $status){
                $query->where('status', $status);
            })
            ->when($request->program_id, function($query, $program_id){
                $query->where('program_id', $program_id);
            })
            ->when($request->tanggal_dari, function($query, $tanggal_dari){
                $query->whereDate('tanggal_daftar', '>=', $tanggal_dari);
            })
            ->when($request->tanggal_sampai, function($query, $tanggal_sampai){
                $query->whereDate('tanggal_daftar', '<=', $tanggal_sampai);
            })
            ->when($request->tanggal_mulai_dari, function($query, $tanggal_mulai_dari){
                $query->whereDate('tanggal_mulai', '>=', $tanggal_mulai_dari);
            })
            ->when($request->tanggal_mulai_sampai, function($query, $tanggal_mulai_sampai){
                $query->whereDate('tanggal_mulai', '<=', $tanggal_mulai_sampai);
            })
            ->orderBy('tanggal_daftar', 'desc');

        $data = $query->get();

        // Generate filter information for the report
        $filterInfo = [];
        if ($request->status) {
            $filterInfo['Status'] = $this->getStatusLabel($request->status);
        }
        if ($request->program_id) {
            $filterInfo['Program ID'] = $request->program_id;
        }
        if ($request->tanggal_dari) {
            $filterInfo['Tanggal Daftar Dari'] = Carbon::parse($request->tanggal_dari)->format('d M Y');
        }
        if ($request->tanggal_sampai) {
            $filterInfo['Tanggal Daftar Sampai'] = Carbon::parse($request->tanggal_sampai)->format('d M Y');
        }
        if ($request->tanggal_mulai_dari) {
            $filterInfo['Tanggal Mulai Dari'] = Carbon::parse($request->tanggal_mulai_dari)->format('d M Y');
        }
        if ($request->tanggal_mulai_sampai) {
            $filterInfo['Tanggal Mulai Sampai'] = Carbon::parse($request->tanggal_mulai_sampai)->format('d M Y');
        }

        // Generate summary statistics
        $statistics = [
            'total' => $data->count(),
            'by_status' => $data->groupBy('status')->map->count()->toArray(),
            'by_program' => $data->groupBy('program.nama')->map->count()->toArray(),
        ];

        // Generate filename
        $filename = 'Laporan_Training_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';

        $pdf = PDF::loadView('pdf.training-report', [
            'data' => $data,
            'filterInfo' => $filterInfo,
            'statistics' => $statistics,
            'statusOptions' => $this->getStatusOptions(),
            'exportDate' => Carbon::now()
        ]);

        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($filename);
    }

    private function getStatusOptions()
    {
        return [
            'terdaftar' => 'Terdaftar',
            'sedang_pelatihan' => 'Sedang Pelatihan',
            'selesai' => 'Selesai',
            'mengundurkan_diri' => 'Mengundurkan Diri'
        ];
    }

    private function getStatusLabel($status)
    {
        $statusOptions = $this->getStatusOptions();
        return $statusOptions[$status] ?? $status;
    }

    
    public function hasil(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            $data = Training::where('id', $id)->first();
            $data->nilai_akhir = $request->nilai_akhir;
            $data->sertifikat_diterbitkan = $request->sertifikat_diterbitkan;
            $data->nomor_sertifikat = $request->nomor_sertifikat;
            $data->catatan = $request->catatan;
            $data->status = 'selesai';
            $data->save();

            $lamaran = Lamaran::where('id', $data->lamaran_id)->first();
            $lamaran->status = 'siap';
            $lamaran->save();

        }catch(\QueryException $e){
            dd($e);
            DB::rollback();
            return back();
        }
        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Training berhasil Disimpan',
        ]);
    }
}
