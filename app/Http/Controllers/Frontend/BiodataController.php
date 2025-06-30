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
use Barryvdh\DomPDF\Facade\Pdf;

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
            'data' => $data,
            'editMode' => false
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
            // Required fields
            'nik' => 'required|string|max:16',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'phone' => 'required|string|max:15',
            'alamat' => 'required|string',
            'pekerjaan' => 'required|string|max:255',
            'negara_tujuan' => 'required|string|max:100',
            
            // Optional personal info
            'agama' => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|in:lajang,menikah,cerai,janda,duda',
            'tinggi_badan' => 'nullable|integer|min:100|max:250',
            'berat_badan' => 'nullable|integer|min:30|max:200',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'email_alternatif' => 'nullable|email|max:255',
            
            // Address fields
            'alamat_domisili' => 'nullable|string',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            
            // Emergency contact
            'kontak_darurat_nama' => 'nullable|string|max:255',
            'kontak_darurat_phone' => 'nullable|string|max:15',
            'kontak_darurat_hubungan' => 'nullable|string|max:100',
            'kontak_darurat_alamat' => 'nullable|string',
            
            // Family
            'nama_izin' => 'nullable|string|max:255',
            'status_izin' => 'nullable|string|max:100',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'alamat_ortu' => 'nullable|string',
            'phone_ortu' => 'nullable|string|max:15',
            
            // Professional
            'objektif_karir' => 'nullable|string',
            'ringkasan_profil' => 'nullable|string',
            'keahlian_khusus' => 'nullable|string',
            'hobi' => 'nullable|string|max:500',
            'motto_hidup' => 'nullable|string|max:500',
            
            // Health
            'kondisi_kesehatan' => 'nullable|string',
            'medical_checkup_terakhir' => 'nullable|date',
            
            // Arrays
            'pendidikan' => 'required|array|min:1',
            'pengalaman' => 'required|array|min:1',
        ];

        $pesan = [
            // Required fields
            'nik.required' => 'NIK Wajib Diisi!',
            'nik.max' => 'NIK maksimal 16 karakter!',
            'nama.required' => 'Nama Lengkap Wajib Diisi!',
            'jenis_kelamin.required' => 'Jenis Kelamin Wajib Diisi!',
            'jenis_kelamin.in' => 'Jenis Kelamin harus Laki-Laki atau Perempuan!',
            'tempat_lahir.required' => 'Tempat Lahir Wajib Diisi!',
            'tanggal_lahir.required' => 'Tanggal Lahir Wajib Diisi!',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid!',
            'phone.required' => 'No Handphone Wajib Diisi!',
            'alamat.required' => 'Alamat Wajib Diisi!',
            'pekerjaan.required' => 'Pekerjaan yang diinginkan Wajib Diisi!',
            'negara_tujuan.required' => 'Negara Tujuan Wajib Diisi!',
            
            // Validation messages for optional fields
            'email_alternatif.email' => 'Format email alternatif tidak valid!',
            'tinggi_badan.min' => 'Tinggi badan minimal 100 cm!',
            'tinggi_badan.max' => 'Tinggi badan maksimal 250 cm!',
            'berat_badan.min' => 'Berat badan minimal 30 kg!',
            'berat_badan.max' => 'Berat badan maksimal 200 kg!',
            'status_perkawinan.in' => 'Status perkawinan tidak valid!',
            'golongan_darah.in' => 'Golongan darah harus A, B, AB, atau O!',
            'medical_checkup_terakhir.date' => 'Format tanggal medical check up tidak valid!',
            
            // Array validation
            'pendidikan.required' => 'Data pendidikan wajib diisi!',
            'pendidikan.min' => 'Minimal 1 data pendidikan harus diisi!',
            'pengalaman.required' => 'Data pengalaman kerja wajib diisi!',
            'pengalaman.min' => 'Minimal 1 data pengalaman kerja harus diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $user = auth()->guard('web')->user();
                $data = UserDetail::firstOrNew(['user_id' => $user->id]);

                // Personal Information
                $data->nik = $request->nik;
                $data->nama = $request->nama;
                $data->jenis_kelamin = $request->jenis_kelamin;
                $data->agama = $request->agama;
                $data->tempat_lahir = $request->tempat_lahir;
                $data->tanggal_lahir = $request->tanggal_lahir;
                $data->status_perkawinan = $request->status_perkawinan;
                $data->tinggi_badan = $request->tinggi_badan;
                $data->berat_badan = $request->berat_badan;
                $data->golongan_darah = $request->golongan_darah;
                $data->phone = $request->phone;
                $data->email_alternatif = $request->email_alternatif;
                
                // Address Information
                $data->alamat = $request->alamat;
                $data->alamat_domisili = $request->alamat_domisili;
                $data->kecamatan = $request->kecamatan;
                $data->kabupaten_kota = $request->kabupaten_kota;
                $data->provinsi = $request->provinsi;
                $data->kode_pos = $request->kode_pos;
                
                // Emergency Contact
                $data->kontak_darurat_nama = $request->kontak_darurat_nama;
                $data->kontak_darurat_phone = $request->kontak_darurat_phone;
                $data->kontak_darurat_hubungan = $request->kontak_darurat_hubungan;
                $data->kontak_darurat_alamat = $request->kontak_darurat_alamat;
                
                // Family Information
                // $data->nama_izin = $request->nama_izin;
                // $data->status_izin = $request->status_izin;
                // $data->nama_ayah = $request->nama_ayah;
                // $data->nama_ibu = $request->nama_ibu;
                // $data->alamat_ortu = $request->alamat_ortu;
                // $data->phone_ortu = $request->phone_ortu;
                
                // Education & Experience (JSON arrays)
                $data->pendidikan = $request->pendidikan; // Will be auto-cast to JSON
                $data->pengalaman = $request->pengalaman; // Will be auto-cast to JSON
                
                // Career Information
                $data->pekerjaan = $request->pekerjaan;
                $data->negara_tujuan = $request->negara_tujuan;
                $data->objektif_karir = $request->objektif_karir;
                $data->ringkasan_profil = $request->ringkasan_profil;
                
                // Skills & Personal
                $data->keahlian_khusus = $request->keahlian_khusus;
                $data->hobi = $request->hobi;
                $data->motto_hidup = $request->motto_hidup;
                
                // Health Information
                $data->kondisi_kesehatan = $request->kondisi_kesehatan;
                $data->medical_checkup_terakhir = $request->medical_checkup_terakhir;
                
                // $data->status = 'draft';
                
                // Handle file uploads
                $userId = $user->id;
                
                // Profile photo
                if (is_file($request->foto)) {
                    $fotoDir = 'document/' . $userId . '/' . Str::random(32) . '.' . $request->file('foto')->getClientOriginalExtension();
                    Storage::disk('public')->put($fotoDir, fopen($request->file('foto'), 'r+'));
                    $data->foto = '/uploads/'.$fotoDir;
                }

                // Document files
                // $fileFields = [
                //     'ktp' => 'ktp',
                //     'kk' => 'kk', 
                //     'akte_lahir' => 'akte_lahir',
                //     'buku_nikah' => 'buku_nikah',
                //     'keterangan_sehat' => 'surat_keterangan_sehat',
                //     'izin_keluarga' => 'surat_izin_keluarga',
                //     'ijazah' => 'ijazah',
                //     'komptensi' => 'kompetensi',
                //     'paspor' => 'paspor',
                //     'pengalaman_kerja' => 'surat_pengalaman_kerja',
                //     'skck' => 'skck'
                // ];
                
                // foreach ($fileFields as $requestField => $dbField) {
                //     if (is_file($request->$requestField)) {
                //         $fileDir = 'document/' . $userId . '/' . Str::random(32) . '.' . $request->file($requestField)->getClientOriginalExtension();
                //         Storage::disk('public')->put($fileDir, fopen($request->file($requestField), 'r+'));
                //         $data->$dbField = $fileDir;
                //     }
                // }
                $data->save();

                // $user->status = 'pending';
                // $user->save();

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
    public function edit()
    {
        $user = auth()->guard('web')->user();
        $data = User::with(['detail'])->where('id', $user->id)->first();

        return Inertia::render('User/Biodata/Form', [
            'data' => $data,
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
    public function update(Request $request)
    {
        // Use the same validation as store but make some fields optional for updates
        $rules = [
            'nik' => 'required|string|max:16',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'phone' => 'required|string|max:15',
            'alamat' => 'required|string',
            
            // Make these optional for updates
            'pekerjaan' => 'nullable|string|max:255',
            'negara_tujuan' => 'nullable|string|max:100',
            'pendidikan' => 'nullable|array',
            'pengalaman' => 'nullable|array',
            
            // Optional fields (same as store)
            'agama' => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|in:lajang,menikah,cerai,janda,duda',
            'tinggi_badan' => 'nullable|integer|min:100|max:250',
            'berat_badan' => 'nullable|integer|min:30|max:200',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'email_alternatif' => 'nullable|email|max:255',
            'alamat_domisili' => 'nullable|string',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'kontak_darurat_nama' => 'nullable|string|max:255',
            'kontak_darurat_phone' => 'nullable|string|max:15',
            'kontak_darurat_hubungan' => 'nullable|string|max:100',
            'kontak_darurat_alamat' => 'nullable|string',
            'nama_izin' => 'nullable|string|max:255',
            'status_izin' => 'nullable|string|max:100',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'alamat_ortu' => 'nullable|string',
            'phone_ortu' => 'nullable|string|max:15',
            'objektif_karir' => 'nullable|string',
            'ringkasan_profil' => 'nullable|string',
            'keahlian_khusus' => 'nullable|string',
            'hobi' => 'nullable|string|max:500',
            'motto_hidup' => 'nullable|string|max:500',
            'kondisi_kesehatan' => 'nullable|string',
            'medical_checkup_terakhir' => 'nullable|date',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        DB::beginTransaction();
        try {
            $user = auth()->guard('web')->user();
            $data = UserDetail::where('user_id', $user->id)->firstOrFail();

            // Update all fillable fields using fill method
            $data->fill($request->only([
                'nik', 'nama', 'jenis_kelamin', 'agama', 'tempat_lahir', 'tanggal_lahir',
                'status_perkawinan', 'tinggi_badan', 'berat_badan', 'golongan_darah',
                'phone', 'email_alternatif', 'alamat', 'alamat_domisili', 'kecamatan',
                'kabupaten_kota', 'provinsi', 'kode_pos', 'kontak_darurat_nama',
                'kontak_darurat_phone', 'kontak_darurat_hubungan', 'kontak_darurat_alamat',
                'pendidikan', 'pengalaman', 'pekerjaan', 'negara_tujuan',
                'objektif_karir', 'ringkasan_profil', 'keahlian_khusus', 'hobi',
                'motto_hidup', 'kondisi_kesehatan', 'medical_checkup_terakhir'
            ]));

            // Handle file uploads (same logic as store)
            $userId = $user->id;
            
            if (is_file($request->foto)) {
                $fotoDir = 'document/' . $userId . '/' . Str::random(32) . '.' . $request->file('foto')->getClientOriginalExtension();
                Storage::disk('public')->put($fotoDir, fopen($request->file('foto'), 'r+'));
                $data->foto = '/uploads/'.$fotoDir;
            }


            $data->save();

        } catch (\QueryException $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data.']);
        }
        
        DB::commit();
        return redirect()->route('user.biodata.index')->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Get PDF Biodata
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function pdf()
    {
        $user = auth()->guard('web')->user();
        $data = User::with(['detail'])->where('id', $user->id)->first();

        if(!$data || !$data->detail) {
            abort(404, 'Data biodata tidak ditemukan.');
        }

        $pdf = Pdf::loadView('pdf.cv', ['data' => $data]);
        return $pdf->stream('CV-'.$data->detail->nama.'.pdf');
    }
    
}
