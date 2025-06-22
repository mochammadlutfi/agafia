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

use App\Models\User;

class PenggunaController extends Controller
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
        return Inertia::render('Pengguna/Index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return Inertia::render('Pengguna/Form');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = $this->validate($request->all());
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $data = new Dukungan();
                $data->nik = $request->nik;
                $data->nama = $request->nama;
                $data->jk = ($request->jk == 'Laki-Laki') ? 'L' : 'P';
                $data->tgl_lahir = Carbon::parse($request->tglLahir)->format('Y-m-d');
                $data->tmp_lahir = $request->tmpLahir;
                $data->alamat = $request->alamat;
                $data->rt = $request->rt;
                $data->rw = $request->rw;
                $data->tps = $request->tps;
                $data->kota_id = $request->kota_id;
                $data->kecamatan_id = $request->kecamatan_id;
                $data->kelurahan_id = $request->kelurahan_id;
                $data->email = $request->email;
                $data->phone = $request->phone;
                $data->user_id = $request->user_id;

                if(!empty($request->file('image'))){
                    $data->image = $this->uploadImage($request->file('image'), str_replace(".", "", $request->kota_id));
                }
                
                if(!empty($request->file('ktp'))){
                    $data->ktp = $this->uploadImage($request->file('ktp'), str_replace(".", "", $request->kota_id), 'ktp');
                }

                $data->save();

                $saksi = new Saksi();
                $saksi->username = $request->username;
                $saksi->password = Hash::make($request->password);
                $data->saksi()->save($saksi);

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.saksi.index');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {

        $data = User::with(['detail'])->where('id', $id)->first();

        return Inertia::render('Pengguna/Show', [
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
        $value = Saksi::with(['kota', 'kecamatan', 'kelurahan'])
        ->where('id', $id)->first();


        return Inertia::render('Pengguna/Form',[
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
        // dd($request->all());
        $validator = $this->validate($request->all(), true);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $data = User::where('id', $id)->first();
                $data->nik = $request->nik;
                $data->nama = $request->nama;
                $data->jk = ($request->jk == 'Laki-Laki') ? 'L' : 'P';
                $data->tgl_lahir = Carbon::parse($request->tglLahir)->format('Y-m-d');
                $data->tmp_lahir = $request->tmpLahir;
                $data->alamat = $request->alamat;
                $data->rt = $request->rt;
                $data->rw = $request->rw;
                $data->tps = $request->tps;
                $data->kota_id = $request->kota_id;
                $data->kecamatan_id = $request->kecamatan_id;
                $data->kelurahan_id = $request->kelurahan_id;
                $data->email = $request->email;
                $data->phone = $request->phone;
                $data->username = $request->username;
                $data->password = Hash::make($request->password);

                // $tps = TPS::where('id', $data->tps_id)->first();
                // $tps->nomor = $request->tps_nomor;
                // $tps->kota_id = $request->tps_kota_id;
                // $tps->kec_id = $request->tps_kec_id;
                // $tps->kel_id = $request->tps_kel_id;
                // $tps->alamat = $request->tps_alamat;
                // $tps->rt = $request->tps_rt;
                // $tps->rw = $request->tps_rw;
                // $tps->save();

                if(!empty($request->file('image'))){
                    if($data->image){

                    }

                    $data->image = $this->uploadImage($request->file('image'), str_replace(".", "", $request->kota_id));
                }
                
                if(!empty($request->file('ktp'))){
                    $data->ktp = $this->uploadImage($request->file('ktp'), str_replace(".", "", $request->kota_id), 'ktp');
                }

                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('saksi.show', $id);
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
            
            $pdk = User::find($id);
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
        return redirect()->route('admin.pengguna.index');

    }

    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        $limit = ($request->limit) ? $request->limit : 25;

        $data = User::when($request->q, function($query, $search){
            $query->where('nama', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('id', 'ASC')->paginate($limit);

        return response()->json($data);
    }

    private function uploadImage($file, $kota_id , $type = 'avatar'){

        $file_name = $kota_id. '-' . uniqid() . '.' . $file->getClientOriginalExtension();

        $imgFile = Image::make($file->getRealPath());
        if($type == 'avatar'){
            $destinationPath = storage_path('app/public/'.$kota_id.'/saksi');
            $return = '/uploads/'.$kota_id.'/Pengguna/'.$file_name;
        }else{
            $destinationPath = storage_path('app/public/'.$kota_id.'/ktp');
            $return = '/uploads/'.$kota_id.'/ktp/'.$file_name;
        }
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 755, true);
        }

        if($type == 'avatar'){
            $width = 800;
            $heigth = 800;
        }else{
            $width = 1200;
            $heigth = 337;
        }

        $imgFile->resize($width, $heigth, function ($constraint) {
		    $constraint->aspectRatio();
		})->save($destinationPath.'/'.$file_name, 90);

        return $return;
    }

    private function validate($data, $editMode = false){
        
        $rules = [
            'nama' => 'required',
            'ktp' => 'required',
            'jk' => 'required',
            'tmpLahir' => 'required',
            'tglLahir' => 'required',
            'alamat' => 'required',
            'rt' => 'required',
            'rw' => 'required',
            'kota_id' => 'required',
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
            'tps' => 'required',
        ];

        $pesan = [
            'nama.required' => 'Nama Lengkap Wajib Diisi!',
            'ktp.required' => 'KTP Wajib Diisi!',
            'jk.required' => 'Jenis Kelamin Wajib Diisi!',
            'tmpLahir.required' => 'Tempat Lahir Wajib Diisi!',
            'tglLahir.required' => 'Tanggal Lahir Wajib Diisi!',
            'alamat.required' => 'Alamat Lengkap Wajib Diisi!',
            'rt.required' => 'RT Wajib Diisi!',
            'rw.required' => 'RW Wajib Diisi!',
            'kota_id.required' => 'Kota Wajib Diisi!',
            'kecamatan_id.required' => 'Kecamatan Wajib Diisi!',
            'kelurahan_id.required' => 'Desa/Kelurahan Wajib Diisi!',
            'tps.required' => 'TPS Wajib Diisi!',
            // 'tps_alamat.required' => 'Alamat TPS Lengkap Wajib Diisi!',
            // 'tps_kota_id.required' => 'Kota TPS Wajib Diisi!',
            // 'tps_kec_id.required' => 'Kecamatan TPS Wajib Diisi!',
            // 'tps_kel_id.required' => 'Desa/Kelurahan TPS Wajib Diisi!',
            // 'tps_rt.required' => 'RT Wajib Diisi!',
            // 'tps_rw.required' => 'RW Wajib Diisi!',
            // 'email.required' => 'Alamat Email Wajib Diisi!',
            // 'email.unique' => 'Alamat Email Sudah Digunakan!',
        ];
        

        if(!$editMode){
            $rules['username'] = 'required|unique:saksi,username';
            $rules['password'] = 'required';

            $pesan['username.required'] = 'Username Wajib Diisi!';
            $pesan['username.unique'] = 'Username Sudah Digunakan!';
            $pesan['password.required'] = 'Password Wajib Diisi!';
        }

        return Validator::make($data, $rules, $pesan);
    }
}
