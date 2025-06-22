<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\JadwalInterview;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->guard('web')->user();

        return Inertia::render('User/Dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function interview()
    {
        $user = auth()->guard('web')->user();

        $data = JadwalInterview::with(['talent', 'pewawancara', 'pembuat'])->where('user_id', $user->id)->first();

        return Inertia::render('User/Interview',[
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
            'nama' => 'required',
            'pekerjaan' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'kota_id' => 'required',
            'kecamatan_id' => 'required',
        ];

        $pesan = [
            'nama.required' => 'Nama Lengkap Wajib Diisi!',
            'pekerjaan.required' => 'Pekerjaan Wajib Diisi!',
            'phone.required' => 'No Handphone Wajib Diisi!',
            'email.required' => 'Alamat Email Wajib Diisi!',
            'kota_id.required' => 'Kota Wajib Diisi!',
            'kecamatan_id.required' => 'Kecamatan Wajib Diisi!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $data = new Aspirasi();
                $data->nama = $request->nama;
                $data->pekerjaan = $request->pekerjaan;
                $data->phone = $request->phone;
                $data->email = $request->email;
                $data->kota_id = $request->kota_id;
                $data->kec_id = $request->kecamatan_id;
                $data->deskripsi = $request->description;
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('home');
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
