<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Storage;

use App\Models\User;
use App\Models\UserAddress;
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = auth()->guard('web')->user();
        $data = User::where('id',$user->id)->first();

        return Inertia::render('User/Profile',[
            'data' => $data,
        ]);
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateProfil(Request $request)
    {
        $user = auth()->guard('web')->user();

        $rules = [
            'nama' => 'required',
        ];

        $pesan = [
            'nama.required' => 'Nama Lengkap harus diisi',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = User::where('id', $user->id)->first();
                    $data->name = $request->name;
                    $data->email = $request->email;
                    $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('user.profile');
        }
    }

    
    private function uploadFiles($file, $name){
        $file_name = $name. '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs(
            'uploads/customer',
            $file,
            $file_name
        );
        
        return 'uploads/customer/'.$file_name;
    }


    public function email()
    {
        $data = User::select('name', 'email', 'phone', 'avatar')
        ->where('id', auth()->guard('web')->user()->id)->first();

        return Inertia::render('Frontend/User/Settings/Email',[
            'data' => $data,
        ]);
    }

    public function validate(Request $request)
    {
        $rules = [
            'password' => 'required|min:6',
        ];

        $pesan = [
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password kurang dari 6 karakter!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return response()->json([
                'success'=> false,
                'message' => $validator->errors()
            ]);
        }else{
            $user = User::where('email', auth()->guard('web')->user()->email)->first();

            if (!Hash::check($request->password, $user->password)) {
                $error['password'] = array('Password salah!');
                return response()->json([
                    'success'=> false,
                    'message' => $error
                ]);
             }
            return response()->json([
                'success' => true, 
                'message'=>'success'
            ]);
        }
    }

    
    public function emailUpdate(Request $request)
    {
        // dd($request->email);
        $rules = [
            'email' => 'required|unique:users,email|email',
        ];

        $pesan = [
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah digunakan.',
            'email.email' => 'Format alamat email salah.',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{

                    $data = User::where('email', auth()->guard('web')->user()->email)->first();
                    $data->email = $request->email;
                    $data->email_verified_at = null;
                    $data->save();
                    
                    $data->sendEmailVerificationNotification();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('user.settings.index');
        }
    }

    
    public function phone()
    {
        $data = User::select('name', 'email', 'phone', 'avatar')
        ->where('id', auth()->guard('web')->user()->id)->first();

        return Inertia::render('Frontend/User/Settings/Phone',[
            'data' => $data,
        ]);
    }


    public function password()
    {
        return Inertia::render('User/Password');
    }



}
