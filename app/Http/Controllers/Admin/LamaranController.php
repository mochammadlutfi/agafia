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
use App\Models\Lamaran;
use App\Models\User;
use App\Models\Lowongan;
use App\Models\DokumenLamaran;
use App\Models\KategoriDokumen;
use Barryvdh\DomPDF\Facade\Pdf;

class LamaranController extends Controller
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
        return Inertia::render('Lamaran/Index');
    }
    
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $lamaran = Lamaran::with([
            'user.detail',
            'lowongan',
            'interview' => function($q) {
                return $q->with(['pewawancara']);
            },
            'dokumen',
            'medical',
            'training' => function($q){
                return $q->with(['program']);
            }
        ])->findOrFail($id);

        // Get completion statistics
        $completionStats = $this->calculateCompletionStats($lamaran);
        
        // Get available status options
        $statusOptions = $this->getStatusOptions();

        // Get required documents for current status
        $requiredDocuments = $this->getRequiredDocuments($lamaran->status);

        return Inertia::render('Lamaran/Show', [
            'lamaran' => $lamaran,
            'completionStats' => $completionStats,
            'statusOptions' => $statusOptions,
            'requiredDocuments' => $requiredDocuments,
        ]);
    }

    /**
     * Get data for DataTable
     */
    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        
        $query = Lamaran::with(['user.detail', 'lowongan'])
            ->when($request->q, function($query, $search){
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('nama', 'LIKE', '%' . $search . '%')
                      ->orWhere('email', 'LIKE', '%' . $search . '%');
                })
                ->orWhereHas('user.detail', function($q) use ($search) {
                    $q->where('nama', 'LIKE', '%' . $search . '%')
                      ->orWhere('nik', 'LIKE', '%' . $search . '%');
                })
                ->orWhereHas('lowongan', function($q) use ($search) {
                    $q->where('posisi', 'LIKE', '%' . $search . '%')
                      ->orWhere('perusahaan', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(!empty($request->status) && $request->status == 'diterima', function($query, $status){
                $query->where('status', 'diterima');
            })
            ->when(!empty($request->status) && $request->status == 'interview', function($query, $status){
                $query->where('status', 'interview');
            })
            ->when(!empty($request->status) && $request->status == 'medical', function($query, $status){
                $query->where('status', 'medical');
            })
            ->when(!empty($request->status) && $request->status == 'pelatihan', function($query, $status){
                $query->where('status', 'pelatihan');
            })
            ->when(!empty($request->status) && $request->status == 'siap', function($query, $status){
                $query->where('status', 'siap');
            })
            ->when(!empty($request->status) && $request->status == 'selesai', function($query, $status){
                $query->where('status', 'selesai');
            })
            // ->when(!empty($request->status) && $request->status == 'interview', function($query, $status){
            //     $query->where('status', 'interview')
            //     ->orWhere('status', 'medical');
            // })
            // ->when(!empty($request->status) && $request->status == 'training', function($query, $status){
            //     $query->where('status', 'medical')
            //     ->orWhere('status', 'training');
            // })
            // ->when(!empty($request->status), function($query, $status){
            //     $query->where('status', $status);
            // })
            ->when($request->lowongan_id, function($query, $lowonganId){
                $query->where('lowongan_id', $lowonganId);
            })
            ->when($request->tanggal_dari, function($query, $tanggalDari){
                $query->whereDate('tanggal_lamaran', '>=', $tanggalDari);
            })
            ->when($request->tanggal_sampai, function($query, $tanggalSampai){
                $query->whereDate('tanggal_lamaran', '<=', $tanggalSampai);
            })
            ->orderBy($sort, $sortDir);

        if($request->limit){
            $data = $query->paginate($request->limit);
        } else {
            $data = $query->get();
        }

        return response()->json($data);
    }

    /**
     * Update application status
     */
    public function updateStatus($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,diterima,ditolak,interview,medical,pelatihan,siap,selesai',
            'catatan' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $lamaran = Lamaran::findOrFail($id);
            
            $lamaran->update([
                'status' => $request->status,
                'catatan' => $request->catatan
            ]);

            // Log status change
            $this->logStatusChange($lamaran, $request->status, $request->catatan);

            return response()->json([
                'success' => true,
                'message' => 'Status lamaran berhasil diperbarui',
                'data' => $lamaran->fresh(['user.detail', 'lowongan'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status lamaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:lamaran,id',
            'status' => 'required|in:pending,diterima,ditolak,interview,medical,pelatihan,siap,selesai',
            'catatan' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updated = Lamaran::whereIn('id', $request->ids)
                ->update([
                    'status' => $request->status,
                    'catatan' => $request->catatan,
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$updated} lamaran berhasil diperbarui"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui lamaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics()
    {
        $stats = [
            'total' => Lamaran::count(),
            'pending' => Lamaran::pending()->count(),
            'diterima' => Lamaran::diterima()->count(),
            'ditolak' => Lamaran::ditolak()->count(),
            'interview' => Lamaran::interview()->count(),
            'medical' => Lamaran::medical()->count(),
            'pelatihan' => Lamaran::pelatihan()->count(),
            'siap' => Lamaran::siap()->count(),
            'selesai' => Lamaran::selesai()->count(),
        ];

        // Monthly statistics
        $monthlyStats = Lamaran::selectRaw('MONTH(tanggal_lamaran) as month, COUNT(*) as count')
            ->whereYear('tanggal_lamaran', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($monthlyStats[$i])) {
                $monthlyStats[$i] = 0;
            }
        }
        ksort($monthlyStats);

        return response()->json([
            'stats' => $stats,
            'monthly' => array_values($monthlyStats)
        ]);
    }

    /**
     * Export applications
     */
    public function export(Request $request)
    {
        $query = Lamaran::with(['user.detail', 'lowongan'])
            ->when($request->status, function($query, $status){
                $query->where('status', $status);
            })
            ->when($request->lowongan_id, function($query, $lowonganId){
                $query->where('lowongan_id', $lowonganId);
            })
            ->when($request->tanggal_dari, function($query, $tanggalDari){
                $query->whereDate('tanggal_lamaran', '>=', $tanggalDari);
            })
            ->when($request->tanggal_sampai, function($query, $tanggalSampai){
                $query->whereDate('tanggal_lamaran', '<=', $tanggalSampai);
            });

        $data = $query->get();

        // Return as Excel or CSV based on request
        $filename = 'lamaran_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
                'ID', 'Nama', 'Email', 'NIK', 'Posisi', 'Perusahaan', 
                'Tanggal Lamaran', 'Status', 'Catatan'
            ]);

            // Data rows
            foreach ($data as $lamaran) {
                fputcsv($file, [
                    $lamaran->id,
                    $lamaran->user->detail->nama ?? $lamaran->user->nama,
                    $lamaran->user->email,
                    $lamaran->user->detail->nik ?? '-',
                    $lamaran->lowongan->posisi,
                    $lamaran->lowongan->perusahaan,
                    $lamaran->tanggal_lamaran->format('d/m/Y'),
                    $lamaran->status_label,
                    $lamaran->catatan ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export applications to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Lamaran::with(['user.detail', 'lowongan', 'interview.pewawancara', 'medical', 'training.program', 'dokumen'])
            ->when($request->status, function($query, $status){
                $query->where('status', $status);
            })
            ->when($request->lowongan_id, function($query, $lowonganId){
                $query->where('lowongan_id', $lowonganId);
            })
            ->when($request->tanggal_dari, function($query, $tanggalDari){
                $query->whereDate('tanggal_lamaran', '>=', $tanggalDari);
            })
            ->when($request->tanggal_sampai, function($query, $tanggalSampai){
                $query->whereDate('tanggal_lamaran', '<=', $tanggalSampai);
            })
            ->orderBy('tanggal_lamaran', 'desc');

        $data = $query->get();
        
        // Get filter information for report header
        $filterInfo = [
            'status' => $request->status ? $this->getStatusOptions()[$request->status] : 'Semua Status',
            'tanggal_dari' => $request->tanggal_dari ? Carbon::parse($request->tanggal_dari)->format('d/m/Y') : null,
            'tanggal_sampai' => $request->tanggal_sampai ? Carbon::parse($request->tanggal_sampai)->format('d/m/Y') : null,
            'total_data' => $data->count(),
            'tanggal_export' => now()->format('d/m/Y H:i:s')
        ];

        // Generate PDF
        $pdf = PDF::loadView('pdf.lamaran-report', [
            'data' => $data,
            'filterInfo' => $filterInfo,
            'statusOptions' => $this->getStatusOptions()
        ]);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'laporan-lamaran-' . date('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Calculate completion statistics
     */
    private function calculateCompletionStats($lamaran)
    {
        // Simplified stats since document relationships don't exist yet
        return [
            'documents' => [
                'required' => 0,
                'uploaded' => 0,
                'approved' => 0,
                'percentage' => 0,
                'approval_percentage' => 0
            ],
            'profile' => [
                'completed' => $lamaran->user->detail ? 85 : 0,
                'percentage' => $lamaran->user->detail ? 85 : 0
            ]
        ];
    }

    /**
     * Get available status options
     */
    private function getStatusOptions()
    {
        return [
            'pending' => 'Menunggu Review',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            'interview' => 'Tahap Interview',
            'medical' => 'Medical Check Up',
            'pelatihan' => 'Pelatihan',
            'siap' => 'Siap Berangkat',
            'selesai' => 'Selesai'
        ];
    }

    /**
     * Get required documents for status
     */
    private function getRequiredDocuments($status)
    {
        // Return basic document list since KategoriDokumen relationships don't exist yet
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
     * Log status changes
     */
    private function logStatusChange($lamaran, $newStatus, $catatan = null)
    {
        // This could be expanded to create audit logs
        \Log::info("Lamaran #{$lamaran->id} status changed to {$newStatus}", [
            'lamaran_id' => $lamaran->id,
            'user_id' => $lamaran->user_id,
            'old_status' => $lamaran->getOriginal('status'),
            'new_status' => $newStatus,
            'catatan' => $catatan,
            'changed_by' => auth()->guard('admin')->id()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $lamaran = Lamaran::findOrFail($id);
            
            // Check if can be deleted
            if (in_array($lamaran->status, ['interview', 'medical', 'pelatihan', 'siap', 'selesai'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lamaran dengan status ' . $lamaran->status_label . ' tidak dapat dihapus'
                ], 422);
            }
            
            $lamaran->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Lamaran berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus lamaran: ' . $e->getMessage()
            ], 500);
        }
    }
}