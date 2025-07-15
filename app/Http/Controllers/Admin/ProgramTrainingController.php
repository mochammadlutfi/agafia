<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Storage;

use App\Models\ProgramTraining;


class ProgramTrainingController extends Controller
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
        return Inertia::render('ProgramTraining/Index');
    }

    
    public function create(Request $request)
    {
        return Inertia::render('ProgramTraining/Form', [
            'editMode' => false,
            'value' => null
        ]);
    }
    
    public function store(Request $request)
    {
        $rules = [
            'nama' => 'required',
            'deskripsi' => 'required',
            'lokasi' => 'required',
            'durasi' => 'required',
            'kapasitas' => 'required',
            'instruktur' => 'required',
        ];

        $pesan = [
            'nama.required' => 'Nama Program Wajib Diisi!',
            'deskripsi.required' => 'Deskripsi Program Wajib Diisi!',
            'lokasi.required' => 'Lokasi Training Wajib Diisi!',
            'durasi.required' => 'Durasi Program Wajib Diisi!',
            'kapasitas.required' => 'Kapasitas Wajib Diisi!',
            'instruktur.required' => 'Instruktur Wajib Diisi!',
        ];

        $validator =  Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $data = new ProgramTraining();
                $data->nama = $request->nama;
                $data->lokasi = $request->lokasi;
                $data->deskripsi = $request->deskripsi;
                $data->durasi = $request->durasi;
                $data->kapasitas = $request->kapasitas;
                $data->instruktur = $request->instruktur;
                $data->aktif = $request->aktif;
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.training.program.show', $data->id);
        }
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data = ProgramTraining::where('id', $id)->first();

        return Inertia::render('ProgramTraining/Show', [
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

        $data = ProgramTraining::with(['detail'])
        ->where('id', $id)->first();

        return Inertia::render('ProgramTraining/Form',[
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
            'nama' => 'required',
            'deskripsi' => 'required',
            'lokasi' => 'required',
            'durasi' => 'required',
            'kapasitas' => 'required',
            'instruktur' => 'required',
        ];

        $pesan = [
            'nama.required' => 'Nama Program Wajib Diisi!',
            'deskripsi.required' => 'Deskripsi Program Wajib Diisi!',
            'lokasi.required' => 'Lokasi Training Wajib Diisi!',
            'durasi.required' => 'Durasi Program Wajib Diisi!',
            'kapasitas.required' => 'Kapasitas Wajib Diisi!',
            'instruktur.required' => 'Instruktur Wajib Diisi!',
        ];

        $validator =  Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $data = ProgramTraining::where('id', $id)->first();
                $data->nama = $request->nama;
                $data->lokasi = $request->lokasi;
                $data->deskripsi = $request->deskripsi;
                $data->durasi = $request->durasi;
                $data->kapasitas = $request->kapasitas;
                $data->instruktur = $request->instruktur;
                $data->aktif = $request->aktif;
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.training.program.show', $id);
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
            
            $program = ProgramTraining::find($id);
            $program->delete();

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'fail' => true,
                'errors' => $e,
                'pesan' => 'Error Menghapus Data Program Training',
            ]);
        }

        DB::commit();
        return redirect()->route('admin.training.program.index');

    }

    public function report(Request $request)
    {
        $kelompok = $request->kelompok;
        $paket = $request->paket;

        return Excel::download(new AnakExport($kelompok, $paket), 'Data Anak.xlsx');
    }

    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        
        $elq = ProgramTraining::withCount('training')
        ->when($request->q, function($query, $search){
            $query->where('nama', 'LIKE', '%' . $search . '%');
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
