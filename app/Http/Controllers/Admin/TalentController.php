<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Image;
use Intervention\Image\ImageManagerStatic;
use Storage;
use App\Notifications\RegisterNotification;
use App\Models\User;
use App\Models\Lamaran;
use App\Models\JadwalInterview;
use App\Models\HasilInterview;
use App\Models\Training;
use App\Models\Medical;
use App\Models\DokumenLamaran;
use App\Models\KategoriDokumen;
use App\Models\UserDetail;

class TalentController extends Controller
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
        return Inertia::render('Talent/Index');
    }
    
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $talent = User::with([
            'detail',
            'lamaran' => function($query) {
                $query->with(['lowongan'])->latest();
            }
        ])->findOrFail($id);

        return Inertia::render('Talent/Show', [
            'talent' => $talent,
        ]);
    }

    /**
     * Calculate application progress for active application
     */
    private function calculateApplicationProgress($lamaran)
    {
        $statuses = [
            'pending' => ['step' => 1, 'label' => 'Lamaran Dikirim', 'color' => 'warning'],
            'diterima' => ['step' => 2, 'label' => 'Lamaran Diterima', 'color' => 'success'],
            'interview' => ['step' => 3, 'label' => 'Tahap Interview', 'color' => 'info'],
            'medical' => ['step' => 4, 'label' => 'Medical Check Up', 'color' => 'primary'],
            'pelatihan' => ['step' => 5, 'label' => 'Pelatihan', 'color' => 'purple'],
            'siap' => ['step' => 6, 'label' => 'Siap Berangkat', 'color' => 'success'],
            'selesai' => ['step' => 7, 'label' => 'Selesai', 'color' => 'success'],
            'ditolak' => ['step' => 0, 'label' => 'Ditolak', 'color' => 'danger'],
        ];

        $current = $statuses[$lamaran->status] ?? $statuses['pending'];
        $totalSteps = 7;
        $progressPercentage = $current['step'] > 0 ? ($current['step'] / $totalSteps) * 100 : 0;

        return [
            'current' => $current,
            'all' => $statuses,
            'percentage' => $progressPercentage,
            'totalSteps' => $totalSteps,
            'lamaran' => $lamaran
        ];
    }

    /**
     * Calculate completion statistics
     */
    private function calculateCompletionStats($talent, $activeApplication = null)
    {
        $requiredFields = [
            'nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'alamat', 'phone', 'nama_ayah', 'nama_ibu'
        ];

        $completed = 0;
        $total = count($requiredFields);
        
        if ($talent->detail) {
            foreach ($requiredFields as $field) {
                if (!empty($talent->detail->$field)) {
                    $completed++;
                }
            }
        }

        $profileStats = [
            'completed' => $completed,
            'total' => $total,
            'percentage' => $total > 0 ? ($completed / $total) * 100 : 0
        ];

        // Document completion based on active application
        $documentStats = ['completed' => 0, 'total' => 0, 'percentage' => 0];
        
        if ($activeApplication) {
            $requiredDocuments = $this->getRequiredDocumentsForStatus($activeApplication->status);
            $uploadedDocuments = $activeApplication->dokumen;
            
            $documentStats = [
                'completed' => $uploadedDocuments->count(),
                'total' => $requiredDocuments->count(),
                'percentage' => $requiredDocuments->count() > 0 ? 
                    ($uploadedDocuments->count() / $requiredDocuments->count()) * 100 : 0
            ];
        }

        return [
            'profile' => $profileStats,
            'documents' => $documentStats,
            'applications' => [
                'total' => $talent->lamaran->count(),
                'active' => $talent->lamaran->whereNotIn('status', ['ditolak'])->count(),
                'completed' => $talent->lamaran->whereIn('status', ['selesai'])->count()
            ]
        ];
    }
    
    /**
     * Calculate document statistics for all applications
     */
    private function calculateDocumentStats($talent)
    {
        $totalDokumen = DokumenLamaran::whereHas('lamaran', function($q) use ($talent) {
            $q->where('user_id', $talent->id);
        })->count();
        
        $approvedDokumen = DokumenLamaran::whereHas('lamaran', function($q) use ($talent) {
            $q->where('user_id', $talent->id);
        })->approved()->count();
        
        $pendingDokumen = DokumenLamaran::whereHas('lamaran', function($q) use ($talent) {
            $q->where('user_id', $talent->id);
        })->pending()->count();
        
        $rejectedDokumen = DokumenLamaran::whereHas('lamaran', function($q) use ($talent) {
            $q->where('user_id', $talent->id);
        })->rejected()->count();
        
        return [
            'total' => $totalDokumen,
            'approved' => $approvedDokumen,
            'pending' => $pendingDokumen,
            'rejected' => $rejectedDokumen,
            'approval_rate' => $totalDokumen > 0 ? ($approvedDokumen / $totalDokumen) * 100 : 0
        ];
    }
    
    /**
     * Get required documents for specific status
     */
    private function getRequiredDocumentsForStatus($status)
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
     * Get available status options for status change
     */
    private function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'diterima' => 'Diterima',
            'interview' => 'Interview',
            'medical' => 'Medical Check Up',
            'pelatihan' => 'Pelatihan',
            'siap' => 'Siap Berangkat',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {

        $data = User::with(['detail'])
        ->where('id', $id)->first();

        return Inertia::render('Talent/Form',[
            'editMode' => true,
            'data' => $data
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
            'nik' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'phone' => 'required',
            'nama_izin' => 'required',
            'status_izin' => 'required',
            'nama_ayah' => 'required',
        ];

        $pesan = [
            'nama.required' => 'Nama Lengkap Wajib Diisi!',
            'nik.required' => 'NIK Wajib Diisi!',
            'tempat_lahir.required' => 'Tempat Lahir Wajib Diisi!',
            'tanggal_lahir.required' => 'Tanggal Lahir Wajib Diisi!',
            'tanggal_lahir.date' => 'Format Tanggal Lahir Tidak Valid!',
            'jenis_kelamin.required' => 'Jenis Kelamin Wajib Diisi!',
            'alamat.required' => 'Alamat Wajib Diisi!',
            'phone.required' => 'Nomor Telepon Wajib Diisi!',
            'nama_izin.required' => 'Nama Pemberi Izin Wajib Diisi!',
            'status_izin.required' => 'Status Izin Wajib Diisi!',
            'nama_ayah.required' => 'Nama Ayah Wajib Diisi!',
        ];

        $validator =  Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                
                $user = User::where('id', $id)->first();
                $user->nama = $request->nama;
                $user->save();

                $detail = $user->detail;
                if (!$detail) {
                    $detail = new UserDetail();
                    $detail->user_id = $user->id;
                }
                $detail->fill($request->all());
                $detail->save();

            }catch(\QueryException $e){
                DB::rollback();
                dd($e);
            }

            DB::commit();
            return redirect()->route('admin.talent.show', $id);
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
            
            $user = User::find($id);
            $user->delete();

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'fail' => true,
                'errors' => $e,
                'pesan' => 'Error Menghapus Data',
            ]);
        }

        DB::commit();
        return redirect()->route('admin.talent.index');

    }

    public function data(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        $page = $request->page ?? 1;
        $limit = $request->limit ?? 25;
        
        $query = User::with([
            'detail',
            'lamaran' => function($q) {
                $q->with('lowongan')->latest();
            }
        ])
        ->when($request->search, function($query, $search){
            $query->where('nama', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('detail', function($q) use ($search) {
                      $q->where('nama', 'LIKE', '%' . $search . '%')
                        ->orWhere('nik', 'LIKE', '%' . $search . '%');
                  });
        })
        ->when($request->status, function($query, $status){
            if ($status !== 'all') {
                $query->whereHas('lamaran', function($q) use ($status) {
                    $q->where('status', $status);
                });
            }
        })
        ->orderBy($sort, $sortDir);

        // Always paginate the results
        $data = $query->paginate($limit, ['*'], 'page', $page);
        
        // Transform data to include application info and status labels
        $data->getCollection()->transform(function($user) {
            $activeApplication = $user->lamaran->whereNotIn('status', ['ditolak'])->first();
            $user->active_application = $activeApplication;
            $user->status = $activeApplication ? $activeApplication->status : 'none';
            $user->status_label = $this->getStatusLabel($user->status);
            
            // Use detail name if available, otherwise fallback to user name
            $user->nama = $user->detail->nama ?? $user->nama;
            
            return $user;
        });

        return response()->json($data);
    }
    
    /**
     * Get status label for display
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu Review',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            'interview' => 'Interview',
            'medical' => 'Medical Check Up',
            'pelatihan' => 'Pelatihan',
            'siap' => 'Siap Berangkat',
            'selesai' => 'Selesai',
            'none' => 'Belum Ada Lamaran'
        ];
        
        return $labels[$status] ?? $status;
    }

    /**
     * Update application status for talent
     */
    public function updateApplicationStatus($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lamaran_id' => 'required|exists:lamaran,id',
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
            $user = User::findOrFail($id);
            $lamaran = $user->lamaran()->findOrFail($request->lamaran_id);
            
            $lamaran->update([
                'status' => $request->status,
                'catatan' => $request->catatan
            ]);

            // Send notification to user
            $user->notify(new RegisterNotification($request->status, $request->catatan));
            
            return response()->json([
                'success' => true,
                'message' => 'Status lamaran berhasil diperbarui',
                'data' => $lamaran->fresh(['lowongan'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }
}
