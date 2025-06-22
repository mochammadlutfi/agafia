<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Redirect;
use Storage;
use PDF;

use App\Models\UserDetail;
use App\Models\Anak;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Carbon\Carbon;
use App\Models\Paket;
use App\Notifications\RegisterNotification;
class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:web');
    }

    public function showRegisterForm()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $rules = [
            'nama' => 'required',
            'email' => 'email|required|unique:users',
            'password' => 'required|min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'required|min:6'
        ];

        $pesan = [
            'nama.required' => 'Nama Lengkap Wajib Diisi!',
            'email.required' => 'Alamat Email Wajib Diisi!',
            'email.unique' => 'Alamat Email Sudah Terdaftar!',
            'email.email' => 'Alamat Email Tidak Valid!',
            'password.required' => 'Password Wajib Diisi!',
            'password.min' => 'Tidak Boleh Kurang Dari 6 Karakter!',
            'password.same' => 'Konfirmasi Password Tidak Sama!',
            'password_confirmation.required' => 'Konfirmasi Password Wajib Diisi!',
            'password_confirmation.min' => 'Tidak Boleh Kurang Dari 6 Karakter!'
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $data = new User;
                $data->nama = $request->nama;
                $data->email = $request->email;
                $data->password = Hash::make($request->password);
                $data->save();

                // $data->sendEmailVerificationNotification();
                auth()->guard('web')->attempt(array('email' => $request->email, 'password' => $request->password), true);
            }catch(\QueryException $e){
                DB::rollback();
                return response()->json([
                    'fail' => true,
                    'pesan' => 'Error Menyimpan Data',
                    'log' => $e,
                ]);
            }

            DB::commit();
            return redirect()->route('verification.notice');
        }
    }

    public function detail()
    {

        $user = auth()->guard('web')->user();

        return Inertia::render('User/Biodata/Form',[
            
        ]);
    }

    public function detailStore(Request $request)
    {
        DB::beginTransaction();
        try{

            $user = auth()->guard('web')->user();

            $ktpAyahDir = null;
            $ktpIbuDir = null;
            $kkDir = null;
            $akteDir = null;

            if (is_file($request->ktp_ayah)) {
                $ktpAyahDir = 'scan/' . Str::random(32) . '.' . $request->file('ktp_ayah')->getClientOriginalExtension();
                $directory = Storage::disk('public')->put($ktpAyahDir, fopen($request->file('ktp_ayah'), 'r+'));
            }

            if (is_file($request->ktp_ibu)) {
                $ktpIbuDir = 'scan/' . Str::random(32) . '.' . $request->file('ktp_ibu')->getClientOriginalExtension();
                $directory = Storage::disk('public')->put($ktpIbuDir, fopen($request->file('ktp_ibu'), 'r+'));
            }

            if (is_file($request->kk)) {
                $kkDir = 'scan/' . Str::random(32) . '.' . $request->file('kk')->getClientOriginalExtension();
                $directory = Storage::disk('public')->put($kkDir, fopen($request->file('kk'), 'r+'));
            }

            if (is_file($request->scan_akte_anak)) {
                $akteDir = 'scan/' . Str::random(32) . '.' . $request->file('scan_akte_anak')->getClientOriginalExtension();
                $directory = Storage::disk('public')->put($akteDir, fopen($request->file('scan_akte_anak'), 'r+'));
            }

            $orang_tua = UserDetail::updateOrCreate(["user_id" => $user->id], [
                "user_id" => $user->id,
                "nama_ayah" => $request->nama_ayah,
                "tmp_lahir_ayah" => $request->tmp_lahir_ayah,
                "tgl_lahir_ayah" => $request->tgl_lahir_ayah,
                "telp_ayah" => $request->telp_ayah,
                "alamat_ayah" => $request->alamat_ayah,
                "pekerjaan_ayah" => $request->pekerjaan_ayah,
                "penghasilan_ayah" => $request->penghasilan_ayah,
                "alamat_kantor_ayah" => $request->alamat_kantor_ayah,
                "pendidikan_ayah" => $request->pendidikan_ayah,
                "agama_ayah" => $request->agama_ayah,

                "nama_ibu" => $request->nama_ibu,
                "tmp_lahir_ibu" => $request->tmp_lahir_ibu,
                "tgl_lahir_ibu" => $request->tgl_lahir_ibu,
                "telp_ibu" => $request->telp_ibu,
                "alamat_ibu" => $request->alamat_ibu,
                "pekerjaan_ibu" => $request->pekerjaan_ibu,
                "penghasilan_ibu" => $request->penghasilan_ibu,
                "alamat_kantor_ibu" => $request->alamat_kantor_ibu,
                "pendidikan_ibu" => $request->pendidikan_ibu,
                "agama_ibu" => $request->agama_ibu,

                "scan_ktp_ayah" => $ktpAyahDir ?? $request->ktp_ayah,
                "scan_ktp_ibu" => $ktpIbuDir ?? $request->ktp_ibu,
                "scan_kk" => $kkDir ?? $request->kk,
            ]);

        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
        return Inertia::location('https://app.sandbox.midtrans.com/snap/v4/redirection/'. $snapToken);
    }

    public function revisi($id)
    {

        $user = auth()->guard('web')->user();

        $data = Anak::with(['user' => function($q){
            return $q->with('detail');
        }])->where('id', $id)->first();

        return Inertia::render('Register/Revisi',[
            'value' => $data,
            'editMode' => $data ? true : false,
        ]);
    }
    
    public function revisiStore($id, Request $request)
    {
        DB::beginTransaction();
        try{

            $user = auth()->guard('web')->user();
            
            $data = Anak::where('id', $id)->first();
            $data->nama = $request->nama_anak;
            $data->username = $request->username;
            $data->jk = $request->jk_anak;
            $data->anak_ke = $request->anak_ke;
            $data->tmp_lahir = $request->tmp_lahir_anak;
            $data->tgl_lahir = $request->tgl_lahir_anak;
            $data->alamat = $request->alamat_anak;
            $data->jarak = $request->jarak;
            $data->sosialisasi_dengan_lingkungan = $request->sosialisasi_dengan_lingkungan_anak;
            $data->sakit_yang_pernah_diderita = $request->sakit_yang_pernah_diderita_anak;
            $data->makanan_yang_disukai = $request->makanan_yang_disukai_anak;
            $data->makanan_yang_tidak_disukai = $request->makanan_yang_tidak_disukai_anak;
            $data->alergi = $request->memiliki_alergi_anak;
            $data->scan_akte = $akteDir ?? $request->scan_akte_anak;
            if (is_file($request->scan_akte_anak)) {
                if($data->scan_akte){
                    Storage::disk('public')->delete($data->scan_akte);
                }

                $akteDir = 'scan/' . Str::random(32) . '.' . $request->file('scan_akte_anak')->getClientOriginalExtension();
                $directory = Storage::disk('public')->put($akteDir, fopen($request->file('scan_akte_anak'), 'r+'));
            }

            $data->status = 'Pending';
            $data->alasan = null;
            $data->save();

        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
        return redirect()->route('user.anak.show', $id);
    }
    
    private function getNomor()
    {

        $q = Invoice::select(DB::raw('MAX(RIGHT(nomor,5)) AS kd_max'));

        $code = 'DC/';
        $no = 1;
        date_default_timezone_set('Asia/Jakarta');

        if($q->count() > 0){
            foreach($q->get() as $k){
                return $code . date('ymd') .'/'.sprintf("%05s", abs(((int)$k->kd_max) + 1));
            }
        }else{
            return $code . date('ymd') .'/'. sprintf("%05s", $no);
        }
    }
}
