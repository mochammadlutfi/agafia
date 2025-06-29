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
        
        $data = Medical::with(['user'])->where('id', $id)->first();
        
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
            'nama.required' => 'Nama Pemeriksaan Wajib Diisi!',
            'tanggal.required' => 'Tanggal Wajib Diisi!',
            'tanggal.date' => 'Format Tanggal Salah!',
            'hasil.required' => 'Hasil Wajib Diisi!',
            'status.required' => 'Status Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $data = Medical::where('id', $id)->first();
                $data->nama = $request->nama;
                $data->tanggal = $request->tanggal;
                $data->hasil = $request->hasil;
                $data->status = $request->status;
                $data->catatan = $request->catatan;
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.medical.show', $id);
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
            $hapus_db = Medical::destroy($id);
        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }

        DB::commit();
        return redirect()->route('admin.medical.index');
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

        $elq = Medical::when($request->q, function($query, $search){
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
