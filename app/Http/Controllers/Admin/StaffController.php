<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Auth;
use App\Models\Admin;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Image;

class StaffController extends Controller
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
        return Inertia::render('Staff/Index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return Inertia::render('Staff/Form');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validator = $this->validate($request->all());
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{                
                $data = new Admin();
                $data->nama = $request->nama;
                $data->email = $request->email;
                $data->phone = $request->phone;
                $data->username = $request->username;
                $data->level = $request->level;
                $data->password = Hash::make($request->password);
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.staff.index');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data = Admin::find($id);

        return Inertia::render('Staff/Show', [
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

        $value = Admin::find($id);

        return Inertia::render('Staff/Form',[
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
        $validator = $this->validate($request->all(), true, $id);
        if ($validator->fails()){
            dd($validator->errors());
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $data = Admin::where('id', $id)->first();
                $data->nama = $request->nama;
                $data->jk = $request->jk;
                $data->email = $request->email;
                $data->phone = $request->phone;
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.staff.show', $id);
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
            
            $pdk = Admin::find($id);
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
        return redirect()->route('admin.staff.index');

    }

    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';

        $elq = Admin::when($request->q, function($query, $search){
            $query->where('nama', 'LIKE', '%' . $search . '%')
            ->orWhere('alamat', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');
        })
        ->when($request->level, function($query, $level){
            $query->where('level', $level);
        })
        ->orderBy($sort, $sortDir);
        
        if($request->limit){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }

        return response()->json($data);
    }

    private function validate($data, $editMode = false, $id = null){
        
        $rules = [
            'nama' => 'required',
            'jk' => 'required',
            'tmpLahir' => 'required',
            'tglLahir' => 'required',
            'alamat' => 'required',
            'agama' => 'required',
            'pendidikan_terakhir' => 'required',
            'tglMulai' => 'required',
            'tglMasuk' => 'required',
            'email' => 'required|unique:admins,email,'.$id,
        ];

        $pesan = [
            'nama.required' => 'Nama Lengkap Wajib Diisi!',
            'jk.required' => 'Jenis Kelamin Wajib Diisi!',
            'tmpLahir.required' => 'Tempat Lahir Wajib Diisi!',
            'tglLahir.required' => 'Tanggal Lahir Wajib Diisi!',
            'alamat.required' => 'Alamat Lengkap Wajib Diisi!',
            'agama.required' => 'Agama Wajib Diisi!',
            'pendidikan_terakhir.required' => 'Pendidikan Terakhir Wajib Diisi!',
            'tglMulai.required' => 'Mulai Bekerja Wajib Diisi!',
            'tglMasuk.required' => 'Tanggal Masuk Wajib Diisi!',
            'email.required' => 'Alamat Email Wajib Diisi!',
            'email.unique' => 'Alamat Email Sudah Digunakan!',
        ];
        
        if(!$editMode){
            $rules['username'] = 'required|unique:admins,username,'.$id;
            $rules['password'] = 'required';

            $pesan['username.required'] = 'Username Wajib Diisi!';
            $pesan['username.unique'] = 'Username Sudah Digunakan!';
            $pesan['password.required'] = 'Password Wajib Diisi!';
        }

        return Validator::make($data, $rules, $pesan);
    }
}
