<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Auth;
use Illuminate\Support\Str;
use Storage;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Lowongan;


class LowonganController extends Controller
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
        return Inertia::render('Lowongan/Index');
    }

    
    public function create(Request $request)
    {
        return Inertia::render('Lowongan/Form', [
            'editMode' => false,
            'value' => null
        ]);
    }
    
    public function store(Request $request)
    {
        $rules = [
            'perusahaan' => 'required',
            'posisi' => 'required',
            'skill' => 'required',
            'deskripsi' => 'required',
            'kuota' => 'required|numeric',
            'lokasi' => 'required',
        ];

        $pesan = [
            'perusahaan.required' => 'Nama Perusahaan Wajib Diisi!',
            'posisi.required' => 'Posisi Wajib Diisi!',
            'skill.required' => 'Skill Wajib Diisi!',
            'deskripsi.required' => 'Deskripsi Wajib Diisi!',
            'kuota.required' => 'Kuota Wajib Diisi!',
            'kuota.numeric' => 'Kuota Harus Angka!',
            'lokasi.required' => 'Lokasi Wajib Diisi!',
        ];

        $validator =  Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $data = new Lowongan();
                $data->perusahaan = $request->perusahaan;
                $data->posisi = $request->posisi;
                $data->skill = $request->skill;
                $data->deskripsi = $request->deskripsi;
                $data->kuota = $request->kuota;
                $data->lokasi = $request->lokasi;
                $data->status = $request->status;

                if (is_file($request->foto)) {
                    $fotoDir = 'lowongan/'. Str::random(32) . '.' . $request->file('foto')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($fotoDir, fopen($request->file('foto'), 'r+'));
                    $data->foto = '/uploads/'.$fotoDir;
                }
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.lowongan.show', $data->id);
        }
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data = Lowongan::where('id', $id)->first();

        return Inertia::render('Lowongan/Show', [
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

        $data = Lowongan::where('id', $id)->first();

        return Inertia::render('Lowongan/Form',[
            'editMode' => true,
            'value' => $data
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
        $rules = [
            'perusahaan' => 'required',
            'posisi' => 'required',
            'skill' => 'required',
            'deskripsi' => 'required',
            'kuota' => 'required|numeric',
            'lokasi' => 'required',
        ];

        $pesan = [
            'perusahaan.required' => 'Nama Perusahaan Wajib Diisi!',
            'posisi.required' => 'Posisi Wajib Diisi!',
            'skill.required' => 'Skill Wajib Diisi!',
            'deskripsi.required' => 'Deskripsi Wajib Diisi!',
            'kuota.required' => 'Kuota Wajib Diisi!',
            'kuota.numeric' => 'Kuota Harus Angka!',
            'lokasi.required' => 'Lokasi Wajib Diisi!',
        ];

        $validator =  Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $data = Lowongan::where('id', $id)->first();
                $data->perusahaan = $request->perusahaan;
                $data->posisi = $request->posisi;
                $data->skill = $request->skill;
                $data->deskripsi = $request->deskripsi;
                $data->kuota = $request->kuota;
                $data->lokasi = $request->lokasi;
                $data->status = $request->status;

                if (is_file($request->foto)) {
                    $fotoDir = 'lowongan/'. Str::random(32) . '.' . $request->file('foto')->getClientOriginalExtension();
                    $directory = Storage::disk('public')->put($fotoDir, fopen($request->file('foto'), 'r+'));
                    $data->foto = '/uploads/'.$fotoDir;
                }

                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.lowongan.show', $id);
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
            
            $pdk = Lowongan::find($id);
            $pdk->delete();

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'fail' => true,
                'errors' => $e,
                'pesan' => 'Error Menhapus Data Lowongan',
            ]);
        }

        DB::commit();
        return redirect()->route('admin.lowongan.index');

    }

    public function data(Request $request)
    { 
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        
        $elq = Lowongan::when($request->q, function($query, $search){
            $query->where('posisi', 'LIKE', '%' . $search . '%')
                ->orWhere('perusahaan', 'LIKE', '%' . $search . '%');
        })
        ->orderBy($sort, $sortDir);

        if($request->limit){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }

        return response()->json($data);
    }
}
