<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Storage;
use Illuminate\Support\Str;
use App\Models\Medical;
use App\Models\User;


class MedicalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = User::select('id', 'nama', 'status')->with(['medical'])
        ->where('id', auth()->guard('web')->user()->id)
        ->first();

        return Inertia::render('User/Medical',[
            'data' => $data
        ]);
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
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $user = User::where('id', auth()->guard('web')->user()->id)->first();
                $user_id = $user->id;

                $data = new Medical();
                $data->user_id = $user->id;
                $data->nama = $request->nama;
                $data->tanggal = $request->tanggal;
                $data->hasil = $request->hasil;
                
                if (is_file($request->file)) {
                    $fileDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('file')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($fileDir, fopen($request->file('file'), 'r+'));
                    $data->file = $fileDir;
                }
                $data->status = 'pending';
                $data->save();

                $user->status = 'medical';
                $user->save();


            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('user.medical.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $data = Paket::where('id', $id)->first();
        
        return Inertia::render('Paket/Form', [
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
            'user_id' => 'required',
            'program_id' => 'required',
            'tanggal' => 'required',
        ];

        $pesan = [
            'user_id.required' => 'Talent Wajib Diisi!',
            'program_id.required' => 'Program Training Wajib Diisi!',
            'tanggal.required' => 'Tanggal Training Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $data = Paket::where('id', $id)->first();
                $data->nama = $request->nama;
                $data->usia = $request->usia;
                $data->pembangunan = $request->pembangunan;
                $data->pendaftaran = $request->pendaftaran;
                $data->spp = $request->spp;
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.paket.index');
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
            $hapus_db = Paket::destroy($id);
        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }

        DB::commit();
        return redirect()->route('admin.paket.index');
    }

    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        $limit = ($request->limit) ? $request->limit : 25;
        $paging = !empty($request->page) ? true : false;

        $elq = Training::when($request->q, function($query, $search){
            $query->where('nama', 'LIKE', '%' . $search . '%')
            ->orWhere('usia', 'LIKE', '%' . $search . '%');
        })
        ->with(['program', 'talent'])
        ->orderBy($sort, $sortDir);
        
        if($paging){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }

        return response()->json($data);
    }
}
