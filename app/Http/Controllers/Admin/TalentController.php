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
            'medical' => function($query) {
                $query->latest();
            }
        ])->findOrFail($id);

        // Get related data for better UX
        $jadwalInterview = DB::table('jadwal_interview')
            ->leftJoin('admins as pewawancara', 'jadwal_interview.pewawancara_id', '=', 'pewawancara.id')
            ->leftJoin('admins as pembuat', 'jadwal_interview.dibuat_oleh', '=', 'pembuat.id')
            ->leftJoin('hasil_interview', 'jadwal_interview.id', '=', 'hasil_interview.jadwal_id')
            ->where('jadwal_interview.user_id', $id)
            ->select(
                'jadwal_interview.*',
                'pewawancara.nama as pewawancara_nama',
                'pembuat.nama as pembuat_nama',
                'hasil_interview.skor_interview',
                'hasil_interview.skor_psikotes',
                'hasil_interview.rekomendasi',
                'hasil_interview.catatan as hasil_catatan',
                'hasil_interview.tanggal_penilaian'
            )
            ->orderBy('jadwal_interview.tanggal', 'desc')
            ->get();

        $training = DB::table('training')
            ->leftJoin('training_program', 'training.program_id', '=', 'training_program.id')
            ->leftJoin('admins', 'training.didaftarkan_oleh', '=', 'admins.id')
            ->where('training.user_id', $id)
            ->select(
                'training.*',
                'training_program.nama as program_nama',
                'training_program.deskripsi as program_deskripsi',
                'training_program.durasi',
                'training_program.lokasi as program_lokasi',
                'training_program.instruktur',
                'admins.nama as pendaftar_nama'
            )
            ->orderBy('training.tanggal_daftar', 'desc')
            ->get();

        // Calculate progress and statistics
        $statusProgress = $this->calculateStatusProgress($talent->status);
        $completionStats = $this->calculateCompletionStats($talent);
        
        return Inertia::render('Talent/Show', [
            'talent' => $talent,
            'jadwalInterview' => $jadwalInterview,
            'training' => $training,
            'statusProgress' => $statusProgress,
            'completionStats' => $completionStats,
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }

    /**
     * Calculate status progress for progress bar
     */
    private function calculateStatusProgress($currentStatus)
    {
        $statuses = [
            'pending' => ['step' => 1, 'label' => 'Pendaftaran', 'color' => 'warning'],
            'diterima' => ['step' => 2, 'label' => 'Diterima', 'color' => 'success'],
            'interview' => ['step' => 3, 'label' => 'Interview', 'color' => 'info'],
            'medical' => ['step' => 4, 'label' => 'Medical Check', 'color' => 'primary'],
            'pelatihan' => ['step' => 5, 'label' => 'Pelatihan', 'color' => 'purple'],
            'siap' => ['step' => 6, 'label' => 'Siap Berangkat', 'color' => 'success'],
            'selesai' => ['step' => 7, 'label' => 'Selesai', 'color' => 'success'],
            'ditolak' => ['step' => 0, 'label' => 'Ditolak', 'color' => 'danger'],
        ];

        $current = $statuses[$currentStatus] ?? $statuses['pending'];
        $totalSteps = 7;
        $progressPercentage = $current['step'] > 0 ? ($current['step'] / $totalSteps) * 100 : 0;

        return [
            'current' => $current,
            'all' => $statuses,
            'percentage' => $progressPercentage,
            'totalSteps' => $totalSteps
        ];
    }

    /**
     * Calculate completion statistics
     */
    private function calculateCompletionStats($talent)
    {
        $requiredFields = [
            'nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'alamat', 'phone', 'nama_ayah', 'nama_ibu'
        ];

        $documentFields = [
            'ktp', 'kk', 'akte_lahir', 'ijazah', 'foto', 'paspor', 'skck'
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

        $documentsCompleted = 0;
        $documentsTotal = count($documentFields);
        
        if ($talent->detail) {
            foreach ($documentFields as $field) {
                if (!empty($talent->detail->$field)) {
                    $documentsCompleted++;
                }
            }
        }

        return [
            'profile' => [
                'completed' => $completed,
                'total' => $total,
                'percentage' => $total > 0 ? ($completed / $total) * 100 : 0
            ],
            'documents' => [
                'completed' => $documentsCompleted,
                'total' => $documentsTotal,
                'percentage' => $documentsTotal > 0 ? ($documentsCompleted / $documentsTotal) * 100 : 0
            ]
        ];
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
        
        $elq = User::with(['detail'])
        ->when($request->q, function($query, $search){
            $query->where('nama', 'LIKE', '%' . $search . '%');
        })
        ->when($request->status, function($query, $status){
            $query->where('status', $status);
        })
        ->orderBy($sort, $sortDir);

        if($request->limit){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }

        return response()->json($data);
    }

    public function state($id, Request $request)
    {
        // dd($request->all());

        $user = User::find($id);
        if($request->state == 'diterima'){
            $user->status = 'diterima';
        }else{
            $user->status = 'ditolak';
            $user->detail->update([
                'catatan' => $request->reason,
            ]);
        }
        $user->save();
        $user->notify(new RegisterNotification($request->state, $request->reason));

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
        ]);
    }
}
