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


use App\Models\JadwalInterview;
use App\Models\User;


class JadwalInterviewController extends Controller
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
        return Inertia::render('JadwalInterview/Index');
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
        // dd($request->all());
        $rules = [
            'user_id' => 'required',
            'tanggal' => 'required',
            'waktu' => 'required',
            'lokasi' => 'required',
            'pewawancara_id' => 'required',
        ];

        $pesan = [
            'user_id.required' => 'Nama Talent Wajib Diisi!',
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
                
                $data = new JadwalInterview();
                $data->user_id = $request->user_id;
                $data->lokasi = $request->lokasi;
                $data->tanggal = $request->tanggal;
                $data->waktu = $request->waktu;
                $data->pewawancara_id = $request->pewawancara_id;
                $data->catatan = $request->catatan;
                $data->dibuat_oleh = auth()->guard('admin')->user()->id;
                $data->status = 'dijadwalkan';
                $data->save();

                $user = User::find($request->user_id);
                $user->status = 'interview';
                $user->save();

                $user->notify(new JadwalInterviewNotification($data));
                

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.interview.jadwal.show', $data->id);
        }
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data = JadwalInterview::with(['user', 'pewawancara', 'pembuat'])->where('id', $id)->first();

        return Inertia::render('JadwalInterview/Show', [
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
        $data = JadwalInterview::with(['user', 'pewawancara', 'pembuat'])->where('id', $id)->first();


        return Inertia::render('JadwalInterview/Form',[
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
            'tanggal' => 'required',
            'waktu' => 'required',
            'lokasi' => 'required',
            'pewawancara_id' => 'required',
        ];

        $pesan = [
            'user_id.required' => 'Nama Talent Wajib Diisi!',
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
                
                $data = JadwalInterview::where('id', $id)->first();
                $data->user_id = $request->user_id;
                $data->lokasi = $request->lokasi;
                $data->tanggal = $request->tanggal;
                $data->waktu = $request->waktu;
                $data->pewawancara_id = $request->pewawancara_id;
                $data->catatan = $request->catatan;
                $data->dibuat_oleh = auth()->guard('admin')->user()->id;
                $data->status = 'dijadwalkan';
                $data->save();

                
                $user = User::find($request->user_id);
                $user->status = 'interview';
                $user->save();

                $user->notify(new JadwalInterviewNotification($data));

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            // DB::commit();
            return redirect()->route('admin.interview.jadwal.show', $id);
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
            
            $pdk = JadwalInterview::find($id);
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
        
        $elq = JadwalInterview::with(['user', 'pewawancara', 'pembuat', 'hasil'])
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
