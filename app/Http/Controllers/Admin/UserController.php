<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Storage;

use App\Models\Admin;
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:admin']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = auth()->guard('admin')->user();
        $data = Admin::where('id',$user->id)->first();

        return Inertia::render('Profile',[
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
        $user = auth()->guard('admin')->user();

        $rules = [
            'nama' => 'required',
            'email' => 'required|unique:admins,email,'.$user->id,
            'username' => 'required|unique:admins,username,'.$user->id,
        ];

        $pesan = [
            'nama.required' => 'Nama Lengkap harus diisi',
            'email.required' => 'Email harus diisi',
            'email.required' => 'Email sudah digunakan!',
            'username.required' => 'Username harus diisi',
            'username.required' => 'Username sudah digunakan!'
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = Admin::where('id', $user->id)->first();
                    $data->nama = $request->nama;
                    $data->email = $request->email;
                    $data->username = $request->username;
                    $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.profile');
        }
    }

    public function passwordUpdate(Request $request)
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
            return back()->withErrors($validator->errors());
        }else{
            $data = auth()->guard('admin')->user();
            $data->password = Hash::make($request->password);
            $data->save();
            
             return redirect()->route('admin.password');
        }
    }


    public function password()
    {
        return Inertia::render('Password');
    }



}
