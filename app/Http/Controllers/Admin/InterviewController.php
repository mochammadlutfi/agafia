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
use App\Notifications\JadwalInterviewNotification;


use App\Models\Interview;
use App\Models\Lamaran;
use App\Models\User;


class InterviewController extends Controller
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
        return Inertia::render('Interview/Index');
    }
    
    public function create(Request $request)
    {
        return Inertia::render('JadwalInterview/Form', [
            'editMode' => false,
            'value' => null
        ]);
    }
    
    public function store(Request $request)
    {
        $rules = [
            'tanggal' => 'required',
            'waktu' => 'required',
            'lokasi' => 'required'
        ];

        $pesan = [
            'tanggal.required' => 'Tanggal Interview Wajib Diisi!',
            'waktu.required' => 'Waktu Interview Wajib Diisi!',
            'lokasi.required' => 'Lokasi Interview Wajib Diisi!',
            'pewawancara_id.required' => 'Pewawancara Wajib Diisi!',
        ];

        $validator =  Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{

                $lamaran = Lamaran::where('id', $request->lamaran_id)->first();
                $user = User::where('id', $lamaran->user_id)->first();
                
                $data = new Interview();
                $data->user_id = $lamaran->user_id;
                $data->lamaran_id = $request->lamaran_id;
                $data->lokasi = $request->lokasi;
                $data->tanggal = $request->tanggal;
                $data->waktu = $request->waktu;
                $data->pewawancara_id = 2;
                $data->catatan = $request->catatan;
                $data->status = 'dijadwalkan';
                $data->save();

                $lamaran->status = 'interview';
                $lamaran->save();

                $user->notify(new JadwalInterviewNotification($data));
                

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil Dibuat',
            ]);
        }
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data = Interview::with(['lamaran' => function($q){
            return $q->with(['user', 'lowongan']);
        }, 'pewawancara'])->where('id', $id)->first();

        return Inertia::render('Interview/Show', [
            'data' => $data,
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
            'tanggal' => 'required',
            'waktu' => 'required',
            'lokasi' => 'required',
        ];

        $pesan = [
            'tanggal.required' => 'Tanggal Interview Wajib Diisi!',
            'waktu.required' => 'Waktu Interview Wajib Diisi!',
            'lokasi.required' => 'Lokasi Interview Wajib Diisi!',
            'pewawancara_id.required' => 'Pewawancara Wajib Diisi!',
        ];

        $validator =  Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{

                
                $lamaran = Lamaran::where('id', $request->lamaran_id)->first();
                $user = User::where('id', $lamaran->user_id)->first();
                
                $data = Interview::where('id', $id)->first();
                $data->user_id = $lamaran->user_id;
                $data->lamaran_id = $request->lamaran_id;
                $data->lokasi = $request->lokasi;
                $data->tanggal = $request->tanggal;
                $data->waktu = $request->waktu;
                $data->pewawancara_id = 2;
                $data->catatan = $request->catatan;
                $data->status = $request->status;
                $data->save();

                $lamaran->status = 'interview';
                $lamaran->save();
                
                if($data->status == 'dijadwalkan'){
                    $user->notify(new JadwalInterviewNotification($data));
                }

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil Dibuat',
            ]);
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
            
            $data = Interview::find($id);

            if($data->status == 'dijadwalkan'){
                $lamaran = Lamaran::where('id', $data->lamaran_id)->first();
                $lamaran->status = 'diterima';
                $lamaran->save();
            }
            $data->delete();

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'fail' => true,
                'errors' => $e,
                'pesan' => 'Error Menhapus Data',
            ]);
        }

        DB::commit();
        return redirect()->route('admin.interview.index');

    }

    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        
        $elq = Interview::with(['pewawancara', 'lamaran' => function($q){
            $q->with(['user', 'lowongan']);
        }])
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

    
    
    public function hasil($id, Request $request)
    {
        DB::beginTransaction();
        try{
            
            $data = Interview::where('id', $id)->first();
            $data->skor_interview = $request->skor_interview;
            $data->skor_psikotes = $request->skor_psikotes;
            $data->kemampuan_komunikasi = $request->kemampuan_komunikasi;
            $data->kemampuan_teknis = $request->kemampuan_teknis;
            $data->kepribadian = $request->kepribadian;
            $data->rekomendasi = $request->rekomendasi;
            $data->catatan_hasil = $request->catatan;
            $data->status = 'selesai';
            $data->save();

            $lamaran = Lamaran::where('id', $data->lamaran_id)->first();
            $lamaran->status = 'medical';
            $lamaran->save();

        }catch(\QueryException $e){
            DB::rollback();
            dd($e);
        }

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil Dibuat',
        ]);
    }
}
