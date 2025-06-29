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
            'nama' => 'required',
            'nik' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'phone' => 'required',
            'nama_izin' => 'required',
            'status_izin' => 'required',
            'nama_ayah' => 'required',
        ];

        $pesan = [
            'nama.required' => 'Nama Lengkap Wajib Diisi!',
            'nik.required' => 'NIK Wajib Diisi!',
            'tempat_lahir.required' => 'Tempat Lahir Wajib Diisi!',
            'tanggal_lahir.required' => 'Tanggal Lahir Wajib Diisi!',
            'tanggal_lahir.date' => 'Format Tanggal Lahir Tidak Valid!',
            'jenis_kelamin.required' => 'Jenis Kelamin Wajib Diisi!',
            'alamat.required' => 'Alamat Wajib Diisi!',
            'phone.required' => 'Nomor Telepon Wajib Diisi!',
            'nama_izin.required' => 'Nama Pemberi Izin Wajib Diisi!',
            'status_izin.required' => 'Status Izin Wajib Diisi!',
            'nama_ayah.required' => 'Nama Ayah Wajib Diisi!',
        ];

        $validator =  Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $user = User::where('id', $id)->first();
                $user->nama = $request->nama;
                $user->save();

                $detail = $user->detail;
                if (!$detail) {
                    $detail = new UserDetail();
                    $detail->user_id = $user->id;
                }
                $detail->fill($request->all());
                $detail->save();

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
            
            $user = User::find($id);
            $user->delete();

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'fail' => true,
                'errors' => $e,
                'pesan' => 'Error Menghapus Data',
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
