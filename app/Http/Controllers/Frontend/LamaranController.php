<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\DokumenLamaran;
use App\Models\KategoriDokumen;

class LamaranController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void 
     */
    public function __construct() {
        $this->middleware('auth:web');
        $this->middleware('verified');
    }

    /**
     * Display user's applications
     * @return Renderable
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $lamaran = $user->lamaran()->with([
            'lowongan'
        ])
        ->when($request->status, function($query, $status) {
            $query->where('status', $status);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        // Get application statistics
        $stats = [
            'total' => $user->lamaran()->count(),
            'pending' => $user->lamaran()->pending()->count(),
            'diterima' => $user->lamaran()->diterima()->count(),
            'ditolak' => $user->lamaran()->ditolak()->count(),
            'interview' => $user->lamaran()->interview()->count(),
            'medical' => $user->lamaran()->medical()->count(),
            'pelatihan' => $user->lamaran()->pelatihan()->count(),
            'siap' => $user->lamaran()->siap()->count(),
            'selesai' => $user->lamaran()->selesai()->count(),
        ];

        return Inertia::render('User/Lamaran/Index', [
            'lamaran' => $lamaran,
            'stats' => $stats,
            'filters' => $request->only('status')
        ]);
    }

    /**
     * Show job application form
     * @param int $lowonganId
     * @return Renderable
     */
    public function create($lowonganId)
    {
        $user = auth()->user();
        $lowongan = Lowongan::buka()->findOrFail($lowonganId);
        
        // Check if user already applied for this job
        $existingApplication = $user->lamaran()
            ->where('lowongan_id', $lowonganId)
            ->whereNotIn('status', ['ditolak'])
            ->first();

        if ($existingApplication) {
            return redirect()->route('user.lamaran.show', $existingApplication->id)
                ->with('info', 'Anda sudah melamar untuk posisi ini.');
        }

        // Check if user profile is complete
        $profileComplete = $user->detail && 
                          $user->detail->nik && 
                          $user->detail->nama && 
                          $user->detail->phone &&
                          $user->detail->alamat;

        if (!$profileComplete) {
            return redirect()->route('user.biodata.form')
                ->with('warning', 'Silakan lengkapi profil Anda terlebih dahulu sebelum melamar.');
        }

        return Inertia::render('User/Lamaran/Create', [
            'lowongan' => $lowongan,
            'user' => $user->load('detail')
        ]);
    }

    /**
     * Store new job application
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'lowongan_id' => 'required|exists:lowongan,id',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB
            'surat_lamaran' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {
            DB::beginTransaction();

            $lowongan = Lowongan::buka()->findOrFail($request->lowongan_id);
            
            // Check if user already applied
            $existingApplication = $user->lamaran()
                ->where('lowongan_id', $request->lowongan_id)
                ->whereNotIn('status', ['ditolak'])
                ->first();

            if ($existingApplication) {
                return back()->withErrors(['already_applied' => 'Anda sudah melamar untuk posisi ini.']);
            }

            // Check if user profile is complete
            $profileComplete = $user->detail && 
                              $user->detail->nik && 
                              $user->detail->nama && 
                              $user->detail->phone &&
                              $user->detail->alamat;

            if (!$profileComplete) {
                return back()->withErrors(['profile_incomplete' => 'Silakan lengkapi profil Anda terlebih dahulu sebelum melamar.']);
            }

            // Handle CV upload
            $cvPath = null;
            if ($request->hasFile('cv_file')) {
                $cvFile = $request->file('cv_file');
                $cvPath = $cvFile->store('cv/' . $user->id, 'public');
            }

            // Create application
            $lamaran = Lamaran::create([
                'user_id' => $user->id,
                'lowongan_id' => $request->lowongan_id,
                'tanggal_lamaran' => now()->toDateString(),
                'status' => 'pending',
                'cv_file' => $cvPath,
                'catatan' => $request->surat_lamaran
            ]);

            DB::commit();

            return redirect()->route('user.lamaran.show', $lamaran->id)
                ->with('success', 'Lamaran Anda berhasil dikirim!')
                ->with('lamaran_id', $lamaran->id);

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withErrors(['server_error' => 'Gagal mengirim lamaran: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Show specific application details
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $user = auth()->user();
        
        $lamaran = $user->lamaran()->with([
            'lowongan'
        ])->findOrFail($id);

        // Calculate progress
        $progress = $this->calculateProgress($lamaran);

        return Inertia::render('User/Lamaran/Show', [
            'lamaran' => $lamaran,
            'progress' => $progress
        ]);
    }

    /**
     * Cancel application (only for pending status)
     * @param int $id
     * @return Response
     */
    public function cancel($id)
    {
        $user = auth()->user();
        
        try {
            $lamaran = $user->lamaran()->where('status', 'pending')->findOrFail($id);
            
            $lamaran->update([
                'status' => 'ditolak',
                'catatan' => 'Dibatalkan oleh pelamar'
            ]);

            return redirect()->route('user.lamaran.index')
                ->with('success', 'Lamaran berhasil dibatalkan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan lamaran: ' . $e->getMessage());
        }
    }

    /**
     * Update application CV
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function updateCv(Request $request, $id)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $lamaran = $user->lamaran()
                ->whereIn('status', ['pending', 'diterima'])
                ->findOrFail($id);

            // Delete old CV if exists
            if ($lamaran->cv_file && \Storage::disk('public')->exists($lamaran->cv_file)) {
                \Storage::disk('public')->delete($lamaran->cv_file);
            }

            // Upload new CV
            $cvFile = $request->file('cv_file');
            $cvPath = $cvFile->store('cv/' . $user->id, 'public');

            $lamaran->update(['cv_file' => $cvPath]);

            return response()->json([
                'success' => true,
                'message' => 'CV berhasil diperbarui',
                'cv_url' => \Storage::disk('public')->url($cvPath)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui CV: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get application history/timeline
     * @param int $id
     * @return Response
     */
    public function timeline($id)
    {
        $user = auth()->user();
        $lamaran = $user->lamaran()->findOrFail($id);

        $timeline = [];

        // Application submitted
        $timeline[] = [
            'date' => $lamaran->created_at,
            'title' => 'Lamaran Dikirim',
            'description' => 'Anda telah mengirim lamaran untuk posisi ' . $lamaran->lowongan->posisi,
            'type' => 'info',
            'icon' => 'send'
        ];

        // Status changes would be tracked here if we had audit logs
        // For now, we'll show based on current status
        
        if ($lamaran->status !== 'pending') {
            $timeline[] = [
                'date' => $lamaran->updated_at,
                'title' => 'Status Diperbarui',
                'description' => 'Status lamaran diubah menjadi: ' . $lamaran->status_label,
                'type' => $lamaran->status === 'ditolak' ? 'danger' : 'success',
                'icon' => 'update'
            ];
        }

        // Interview schedule (commented out since relationship doesn't exist yet)
        // if ($lamaran->jadwalInterview->count() > 0) {
        //     foreach ($lamaran->jadwalInterview as $jadwal) {
        //         $timeline[] = [
        //             'date' => $jadwal->created_at,
        //             'title' => 'Interview Dijadwalkan',
        //             'description' => 'Interview dijadwalkan pada ' . $jadwal->tanggal->format('d M Y H:i'),
        //             'type' => 'info',
        //             'icon' => 'calendar'
        //         ];
        //     }
        // }

        // Sort by date
        usort($timeline, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return response()->json($timeline);
    }

    /**
     * Get required documents based on status
     */
    private function getRequiredDocuments($status)
    {
        // Return basic document requirements based on status
        switch($status) {
            case 'pending':
            case 'diterima':
                return [
                    'KTP', 'KK', 'Ijazah', 'CV', 'Foto', 'SKCK'
                ];
            case 'medical':
                return [
                    'Hasil Medical Check Up', 'Surat Sehat'
                ];
            case 'siap':
                return [
                    'Paspor', 'Visa', 'Tiket'
                ];
            default:
                return [];
        }
    }

    /**
     * Get document type for status
     */
    private function getDocumentTypeForStatus($status)
    {
        switch($status) {
            case 'pending':
            case 'diterima':
                return 'pendaftaran';
            case 'medical':
                return 'medical';
            case 'siap':
                return 'keberangkatan';
            default:
                return null;
        }
    }

    /**
     * Calculate application progress
     */
    private function calculateProgress($lamaran)
    {
        $steps = [
            'pending' => ['step' => 1, 'label' => 'Lamaran Dikirim', 'percentage' => 15],
            'diterima' => ['step' => 2, 'label' => 'Lamaran Diterima', 'percentage' => 30],
            'interview' => ['step' => 3, 'label' => 'Tahap Interview', 'percentage' => 50],
            'medical' => ['step' => 4, 'label' => 'Medical Check Up', 'percentage' => 70],
            'pelatihan' => ['step' => 5, 'label' => 'Pelatihan', 'percentage' => 85],
            'siap' => ['step' => 6, 'label' => 'Siap Berangkat', 'percentage' => 95],
            'selesai' => ['step' => 7, 'label' => 'Selesai', 'percentage' => 100],
            'ditolak' => ['step' => 0, 'label' => 'Ditolak', 'percentage' => 0],
        ];

        return $steps[$lamaran->status] ?? $steps['pending'];
    }
}