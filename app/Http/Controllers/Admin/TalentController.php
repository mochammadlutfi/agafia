<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Image;
use Intervention\Image\ImageManagerStatic;
use Storage;
use App\Notifications\RegisterNotification;
use App\Models\User;

class TalentController extends Controller
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
        return Inertia::render('Talent/Index');
    }
    
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data = User::with(['detail'])
        ->where('id', $id)->first();
        
        // dd($data);
        return Inertia::render('Talent/Show', [
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

        $data = User::with(['detail'])
        ->where('id', $id)->first();

        return Inertia::render('Talent/Form',[
            'editMode' => true,
            'value' => $value
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
            'nama' => 'required',
            'username' => 'required',
            'jk' => 'required',
            'tmp_lahir' => 'required',
            'tgl_lahir' => 'required',
            'alamat' => 'required',
            'jarak' => 'required',
            'sosialisasi_dengan_lingkungan' => 'required',
        ];

        $pesan = [
            'nama.required' => 'Nama Lengkap Wajib Diisi!',
            'username.required' => 'Nama Panggilan Wajib Diisi!',
            'jk.required' => 'Jenis Kelamin Wajib Diisi!',
            'tmp_lahir.required' => 'Tempat Lahir Wajib Diisi!',
            'tgl_lahir.required' => 'Tanggal Lahir Wajib Diisi!',
            'alamat.required' => 'Alamat Lengkap Wajib Diisi!',
            'jarak.required' => 'Jarak Rumah Wajib Diisi!',
            'sosialisasi_dengan_lingkungan.required' => 'Sosialisasi dengan lingkungan Ke Wajib Diisi!',
        ];

        $validator =  Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $data = Anak::where('id', $id)->first();
                $data->username = $request->username;
                $data->nama = $request->nama;
                $data->jk = $request->jk;
                $data->tgl_lahir = Carbon::parse($request->tgl_lahir)->format('Y-m-d');
                $data->tmp_lahir = $request->tmp_lahir;
                $data->alamat = $request->alamat;
                $data->anak_ke = $request->anak_ke;
                $data->jarak = $request->jarak;
                $data->sosialisasi_dengan_lingkungan = $request->sosialisasi_dengan_lingkungan;
                $data->sakit_yang_pernah_diderita = $request->sakit_yang_pernah_diderita;
                $data->makanan_yang_disukai = $request->makanan_yang_disukai;
                $data->makanan_yang_tidak_disukai = $request->makanan_yang_tidak_disukai;
                $data->alergi = $request->alergi;
                $data->scan_akte = $request->scan_akte;
                $data->isAntarJemput = $request->isAntarJemput;
                $data->isLaundry = $request->isLaundry;
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.talent.show', $id);
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
            
            $pdk = Pendukung::find($id);
            $pdk->delete();

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'fail' => true,
                'errors' => $e,
                'pesan' => 'Error Menhapus Data Pendukung',
            ]);
        }

        DB::commit();
        return redirect()->route('admin.talent.index');

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
        
        $elq = User::with(['detail'])
        ->when($request->q, function($query, $search){
            $query->where('nama', 'LIKE', '%' . $search . '%');
        })
        ->when($request->status, function($query, $status){
            $query->where('status', $status);
        })
        ->orderBy($sort, $sortDir);

        if($request->limit){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }

        return response()->json($data);
    }

    public function state($id, Request $request)
    {
        // dd($request->all());

        $user = User::find($id);
        if($request->state == 'diterima'){
            $user->status = 'diterima';
        }else{
            $user->status = 'ditolak';
            $user->detail->update([
                'catatan' => $request->reason,
            ]);
        }
        $user->save();
        $user->notify(new RegisterNotification($request->state, $request->reason));

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
        ]);
    }
}
