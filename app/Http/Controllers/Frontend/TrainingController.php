<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;
use Storage;
use App\Models\Training;

class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = auth()->guard('web')->user();
        
        $data = Training::with([
            'program',
            'lamaran.lowongan',
            'staff'
        ])->where('user_id', $user->id)->orderBy('tanggal_daftar', 'desc')->get();

        return Inertia::render('User/Training/Index',[
            'data' => $data
        ]);
    }

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $data = Training::with([
            'program',
            'lamaran.lowongan',
            'staff',
            'user.detail'
        ])->findOrFail($id);

        return Inertia::render('User/Training/Show',[
            'data' => $data
        ]);
    }

}
