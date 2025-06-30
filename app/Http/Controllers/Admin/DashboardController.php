<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Inertia\Inertia;

use App\Models\User;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\JadwalInterview;
use App\Models\Training;
use App\Models\ProgramTraining;
use App\Models\DokumenLamaran;


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
        // Application statistics using new lamaran system
        $total_lamaran = Lamaran::count();
        $total_pending = Lamaran::pending()->count();
        $total_diterima = Lamaran::diterima()->count();
        $total_ditolak = Lamaran::ditolak()->count();
        $interview_scheduled = JadwalInterview::where('status', 'dijadwalkan')->count();
        $medical_process = Lamaran::where('status', 'medical')->count();
        $training_process = Lamaran::where('status', 'pelatihan')->count();
        $siap_berangkat = Lamaran::siap()->count();
        $selesai = Lamaran::selesai()->count();
        
        // User statistics
        $total_users = User::count();
        $verified_users = User::whereNotNull('email_verified_at')->count();
        
        // Lowongan statistics
        $total_lowongan = Lowongan::count();
        $lowongan_aktif = Lowongan::buka()->count();
        
        // Document statistics
        $total_dokumen = DokumenLamaran::count();
        $dokumen_pending = DokumenLamaran::pending()->count();
        $dokumen_approved = DokumenLamaran::approved()->count();
        
        $stats = collect([
            [
                'label' => 'Total Lamaran',
                'value' => $total_lamaran,
                'color' => '#409eff',
                'icon' => 'document',
            ],
            [
                'label' => 'Lamaran Pending',
                'value' => $total_pending,
                'color' => '#e6a23c',
                'icon' => 'clock',
            ],
            [
                'label' => 'Lamaran Diterima',
                'value' => $total_diterima,
                'color' => '#67c23a',
                'icon' => 'check',
            ],
            [
                'label' => 'Interview Terjadwal',
                'value' => $interview_scheduled,
                'color' => '#409eff',
                'icon' => 'calendar',
            ],
            [
                'label' => 'Medical Check Up',
                'value' => $medical_process,
                'color' => '#f56c6c',
                'icon' => 'medical',
            ],
            [
                'label' => 'Sedang Training',
                'value' => $training_process,
                'color' => '#909399',
                'icon' => 'graduation',
            ],
            [
                'label' => 'Siap Berangkat',
                'value' => $siap_berangkat,
                'color' => '#67c23a',
                'icon' => 'plane',
            ],
            [
                'label' => 'Selesai',
                'value' => $selesai,
                'color' => '#67c23a',
                'icon' => 'success',
            ],
        ]);

        // Monthly application statistics
        $monthlyStats = Lamaran::selectRaw('MONTH(tanggal_lamaran) as month, COUNT(*) as count')
            ->whereYear('tanggal_lamaran', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = $monthlyStats[$i] ?? 0;
        }

        // Status distribution
        $statusDistribution = [
            'pending' => $total_pending,
            'diterima' => $total_diterima,
            'ditolak' => $total_ditolak,
            'interview' => Lamaran::where('status', 'interview')->count(),
            'medical' => $medical_process,
            'pelatihan' => $training_process,
            'siap' => $siap_berangkat,
            'selesai' => $selesai,
        ];

        // Recent applications
        $recentApplications = Lamaran::with(['user.detail', 'lowongan'])
            ->latest()
            ->limit(10)
            ->get();

        // Training programs with counts
        $program = ProgramTraining::withCount('training')->latest()->get();

        // Additional statistics
        $additionalStats = [
            'users' => [
                'total' => $total_users,
                'verified' => $verified_users,
                'completion_rate' => $total_users > 0 ? round(($verified_users / $total_users) * 100, 1) : 0
            ],
            'lowongan' => [
                'total' => $total_lowongan,
                'aktif' => $lowongan_aktif,
                'closure_rate' => $total_lowongan > 0 ? round((($total_lowongan - $lowongan_aktif) / $total_lowongan) * 100, 1) : 0
            ],
            'documents' => [
                'total' => $total_dokumen,
                'pending' => $dokumen_pending,
                'approved' => $dokumen_approved,
                'approval_rate' => $total_dokumen > 0 ? round(($dokumen_approved / $total_dokumen) * 100, 1) : 0
            ]
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'monthlyData' => $monthlyData,
            'statusDistribution' => $statusDistribution,
            'recentApplications' => $recentApplications,
            'program' => $program,
            'additionalStats' => $additionalStats
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
