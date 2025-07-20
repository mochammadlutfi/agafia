<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\DokumenLamaran;
use App\Models\Training;
use App\Models\Interview;
use App\Models\Medical;

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
        
        // Get user's application statistics
        $stats = [
            'total_lamaran' => $user->lamaran()->count(),
            'pending' => $user->lamaran()->pending()->count(),
            'diterima' => $user->lamaran()->diterima()->count(),
            'ditolak' => $user->lamaran()->ditolak()->count(),
            'interview' => $user->lamaran()->interview()->count(),
            'medical' => $user->lamaran()->medical()->count(),
            'pelatihan' => $user->lamaran()->pelatihan()->count(),
            'siap' => $user->lamaran()->siap()->count(),
            'selesai' => $user->lamaran()->selesai()->count(),
        ];
        
        // Get recent applications
        $recentApplications = $user->lamaran()
            ->with(['lowongan'])
            ->latest()
            ->limit(5)
            ->get();
            
        // Get upcoming interviews
        $upcomingInterviews = Interview::with(['lamaran.lowongan', 'pewawancara'])
            ->where('tanggal', '>=', now())
            ->where('status', 'dijadwalkan')
            ->where('user_id', $user->id)
            ->orderBy('tanggal', 'asc')
            ->limit(3)
            ->get();
            
        // Get active job opportunities
        $activeJobs = Lowongan::buka()
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('lowongan_id')
                      ->from('lamaran')
                      ->where('user_id', $user->id)
                      ->whereNotIn('status', ['ditolak']);
            })
            ->latest()
            ->limit(6)
            ->get();
            
        // Get document completion for active applications
        $activeApplications = $user->lamaran()
            ->whereNotIn('status', ['ditolak', 'selesai'])
            ->with(['lowongan', 'dokumen'])
            ->get()
            ->map(function($lamaran) {
                $requiredDocs = $this->getRequiredDocumentsCount($lamaran->status);
                $uploadedDocs = $lamaran->dokumen()->count();
                $approvedDocs = $lamaran->dokumen()->where('status', 'approved')->count();
                
                return [
                    'lamaran' => $lamaran,
                    'documents' => [
                        'required' => $requiredDocs,
                        'uploaded' => $uploadedDocs,
                        'approved' => $approvedDocs,
                        'completion_percentage' => $requiredDocs > 0 ? ($uploadedDocs / $requiredDocs) * 100 : 0
                    ]
                ];
            });

        return Inertia::render('User/Dashboard', [
            'stats' => $stats,
            'recentApplications' => $recentApplications,
            'upcomingInterviews' => $upcomingInterviews,
            'activeJobs' => $activeJobs,
            'activeApplications' => $activeApplications
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function interview()
    {
        $user = auth()->guard('web')->user();

        $interviews = Interview::with([
                'lamaran.lowongan',
                'pewawancara',
                'pembuat',
                'hasil'
            ])
            ->where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return Inertia::render('User/Interview', [
            'interviews' => $interviews
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Get dashboard statistics for API calls
     */
    public function statistics()
    {
        $user = auth()->guard('web')->user();
        
        $stats = [
            'applications' => [
                'total' => $user->lamaran()->count(),
                'pending' => $user->lamaran()->pending()->count(),
                'accepted' => $user->lamaran()->diterima()->count(),
                'rejected' => $user->lamaran()->ditolak()->count(),
                'in_progress' => $user->lamaran()->whereIn('status', ['interview', 'medical', 'pelatihan'])->count(),
                'completed' => $user->lamaran()->whereIn('status', ['siap', 'selesai'])->count(),
            ],
            'documents' => [
                'total' => DokumenLamaran::whereHas('lamaran', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->count(),
                'pending' => DokumenLamaran::whereHas('lamaran', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->pending()->count(),
                'approved' => DokumenLamaran::whereHas('lamaran', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->approved()->count(),
            ],
            'interviews' => [
                'total' => Interview::where('user_id', $user->id)->count(),
                'upcoming' => Interview::where('user_id', $user->id)
                ->where('tanggal', '>=', now())->where('status', 'dijadwalkan')->count(),
                'completed' => Interview::where('user_id', $user->id)
                ->where('status', 'selesai')->count(),
            ]
        ];
        
        return response()->json($stats);
    }

    /**
     * Get required documents count based on status
     */
    private function getRequiredDocumentsCount($status)
    {
        switch($status) {
            case 'pending':
            case 'diterima':
                return \App\Models\KategoriDokumen::pendaftaran()->count();
            case 'medical':
                return \App\Models\KategoriDokumen::medical()->count();  
            case 'siap':
                return \App\Models\KategoriDokumen::keberangkatan()->count();
            default:
                return 0;
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
