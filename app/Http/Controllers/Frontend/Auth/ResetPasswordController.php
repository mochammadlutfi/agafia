<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;
use Str;
use Inertia\Inertia;
class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function showResetForm($token, Request $request)
    {
        return Inertia::render('Frontend/Auth/Passwords/Reset',[
            'token' => $token
        ]);
        // return view("website.auth.passwords.reset", compact("token"));
    }


    public function reset(Request $request)
    {
        $rules = [
            'password' => 'required|string|same:password_confirmation'
        ];

        $pesan = [
            'password.required' => 'Password Baru Wajib Diisi!',
            'password.same' => 'Password baru yang dimasukan tidak sama!',
        ];
        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            
            // DB::beginTransaction();
            // try{

                $credentials = array(
                    'token' => $request->token,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                );

                $status = Password::reset($credentials,
                    function ($user, $password) {
                        $user->forceFill([
                            'password' => Hash::make($password)
                        ])->setRememberToken(Str::random(60));
                        $user->save();
                    }
                );
                
                if($status === Password::PASSWORD_RESET){
                    return back();
                }else{
                    return redirect()->route('login');
                }
        }
    }
}
