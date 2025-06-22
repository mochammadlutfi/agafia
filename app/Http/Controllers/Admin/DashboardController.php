<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Inertia\Inertia;


use App\Models\User;
use App\Models\JadwalInterview;
use App\Models\Training;
use App\Models\ProgramTraining;


class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $total_talent = User::latest()->get()->count();
        $total_pending = User::where('status', 'pending')->whereHas('detail')->latest()->get()->count();
        $jadwal_interview = JadwalInterview::where('status', 'dijadwalkan')->latest()->get()->count();
        $training = Training::where('status', 'sedang_pelatihan')->get()->count();
        $siap = User::where('status', 'siap')->latest()->get()->count();
        $medical = User::where('status', 'medical_checkup')->latest()->get()->count();
        
        $stats = collect([
            [
                'label' => 'Total Pendaftar',
                'value' => $total_talent,
                'color' => '#409eff',
            ],
            [
                'label' => 'Total Pending',
                'value' => $total_pending,
                'color' => '#e6a23c',
            ],
            [
                'label' => 'Interview Terjadwal',
                'value' => $jadwal_interview,
                'color' => '#67c23a',
            ],
            [
                'label' => 'Medical Checkup',
                'value' => $medical,
                'color' => '#67c23a',
            ],
            [
                'label' => 'Sedang Training',
                'value' => $training,
                'color' => '#909399',
            ],
            [
                'label' => 'Siap Kerja',
                'value' => $siap,
                'color' => '#67c23a',
            ],
        ]);

        $program = ProgramTraining::withCount('training')->latest()->get();

        return Inertia::render('Dashboard',[
            'stats' => $stats,
            'program' =>  $program
        ]);

    }


    public function statistik()
    {


        return response()->json($data);
    }


    public function generator()
    {
        DB::table('antar_jemput')->insert([
            'email' => 'kayla@example.com',
            'votes' => 0
        ]);
    }
}
