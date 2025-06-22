<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Medical;


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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $data = Medical::with(['talent'])->where('id', $id)->first();
        
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
        // dd($request->all());
        $rules = [
            'nama' => 'required',
            'usia' => 'required',
            'pembangunan' => 'required',
            'pendaftaran' => 'required',
            'spp' => 'required',
        ];

        $pesan = [
            'nama.required' => 'Nama Paket Wajib Diisi!',
            'usia.required' => 'Usia Wajib Diisi!',
            'pembangunan.required' => 'Biaya Pembangunan Wajib Diisi!',
            'pendaftaran.required' => 'Biaya Pendaftaran Wajib Diisi!',
            'spp.required' => 'Biaya SPP Bulanan Wajib Diisi!',
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

    public function state($id, Request $request)
    {

        $data = Medical::with(['talent'])->where('id', $id)->first();

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

        $elq = Medical::when($request->q, function($query, $search){
            $query->where('nama', 'LIKE', '%' . $search . '%');
        })
        ->with(['talent'])
        ->orderBy($sort, $sortDir);
        
        if($paging){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }

        return response()->json($data);
    }
}
