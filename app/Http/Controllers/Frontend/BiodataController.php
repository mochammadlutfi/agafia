<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;
use Storage;
use App\Models\User;
use App\Models\UserDetail;

class BiodataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->guard('web')->user();

        $data = User::with(['detail'])->where('id', $user->id)->first();

        if (!$data->detail) {
            return redirect()->route('user.biodata.form');
        }
        
        if ($data->status == 'ditolak') {
            return redirect()->route('user.biodata.form');
        }

        $checking = $user->status == 'pending' ? true : false;

        return Inertia::render('User/Biodata/Index',[
            'data' => $data,
            'checking' => $checking,
        ]);
    }

    
    public function form()
    {
        $user = auth()->guard('web')->user();

        $data = User::with(['detail'])->where('id', $user->id)->first();

        return Inertia::render('User/Biodata/Form',[
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
            'nik' => 'required',
            'nama' => 'required',
            'jenis_kelamin' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'phone' => 'required',
            'alamat' => 'required',
            'nama_izin' => 'required',
            'status_izin' => 'required',
            'nama_ayah' => 'required',
            'nama_ibu' => 'required',
            'alamat_ortu' => 'required',
            'phone_ortu' => 'required',
            'pekerjaan' => 'required',
            'negara_tujuan' => 'required',
        ];

        $pesan = [
            'nik.required' => 'NIK Wajib Diisi!',
            'nama.required' => 'Nama Lengkap Wajib Diisi!',
            'jenis_kelamin.required' => 'Jenis Kelamin Wajib Diisi!',
            'tempat_lahir.required' => 'Tempat/Tanggal Lahir Wajib Diisi!',
            'tanggal_lahir.required' => 'Tempat/Tanggal Lahir Wajib Diisi!',
            'phone.required' => 'No Handphone Wajib Diisi!',
            'alamat.required' => 'Alamat Wajib Diisi!',
            'nama_izin.required' => 'Nama Pemberi Izin Wajib Diisi!',
            'status_izin.required' => 'Status Pemberi Izin Wajib Diisi!',
            'nama_ayah.required' => 'Nama Ayah Wajib Diisi!',
            'nama_ibu.required' => 'Nama Ibu Wajib Diisi!',
            'alamat_ortu.required' => 'Alamat Orang Tua Wajib Diisi!',
            'phone_ortu.required' => 'No Handphone Orang Tua Wajib Diisi!',
            'pekerjaan.required' => 'Pekerjaan yang di inginkan Wajib Diisi!',
            'negara_tujuan.required' => 'Negara Tujuan Wajib Diisi!'
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $user = auth()->guard('web')->user();
                $data = UserDetail::firstOrNew(['user_id' =>  $user->id]);

                $data->nik = $request->nik;
                $data->nama = $request->nama;
                $data->jenis_kelamin = $request->jenis_kelamin;
                $data->tempat_lahir = $request->tempat_lahir;
                $data->tanggal_lahir = $request->tanggal_lahir;
                $data->phone = $request->phone;
                $data->alamat = $request->alamat;
                $data->nama_izin = $request->nama_izin;
                $data->status_izin = $request->status_izin;
                $data->nama_ayah = $request->nama_ayah;
                $data->nama_ibu = $request->nama_ibu;
                $data->alamat_ortu = $request->alamat_ortu;
                $data->phone_ortu = $request->phone_ortu;
                $data->pendidikan = json_encode($request->pendidikan);
                $data->pengalaman = json_encode($request->pengalaman);
                $data->pekerjaan = $request->pekerjaan;
                $data->negara_tujuan = $request->negara_tujuan;
                $data->status = 'draft';
                
                if (is_file($request->ktp)) {
                    $ktpDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('ktp')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($ktpDir, fopen($request->file('ktp'), 'r+'));
                    $data->ktp = $ktpDir;
                }

                if (is_file($request->kk)) {
                    $kkDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('kk')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($kkDir, fopen($request->file('kk'), 'r+'));
                    $data->kk = $kkDir;
                }

                if (is_file($request->akte_lahir)) {
                    $akteDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('akte_lahir')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($akteDir, fopen($request->file('akte_lahir'), 'r+'));
                    $data->akte_lahir = $akteDir;

                }

                if (is_file($request->buku_nikah)) {
                    $bukuNikahDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('buku_nikah')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($bukuNikahDir, fopen($request->file('buku_nikah'), 'r+'));
                    $data->buku_nikah = $bukuNikahDir;

                }

                if (is_file($request->keterangan_sehat)) {
                    $keteranganSehatDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('keterangan_sehat')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($keteranganSehatDir, fopen($request->file('keterangan_sehat'), 'r+'));
                    $data->surat_keterangan_sehat = $keteranganSehatDir;
                }
                if (is_file($request->izin_keluarga)) {
                    $izinKeluargaDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('izin_keluarga')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($izinKeluargaDir, fopen($request->file('izin_keluarga'), 'r+'));
                    $data->surat_izin_keluarga = $izinKeluargaDir;
                }

                if (is_file($request->ijazah)) {
                    $ijazahDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('ijazah')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($ijazahDir, fopen($request->file('ijazah'), 'r+'));
                    $data->ijazah = $ijazahDir;
                }

                if (is_file($request->kompetensi)) {
                    $kompetensiDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('kompetensi')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($kompetensiDir, fopen($request->file('kompetensi'), 'r+'));
                    $data->kompetensi = $kompetensiDir;
                }

                if (is_file($request->paspor)) {
                    $pasporDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('paspor')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($pasporDir, fopen($request->file('paspor'), 'r+'));
                    $data->paspor = $pasporDir;
                }
                if (is_file($request->pengalaman_kerja)) {
                    $pengalaman_kerjaDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('pengalaman_kerja')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($pengalaman_kerjaDir, fopen($request->file('pengalaman_kerja'), 'r+'));
                    $data->surat_pengalaman_kerja = $pengalaman_kerjaDir;
                }

                if (is_file($request->skck)) {
                    $skckDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('skck')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($skckDir, fopen($request->file('skck'), 'r+'));
                    $data->skck = $skckDir;
                }


                if (is_file($request->foto)) {
                    $fotoDir = 'document/' . $user_id .'/' . Str::random(32) . '.' . $request->file('foto')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($fotoDir, fopen($request->file('foto'), 'r+'));
                    $data->foto = $fotoDir;
                }
                $data->save();

                $user->status = 'pending';
                $user->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('user.biodata.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
