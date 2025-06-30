<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\DokumenLamaran;
use App\Models\Lamaran;
use App\Models\KategoriDokumen;

class DokumenLamaranController extends Controller
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
     * Display a listing of documents
     * @return Renderable
     */
    public function index(Request $request)
    {
        return Inertia::render('DokumenLamaran/Index');
    }
    
    /**
     * Show the specified document
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $dokumen = DokumenLamaran::with([
            'lamaran.user.detail',
            'lamaran.lowongan',
            'kategoriDokumen',
            'reviewer'
        ])->findOrFail($id);

        return Inertia::render('DokumenLamaran/Show', [
            'dokumen' => $dokumen
        ]);
    }

    /**
     * Get data for DataTable
     */
    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        
        $query = DokumenLamaran::with([
            'lamaran.user.detail',
            'lamaran.lowongan', 
            'kategoriDokumen',
            'reviewer'
        ])
        ->when($request->q, function($query, $search){
            $query->whereHas('lamaran.user', function($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%');
            })
            ->orWhereHas('lamaran.user.detail', function($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                  ->orWhere('nik', 'LIKE', '%' . $search . '%');
            })
            ->orWhereHas('kategoriDokumen', function($q) use ($search) {
                $q->where('nama_kategori', 'LIKE', '%' . $search . '%');
            })
            ->orWhere('nama_file', 'LIKE', '%' . $search . '%');
        })
        ->when($request->status, function($query, $status){
            $query->where('status', $status);
        })
        ->when($request->kategori_id, function($query, $kategoriId){
            $query->where('kategori_dokumen_id', $kategoriId);
        })
        ->when($request->jenis_dokumen, function($query, $jenis){
            $query->whereHas('kategoriDokumen', function($q) use ($jenis) {
                $q->where('jenis_dokumen', $jenis);
            });
        })
        ->when($request->lamaran_id, function($query, $lamaranId){
            $query->where('lamaran_id', $lamaranId);
        })
        ->when($request->tanggal_dari, function($query, $tanggalDari){
            $query->whereDate('diupload_tanggal', '>=', $tanggalDari);
        })
        ->when($request->tanggal_sampai, function($query, $tanggalSampai){
            $query->whereDate('diupload_tanggal', '<=', $tanggalSampai);
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
     * Review document (approve/reject)
     */
    public function review($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'catatan' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dokumen = DokumenLamaran::findOrFail($id);
            
            $dokumen->update([
                'status' => $request->status,
                'catatan' => $request->catatan,
                'direview_oleh' => auth()->guard('admin')->id(),
                'direview_tanggal' => now()
            ]);

            // Log review activity
            $this->logReviewActivity($dokumen, $request->status, $request->catatan);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil di-review',
                'data' => $dokumen->fresh(['lamaran.user.detail', 'kategoriDokumen', 'reviewer'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereview dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk review documents
     */
    public function bulkReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:dokumen_lamaran,id',
            'status' => 'required|in:approved,rejected',
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

            $updated = DokumenLamaran::whereIn('id', $request->ids)
                ->update([
                    'status' => $request->status,
                    'catatan' => $request->catatan,
                    'direview_oleh' => auth()->guard('admin')->id(),
                    'direview_tanggal' => now(),
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$updated} dokumen berhasil di-review"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereview dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download document file
     */
    public function download($id)
    {
        try {
            $dokumen = DokumenLamaran::findOrFail($id);
            
            $filePath = 'public/' . $dokumen->file_path;
            
            if (!Storage::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            return Storage::download($filePath, $dokumen->nama_file);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View document in browser (for PDFs and images)
     */
    public function view($id)
    {
        try {
            $dokumen = DokumenLamaran::findOrFail($id);
            
            $filePath = 'public/' . $dokumen->file_path;
            
            if (!Storage::exists($filePath)) {
                abort(404, 'File tidak ditemukan');
            }

            $mimeType = Storage::mimeType($filePath);
            $fileContent = Storage::get($filePath);

            return response($fileContent)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . $dokumen->nama_file . '"');

        } catch (\Exception $e) {
            abort(500, 'Gagal membuka file');
        }
    }

    /**
     * Get documents by application
     */
    public function byLamaran($lamaranId)
    {
        $lamaran = Lamaran::with([
            'user.detail',
            'lowongan',
            'dokumen' => function($query) {
                $query->with(['kategoriDokumen', 'reviewer'])
                      ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($lamaranId);

        // Get required documents for current status
        $requiredDocuments = $this->getRequiredDocuments($lamaran->status);
        
        // Get missing documents
        $uploadedKategoriIds = $lamaran->dokumen->pluck('kategori_dokumen_id');
        $missingDocuments = $requiredDocuments->whereNotIn('id', $uploadedKategoriIds);

        return response()->json([
            'lamaran' => $lamaran,
            'required_documents' => $requiredDocuments,
            'missing_documents' => $missingDocuments,
            'stats' => [
                'total_required' => $requiredDocuments->count(),
                'uploaded' => $lamaran->dokumen->count(),
                'approved' => $lamaran->dokumen->where('status', 'approved')->count(),
                'rejected' => $lamaran->dokumen->where('status', 'rejected')->count(),
                'pending' => $lamaran->dokumen->where('status', 'pending')->count(),
            ]
        ]);
    }

    /**
     * Get document statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => DokumenLamaran::count(),
            'pending' => DokumenLamaran::pending()->count(),
            'approved' => DokumenLamaran::approved()->count(),
            'rejected' => DokumenLamaran::rejected()->count(),
        ];

        // By category
        $byCategory = DokumenLamaran::select('kategori_dokumen_id')
            ->with('kategoriDokumen:id,nama_kategori')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('kategori_dokumen_id')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->kategoriDokumen->nama_kategori => $item->count];
            });

        // By document type
        $byType = DokumenLamaran::whereHas('kategoriDokumen')
            ->with('kategoriDokumen:id,jenis_dokumen')
            ->get()
            ->groupBy('kategoriDokumen.jenis_dokumen')
            ->map(function($items) {
                return $items->count();
            });

        // Monthly uploads
        $monthlyUploads = DokumenLamaran::selectRaw('MONTH(diupload_tanggal) as month, COUNT(*) as count')
            ->whereYear('diupload_tanggal', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($monthlyUploads[$i])) {
                $monthlyUploads[$i] = 0;
            }
        }
        ksort($monthlyUploads);

        return response()->json([
            'stats' => $stats,
            'by_category' => $byCategory,
            'by_type' => $byType,
            'monthly_uploads' => array_values($monthlyUploads)
        ]);
    }

    /**
     * Delete document
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $dokumen = DokumenLamaran::findOrFail($id);
            
            // Check if document can be deleted
            if ($dokumen->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen yang sudah disetujui tidak dapat dihapus'
                ], 422);
            }
            
            // Delete file from storage
            if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }
            
            $dokumen->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get required documents based on status
     */
    private function getRequiredDocuments($status)
    {
        switch($status) {
            case 'pending':
            case 'diterima':
                return KategoriDokumen::pendaftaran()->get();
            case 'medical':
                return KategoriDokumen::medical()->get();
            case 'siap':
                return KategoriDokumen::keberangkatan()->get();
            default:
                return collect();
        }
    }

    /**
     * Log review activity
     */
    private function logReviewActivity($dokumen, $status, $catatan = null)
    {
        \Log::info("Document #{$dokumen->id} reviewed as {$status}", [
            'dokumen_id' => $dokumen->id,
            'lamaran_id' => $dokumen->lamaran_id,
            'kategori' => $dokumen->kategoriDokumen->nama_kategori,
            'status' => $status,
            'catatan' => $catatan,
            'reviewed_by' => auth()->guard('admin')->id()
        ]);
    }
}