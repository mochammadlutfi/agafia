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
        $validator = $this->validate($request->all());
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $user = new User();
                $user->nama = $request->nama;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->status = 'pending';
                $user->save();

                $detail = new UserDetail($request->all());
                $user->detail()->save($detail);

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.pengguna.index');
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
        $data = User::with(['detail'])
        ->where('id', $id)->first();


        return Inertia::render('Pengguna/Form',[
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
        $validator = $this->validate($request->all(), true);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $user = User::where('id', $id)->first();
                $user->nama = $request->nama;
                $user->email = $request->email;
                if ($request->password) {
                    $user->password = Hash::make($request->password);
                }
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
            return redirect()->route('admin.pengguna.show', $id);
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
        return redirect()->route('admin.pengguna.index');

    }

    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        $limit = ($request->limit) ? $request->limit : 25;

        $data = User::with(['detail'])
        ->when($request->q, function($query, $search){
            $query->where('nama', 'LIKE', '%' . $search . '%');
        })
        ->orderBy($sort, $sortDir)->paginate($limit);

        return response()->json($data);
    }

    

    private function validate($data, $editMode = false){
        
        $rules = [
            'nama' => 'required',
            'email' => 'required|email|unique:users,email,'.($editMode ? $data['id'] : 'NULL').',id',
            'nik' => 'required|unique:user_details,nik,'.($editMode ? $data['id'] : 'NULL').',user_id',
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
            'email.required' => 'Email Wajib Diisi!',
            'email.email' => 'Format Email Tidak Valid!',
            'email.unique' => 'Email Sudah Terdaftar!',
            'nik.required' => 'NIK Wajib Diisi!',
            'nik.unique' => 'NIK Sudah Terdaftar!',
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
        

        if(!$editMode){
            $rules['password'] = 'required|min:6';
            $pesan['password.required'] = 'Password Wajib Diisi!';
            $pesan['password.min'] = 'Password Minimal 6 Karakter!';
        }

        return Validator::make($data, $rules, $pesan);
    }
}
