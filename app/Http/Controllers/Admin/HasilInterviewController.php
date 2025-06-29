<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Storage;

use App\Models\JadwalInterview;
use App\Models\HasilInterview;


class HasilInterviewController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void 
     */
    public function __construct() {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        return Inertia::render('HasilInterview/Index');
    }
    
    public function create(Request $request)
    {
        return Inertia::render('HasilInterview/Form', [
            'editMode' => false,
            'value' => null
        ]);
    }
    
    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            'user_id' => 'required',
            'kemampuan_komunikasi' => 'required',
            'kemampuan_teknis' => 'required',
            'penilaian_kepribadian' => 'required',
            'rekomendasi' => 'required',
            'skor_interview' => 'required',
            'skor_psikotes' => 'required',
        ];

        $pesan = [
            'user_id.required' => 'Nama Talent Wajib Diisi!',
            'kemampuan_komunikasi.required' => 'Kemampuan Komunikasi Wajib Diisi!',
            'kemampuan_teknis.required' => 'Kemampuan Teknis Wajib Diisi!',
            'penilaian_kepribadian.required' => 'Kepribadian Wajib Diisi!',
            'skor_interview.required' => 'Nilai Interview Wajib Diisi!',
            'skor_psikotes.required' => 'Nilai Psikotes Wajib Diisi!',
        ];

        $validator =  Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                if(!$request->jadwal_id){
                    $jadwal = JadwalInterview::where('user_id', $request->user_id)
                        ->where('status', 'dijadwalkan')
                        ->first();
                }else{
                    $jadwal = JadwalInterview::where('id', $request->jadwal_id)->first();
                }

                $data = new HasilInterview();
                $data->user_id = $request->user_id;
                $data->jadwal_id = $jadwal->id;
                $data->skor_interview = $request->skor_interview;
                $data->skor_psikotes = $request->skor_psikotes;
                $data->kemampuan_komunikasi = $request->kemampuan_komunikasi;
                $data->kemampuan_teknis = $request->kemampuan_teknis;
                $data->penilaian_kepribadian = $request->penilaian_kepribadian;
                $data->rekomendasi = $request->rekomendasi;
                $data->catatan = $request->catatan;
                $data->dinilai_oleh = auth()->guard('admin')->user()->id;
                $data->tanggal_penilaian = Carbon::now();
                $jadwal->hasil()->save($data);
                
                $jadwal->status = 'selesai';
                $jadwal->save();
            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.interview.jadwal.show', $data->jadwal_id);
        }
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data = HasilInterview::with(['user', 'jadwal', 'penilai'])->where('id', $id)->first();

        return Inertia::render('HasilInterview/Show', [
            'data' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data = HasilInterview::with(['user', 'jadwal', 'penilai'])->where('id', $id)->first();


        return Inertia::render('HasilInterview/Form',[
            'editMode' => true,
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'user_id' => 'required',
            'kemampuan_komunikasi' => 'required',
            'kemampuan_teknis' => 'required',
            'penilaian_kepribadian' => 'required',
            'rekomendasi' => 'required',
            'skor_interview' => 'required',
            'skor_psikotes' => 'required',
        ];

        $pesan = [
            'user_id.required' => 'Nama Talent Wajib Diisi!',
            'kemampuan_komunikasi.required' => 'Kemampuan Komunikasi Wajib Diisi!',
            'kemampuan_teknis.required' => 'Kemampuan Teknis Wajib Diisi!',
            'penilaian_kepribadian.required' => 'Kepribadian Wajib Diisi!',
            'skor_interview.required' => 'Nilai Interview Wajib Diisi!',
            'skor_psikotes.required' => 'Nilai Psikotes Wajib Diisi!',
        ];

        $validator =  Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $data = HasilInterview::where('id', $id)->first();
                $data->user_id = $request->user_id;
                $data->skor_interview = $request->skor_interview;
                $data->skor_psikotes = $request->skor_psikotes;
                $data->kemampuan_komunikasi = $request->kemampuan_komunikasi;
                $data->kemampuan_teknis = $request->kemampuan_teknis;
                $data->penilaian_kepribadian = $request->penilaian_kepribadian;
                $data->rekomendasi = $request->rekomendasi;
                $data->catatan = $request->catatan;
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.interview.hasil.show', $id);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            
            $pdk = HasilInterview::find($id);
            $pdk->delete();

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'fail' => true,
                'errors' => $e,
                'pesan' => 'Error Menhapus Data',
            ]);
        }

        DB::commit();
        return redirect()->route('admin.interview.jadwal.index');

    }

    public function report(Request $request)
    {
        $kelompok = $request->kelompok;
        $paket = $request->paket;

        return Excel::download(new AnakExport($kelompok, $paket), 'Data Anak.xlsx');
    }

    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        
        $elq = HasilInterview::with(['user', 'jadwal', 'penilai', 'penyetuju'])
        ->when($request->q, function($query, $search){
            $query->where('nama', 'LIKE', '%' . $search . '%');
        })
        ->orderBy($sort, $sortDir);

        if($request->limit){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }

        return response()->json($data);
    }
}
