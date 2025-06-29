<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use App\Models\Training;
use App\Models\HasilInterview;
use App\Models\NilaiDetail;


class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Training/Index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Training/Form');
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
            'user_id' => 'required',
            'program_id' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ];

        $pesan = [
            'user_id.required' => 'Talent Wajib Diisi!',
            'program_id.required' => 'Program Training Wajib Diisi!',
            'tanggal_mulai.required' => 'Tanggal Mulai Wajib Diisi!',
            'tanggal_mulai.date' => 'Format Tanggal Mulai Tidak Valid!',
            'tanggal_selesai.required' => 'Tanggal Selesai Wajib Diisi!',
            'tanggal_selesai.date' => 'Format Tanggal Selesai Tidak Valid!',
            'tanggal_selesai.after_or_equal' => 'Tanggal Selesai Harus Setelah atau Sama Dengan Tanggal Mulai!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $user = auth()->guard('admin')->user();

                $data = new Training();
                $data->user_id = $request->user_id;
                $data->program_id = $request->program_id;
                $data->tanggal_daftar = Carbon::today();
                $data->tanggal_mulai = $request->tanggal_mulai;
                $data->tanggal_selesai = $request->tanggal_selesai;
                $data->status = 'terdaftar';
                $data->didaftarkan_oleh = $user->id;
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.training.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $data = Training::with(['user', 'program', 'staff' ])->where('id', $id)->first();
        
        return Inertia::render('Training/Show', [
            'data' => $data
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $data = Paket::where('id', $id)->first();
        
        return Inertia::render('Paket/Form', [
            'value' => $data,
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
    public function update(Request $request, $id)
    {
        $rules = [
            'user_id' => 'required',
            'program_id' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ];

        $pesan = [
            'user_id.required' => 'Talent Wajib Diisi!',
            'program_id.required' => 'Program Training Wajib Diisi!',
            'tanggal_mulai.required' => 'Tanggal Mulai Wajib Diisi!',
            'tanggal_mulai.date' => 'Format Tanggal Mulai Tidak Valid!',
            'tanggal_selesai.required' => 'Tanggal Selesai Wajib Diisi!',
            'tanggal_selesai.date' => 'Format Tanggal Selesai Tidak Valid!',
            'tanggal_selesai.after_or_equal' => 'Tanggal Selesai Harus Setelah atau Sama Dengan Tanggal Mulai!',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                $data = Training::where('id', $id)->first();
                $data->user_id = $request->user_id;
                $data->program_id = $request->program_id;
                $data->tanggal_mulai = $request->tanggal_mulai;
                $data->tanggal_selesai = $request->tanggal_selesai;
                $data->status = $request->status;
                $data->save();

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.training.show', $id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $hapus_db = Training::destroy($id);
        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }

        DB::commit();
        return redirect()->route('admin.training.index');
    }

    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        $limit = ($request->limit) ? $request->limit : 25;
        $paging = !empty($request->page) ? true : false;

        $elq = Training::when($request->q, function($query, $search){
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%');
            });
        })
        ->with(['program', 'user'])
        ->orderBy($sort, $sortDir);
        
        if($paging){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }

        return response()->json($data);
    }
}
