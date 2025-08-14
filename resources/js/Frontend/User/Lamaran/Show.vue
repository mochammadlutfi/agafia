<template>
    <user-layout>
        <div class="align-middle border-2 border-bottom border-primary d-flex justify-content-between mb-4 py-2">
            <span class="fw-bold fs-lg">Detail Lamaran</span>
            <div>
                <el-button :tag="Link" :href="route('user.lamaran.index')" type="default">
                    <i class="fa fa-arrow-left me-2"></i>
                    Kembali
                </el-button>
                <el-button 
                    v-if="lamaran.status === 'pending'"
                    @click="cancelApplication"
                    type="danger"
                    plain
                >
                    <i class="fa fa-times me-2"></i>
                    Batalkan Lamaran
                </el-button>
            </div>
        </div>
        
            <!-- Job Information Card -->
            <el-card class="job-info-card mb-4">
                <div class="job-info-content">
                    <div class="job-details">
                        <h2 class="job-title">{{ lamaran.lowongan?.posisi }}</h2>
                        <div class="job-meta">
                            <div class="meta-item">
                                <i class="fa fa-building text-primary me-2"></i>
                                <span>{{ lamaran.lowongan?.perusahaan }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fa fa-map-marker-alt text-success me-2"></i>
                                <span>{{ lamaran.lowongan?.lokasi }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fa fa-calendar text-info me-2"></i>
                                <span>Dilamar pada {{ format_date(lamaran.tanggal_lamaran) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="job-status">
                        <el-tag 
                            :type="getStatusType(lamaran.status)" 
                            size="large"
                            class="status-tag"
                        >
                            {{ getStatusLabel(lamaran.status) }}
                        </el-tag>
                    </div>
                </div>
            </el-card>

            <!-- Progress Steps -->
            <el-card class="progress-card mb-4">
                <template #header>
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-tasks me-2"></i>
                            Progress Lamaran
                        </h3>
                        <span class="progress-percentage">{{ progress.percentage }}%</span>
                    </div>
                </template>
                
                <div class="steps-container">
                    <el-steps 
                        :active="getActiveStep()" 
                        :process-status="getProcessStatus()"
                        direction="horizontal"
                        align-center
                    >
                        <el-step 
                            title="Verifikasi" 
                            description="Lamaran sedang diverifikasi"
                        />
                        <el-step 
                            title="Interview" 
                            description="Proses wawancara dan test psikologi"
                        />
                        <el-step 
                            title="Medical Check" 
                            description="Pemeriksaan kesehatan"
                        />
                        <el-step 
                            title="Training" 
                            description="Pelatihan dan orientasi"
                        />
                        <el-step 
                            title="Upload Dokumen" 
                            description="Kelengkapan dokumen keberangkatan"
                        />
                        <el-step 
                            title="Selesai" 
                            description="Siap untuk penempatan kerja"
                        />
                    </el-steps>
                </div>

                <!-- Current Step Details -->
                <div class="current-step-details mt-4">
                    <el-alert
                        :title="getCurrentStepTitle()"
                        :type="getCurrentStepType()"
                        show-icon
                        :closable="false"
                    >

                    <template #default>
                        {{ getCurrentStepDescription() }}
                        <div>
                            {{ lamaran.catatan || 'Tidak ada catatan' }}
                        </div>
                        </template> 
                    </el-alert>
                </div>
            </el-card>

            <!-- Application Information -->
            <el-card class="mb-4 rounded">
                <template #header>
                    <div class="fs-4">
                        <i class="fa fa-info-circle me-2"></i>
                        Informasi Lamaran
                    </div>
                </template>
                
                <el-descriptions :column="1" border>
                    <el-descriptions-item label="ID Lamaran">
                        #{{ lamaran.id }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Tanggal Lamaran">
                        {{ format_date(lamaran.tanggal_lamaran) }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Status">
                        <el-tag :type="getStatusType(lamaran.status)">
                            {{ getStatusLabel(lamaran.status) }}
                        </el-tag>
                    </el-descriptions-item>
                    <el-descriptions-item label="Terakhir Diperbarui">
                        {{ format_date(lamaran.updated_at) }}
                    </el-descriptions-item>
                    <el-descriptions-item label="CV" v-if="lamaran.cv_file">
                        <el-button 
                            :tag="'a'"
                            :href="'/storage/' + lamaran.cv_file"
                            target="_blank"
                            type="primary"
                            size="small"
                        >
                            <i class="fa fa-download me-2"></i>
                            Download CV
                        </el-button>
                    </el-descriptions-item>
                    <el-descriptions-item label="Catatan" v-if="lamaran.catatan">
                        {{ lamaran.catatan }}
                    </el-descriptions-item>
                </el-descriptions>
            </el-card>

            <!-- Interview Schedule (if exists) -->
            <el-card v-if="lamaran.interview" class="mb-4 rounded">
                <template #header>
                    <div class="fs-4">
                        <i class="fa fa-calendar me-2"></i>
                        Jadwal Interview
                    </div>
                </template>
                
                <div class="interview-list">
                    <div class="interview-item">
                        <div class="interview-header">
                            <h4>Interview #{{ lamaran.interview.id }}</h4>
                            <el-tag 
                                :type="lamaran.interview.status === 'selesai' ? 'success' : 'warning'"
                                size="small"
                            >
                                {{ lamaran.interview.status === 'selesai' ? 'Selesai' : 'Terjadwal' }}
                            </el-tag>
                        </div>
                        <div class="interview-details">
                            <p><strong>Tanggal:</strong> {{ format_date(lamaran.interview.tanggal) }}</p>
                            <p><strong>Waktu:</strong> {{ format_time(lamaran.interview.waktu) }}</p>
                            <p><strong>Lokasi:</strong> {{ lamaran.interview.lokasi || 'Online' }}</p>
                            <p v-if="lamaran.interview.catatan"><strong>Catatan:</strong> {{ lamaran.interview.catatan }}</p>
                        </div>
                    </div>
                </div>

                
                <el-descriptions title="Hasil Interview" :column="2" border direction="horizontal" v-if="lamaran.interview.status == 'selesai'">
                    <el-descriptions-item label="Skor Interview">
                        {{ lamaran.interview.skor_interview ?? '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Skor Psikotes">
                        {{ lamaran.interview.skor_psikotes ?? '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Kemampuan Komunikasi">
                        {{ lamaran.interview.kemampuan_komunikasi ?? '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Kemampuan Teknis">
                        {{ lamaran.interview.kemampuan_teknis ?? '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Rekomendasi">
                        {{ lamaran.interview.rekomendasi ?? '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Penilaian Kepribadian">
                        {{ lamaran.interview.kepribadian ?? '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Catatan">
                        {{ lamaran.interview.catatan_hasil ?? '-' }}
                    </el-descriptions-item>
                </el-descriptions>
            </el-card> 

            <el-card v-if="getActiveStep() > 0" class="mb-4 rounded">
                <template #header>
                    <div class="fs-4">
                        <i class="fa fa-calendar me-2"></i>
                        Medical Check Up
                    </div>
                </template>
                <div v-if="lamaran.medical">
                    <el-descriptions :column="2" border direction="horizontal">
                        <el-descriptions-item label="Nama Fasilitas Kesehatan">
                            {{ lamaran.medical.nama }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Tanggal Pengecekan">
                            {{ format_date(lamaran.medical.tanggal) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Hasil">
                            {{ lamaran.medical.hasil }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Dokumen File">
                            <span v-if="!lamaran.medical.file">-</span>
                            <el-button v-else tag="a" :href="'/uploads/'+lamaran.medical.file" target="_blank" type="primary" size="small">
                                <i class="fa fa-file-pdf-o"></i> Lihat
                            </el-button>                        
                        </el-descriptions-item>
                        <el-descriptions-item label="Status">
                            <el-tag :type="getMedicalStatus(lamaran.medical.status)" size="small">
                                {{ lamaran.medical.status_label }}
                            </el-tag>
                        </el-descriptions-item>
                        <el-descriptions-item label="Catatan" :span="2" label-position="top">
                            {{ lamaran.medical.catatan }}
                        </el-descriptions-item>
                    </el-descriptions>
                </div>
                <el-empty v-else>
                    <el-button 
                        @click="showMedicalModal = true"
                        type="primary">
                        Upload Medical Checkup
                    </el-button>
                </el-empty>
            </el-card>
            
            <el-card v-if="lamaran.training.length" class="mb-4 rounded">
                <template #header>
                    <div class="fs-4">
                        <i class="fa fa-chalkboard-teacher me-2"></i>
                        Training
                    </div>
                </template>
                
                <el-table 
                    :data="lamaran.training || []" 
                    border 
                    style="width: 100%"
                    :empty-text="'Belum ada data pelatihan'"
                >
                    <el-table-column prop="program.nama" label="Program" />
                    <el-table-column prop="tanggal_daftar" label="Tanggal Daftar" width="120">
                        <template #default="{ row }">
                            {{ format_date(row.tanggal_daftar) }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="status" label="Status" width="150">
                        <template #default="{ row }">
                            <el-tag :type="getStatusType(row.status)" size="small">
                                {{ getStatusLabel(row.status) }}
                            </el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column label="Aksi" width="120">
                        <template #default="{ row }">
                            <el-button :tag="Link" :href="route('user.training.show', row.id)"
                                type="primary" 
                                size="small"
                            >
                                Detail
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
                
            </el-card>
            
            <el-card class="mb-4 rounded">
                <template #header>
                    <div class="fs-4">
                        <i class="fa fa-file me-2"></i>
                        Dokumen Kelengkapan
                    </div>
                </template>

                
                <div v-if="lamaran.dokumen">
                    <!-- Document Status Overview -->
                    <el-card class="mb-4">
                        <template #header>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Status Review Dokumen</span>
                            </div>
                        </template>
                        <el-row :gutter="16">
                            <el-col :span="8">
                                <el-statistic title="Status" :value="lamaran.dokumen.status_label">
                                    <template #suffix>
                                        <el-tag :type="lamaran.dokumen.status_color" size="small" class="ms-2">
                                            {{ lamaran.dokumen.status_label }}
                                        </el-tag>
                                    </template>
                                </el-statistic>
                            </el-col>
                            <el-col :span="16">
                                <div>
                                    <strong>Catatan Review:</strong>
                                    <p class="mt-1 mb-0 text-muted">{{ lamaran.dokumen.catatan || 'Belum ada catatan review' }}</p>
                                </div>
                            </el-col>
                        </el-row>
                    </el-card>
                    
                    <!-- Document List -->
                    <el-descriptions :column="2" border direction="horizontal">
                        <el-descriptions-item :label="doc.label" v-for="(doc, index) in documentTableData" :key="index">
                            <el-button 
                                v-if="doc.file"
                                tag="a" 
                                :href="'/uploads/' + doc.file" 
                                target="_blank" 
                                type="primary" 
                                size="small"
                            >
                                <i class="fa fa-eye me-1"></i>
                                Lihat
                            </el-button>
                            <span v-else class="text-muted">-</span>
                        </el-descriptions-item>
                    </el-descriptions>
                </div>
                <el-empty v-else>
                    <el-button 
                        @click="openDocumentModal"
                        type="primary">
                        Upload Dokumen
                    </el-button>
                </el-empty>
            </el-card>
            <!-- Job Details -->
            <el-card class="mb-4 rounded">
                <template #header>
                    <div class="fs-4">
                        <i class="fa fa-briefcase me-2"></i>
                        Detail Pekerjaan
                    </div>
                </template>
                
                <el-descriptions :column="1" border>
                    <el-descriptions-item label="Posisi">
                        {{ lamaran.lowongan?.posisi }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Perusahaan">
                        {{ lamaran.lowongan?.perusahaan }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Lokasi">
                        {{ lamaran.lowongan?.lokasi }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Negara Tujuan">
                        {{ lamaran.lowongan?.negara_tujuan || '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Gaji">
                        {{ formatCurrency(lamaran.lowongan?.gaji) }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Skill Required">
                        {{ lamaran.lowongan?.skill || '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Kuota">
                        {{ lamaran.lowongan?.kuota }} Posisi
                    </el-descriptions-item>
                </el-descriptions>
            </el-card>

            <!-- Next Actions -->
            <el-card>
                <template #header>
                    <h3>
                        <i class="fa fa-clipboard-list me-2"></i>
                        Langkah Selanjutnya
                    </h3>
                </template>
                
                <div class="next-actions">
                    <div v-if="lamaran.status === 'pending'" class="action-item">
                        <el-alert
                            title="Menunggu Verifikasi"
                            description="Lamaran Anda sedang diverifikasi. Anda akan dihubungi jika lolos tahap ini."
                            type="info"
                            :closable="false"
                        />
                    </div>
                    
                    <div v-else-if="lamaran.status === 'diterima'" class="action-item">
                        <el-alert
                            title="Persiapkan Interview"
                            description="Lamaran Anda diterima! Silakan persiapkan diri untuk tahap interview. Tim Kami akan menghubungi Anda segera."
                            type="success"
                            :closable="false"
                        />
                    </div>
                    
                    <div v-else-if="lamaran.status === 'interview'" class="action-item">
                        <el-alert
                            title="Tahap Interview"
                            description="Pastikan Anda hadir sesuai jadwal interview yang telah ditentukan."
                            type="warning"
                            :closable="false"
                        />
                    </div>
                    
                    <div v-else-if="lamaran.status === 'medical'" class="action-item">
                        <el-alert
                            title="Medical Check Up"
                            description="Lakukan pemeriksaan kesehatan di klinik yang telah ditentukan."
                            type="info"
                            :closable="false"
                        />
                    </div>
                    
                    <div v-else-if="lamaran.status === 'pelatihan'" class="action-item">
                        <el-alert
                            title="Pelatihan"
                            description="Ikuti program pelatihan dan orientasi sesuai jadwal yang diberikan."
                            type="warning"
                            :closable="false"
                        />
                    </div>
                    
                    <div v-else-if="lamaran.status === 'siap'" class="action-item">
                        <el-alert
                            title="Upload Dokumen"
                            description="Upload dokumen kelengkapan untuk keberangkatan."
                            type="info"
                            :closable="false"
                        />
                    </div>
                    
                    <div v-else-if="lamaran.status === 'selesai'" class="action-item">
                        <el-alert
                            title="Selamat!"
                            description="Proses lamaran telah selesai. Anda siap untuk penempatan kerja."
                            type="success"
                            :closable="false"
                        />
                    </div>
                    
                    <div v-else-if="lamaran.status === 'ditolak'" class="action-item">
                        <el-alert
                            title="Lamaran Ditolak"
                            description="Maaf, lamaran Anda tidak dapat diproses lebih lanjut. Jangan menyerah, coba lamar posisi lain yang sesuai."
                            type="error"
                            :closable="false"
                        />
                        <el-button 
                            :tag="Link" 
                            :href="route('lowongan')"
                            type="primary"
                            class="mt-3"
                        >
                            <i class="fa fa-search me-2"></i>
                            Cari Lowongan Lain
                        </el-button>
                    </div>
                </div>
            </el-card>
        <el-dialog
            v-model="showMedicalModal"
            title="Medical Check Up"
            width="600px"
            :before-close="handleMedicalModalClose"
        >
            <el-form :model="medicalForm" :rules="medicalRules" ref="medicalFormRef" label-position="top" label-width="150px">
                <el-form-item label="Tanggal Medical" prop="tanggal">
                    <el-date-picker
                        v-model="medicalForm.tanggal"
                        type="date"
                        placeholder="Pilih tanggal"
                        format="DD/MM/YYYY"
                        value-format="YYYY-MM-DD"
                        style="width: 100%"
                    />
                </el-form-item>
                <el-form-item label="Nama Fasilitas Kesehatan" prop="nama">
                    <el-input v-model="medicalForm.nama" placeholder="Nama Fasilitas Kesehatan (misal:Rumah Sakit Bunga, dll)" />
                </el-form-item>
                <el-form-item label="Upload File" prop="file">
                    <single-file-upload v-model="medicalForm.file"/>
                </el-form-item>
                <el-form-item label="Hasil" prop="hasil">
                    <el-input v-model="medicalForm.hasil" placeholder="Hasil medical check" />
                </el-form-item>
                <el-form-item label="Catatan">
                    <el-input
                        v-model="medicalForm.catatan"
                        type="textarea"
                        :rows="3"
                        placeholder="Catatan tambahan (opsional)"
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showMedicalModal = false">Batal</el-button>
                    <el-button type="info" @click="submitMedicalForm" :loading="medicalLoading">
                        Simpan
                    </el-button>
                </span>
            </template>
        </el-dialog>

        
        <el-dialog
            v-model="showDokumenModal"
            title="Upload Dokumen"
            width="800px"
            :before-close="handleDokumenModalClose">
            <el-form label-position="top" :model="dokumenForm" label-width="100%" :rules="dokumenRules" ref="dokumenFormRef">
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="KTP" prop="ktp">
                            <single-file-upload v-model="dokumenForm.ktp" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Kartu Keluarga" prop="kk">
                            <single-file-upload v-model="dokumenForm.kk" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Akte Kelahiran" prop="akte_lahir">
                            <single-file-upload v-model="dokumenForm.akte_lahir" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Buku Nikah (Jika ada)" prop="buku_nikah">
                            <single-file-upload v-model="dokumenForm.buku_nikah" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Surat Keterangan Sehat" prop="surat_keterangan_sehat">
                            <single-file-upload v-model="dokumenForm.surat_keterangan_sehat" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Surat Izin Keluarga" prop="surat_izin_keluarga">
                            <single-file-upload v-model="dokumenForm.surat_izin_keluarga" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Ijazah Terakhir" prop="ijazah">
                            <single-file-upload v-model="dokumenForm.ijazah" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Sertifikat Keahlian/ Kompetensi" prop="sertifikat_keahlian">
                            <single-file-upload v-model="dokumenForm.sertifikat_keahlian" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Paspor" prop="paspor">
                            <single-file-upload v-model="dokumenForm.paspor" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Surat Keterangan Pengalaman Kerja" prop="surat_pengalaman">
                            <single-file-upload v-model="dokumenForm.surat_pengalaman" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="SKCK" prop="skck">
                            <single-file-upload v-model="dokumenForm.skck" />
                        </el-form-item>
                    </el-col>
                </el-row>
                
                <div class="text-end">
                    <el-button @click="showDokumenModal = false">Cancel</el-button>
                    <el-button type="success" @click="submitDokumen" :loading="dokumenLoading">
                        Simpan
                    </el-button>
                </div>
            </el-form>
        </el-dialog>
    </user-layout>
</template>

<script setup>
import { computed, ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { ElMessage, ElMessageBox, ElLoading } from 'element-plus'
import SingleFileUpload from '@/Components/SingleFileUpload.vue';
import moment from 'moment';

const props = defineProps({
    lamaran: Object,
    progress: Object
})


const showMedicalModal = ref(false)
const showDokumenModal = ref(false)

const medicalLoading = ref(false)
const dokumenLoading = ref(false)

const medicalFormRef = ref()
const dokumenFormRef = ref()

const dokumenForm = reactive({
    lamaran_id: props.lamaran.id,
    ktp: null,
    kk: null,
    akte_lahir: null,
    buku_nikah: null,
    surat_keterangan_sehat: null,
    surat_izin_keluarga: null,
    ijazah: null,
    sertifikat_keahlian: null,
    paspor: null,
    surat_pengalaman: null,
    skck: null
})


const dokumenRules = {
    ktp: [{ required: true, message: 'KTP wajib diisi', trigger: 'change' }],
    kk: [{ required: true, message: 'Kartu keluarga wajib diisi', trigger: 'change' }],
    akte_lahir: [{ required: true, message: 'Akte lahir wajib diisi', trigger: 'change' }],
    surat_keterangan_sehat: [{ required: true, message: 'Surat keterangan sehat wajib diisi', trigger: 'change' }],
    surat_izin_keluarga: [{ required: true, message: 'Surat izin keluarga wajib diisi', trigger: 'change' }],
    ijazah: [{ required: true, message: 'Ijazah wajib diisi', trigger: 'change' }],
    sertifikat_keahlian: [{ required: false, message: 'Sertifikat keahlian wajib diisi', trigger: 'change' }],
    paspor: [{ required: true, message: 'Paspor wajib diisi', trigger: 'change' }],
    surat_pengalaman: [{ required: false, message: 'Surat pengalaman kerja wajib diisi', trigger: 'change' }],
    skck: [{ required: true, message: 'SKCK wajib diisi', trigger: 'change' }],
}

// Medical Form
const medicalForm = reactive({
    lamaran_id : props.lamaran.id,
    tanggal: '',
    nama: '',
    hasil: '',
    file: null,
    catatan: '',
    status: 'pending'
})

const medicalRules = {
    tanggal: [{ required: true, message: 'Tanggal medical wajib diisi', trigger: 'blur' }],
    nama: [{ required: true, message: 'Nama Faskes wajib diisi', trigger: 'blur' }],
    file: [{ required: true, message: 'File wajib diupload', trigger: 'change' }],
    hasil: [{ required: true, message: 'Hasil wajib diisi', trigger: 'blur' }]
}
// Status configurations
const statusLabels = {
    'pending': 'Menunggu Review',
    'diterima': 'Diterima',
    'ditolak': 'Ditolak',
    'interview': 'Interview',
    'medical': 'Medical Check',
    'pelatihan': 'Pelatihan',
    'siap': 'Siap Berangkat',
    'selesai': 'Selesai'
}

const statusTypes = {
    'pending': 'warning',
    'diterima': 'success',
    'ditolak': 'danger',
    'interview': 'primary',
    'medical': 'info',
    'pelatihan': '',
    'siap': 'success',
    'selesai': 'success'
}

// Helper functions
const getStatusLabel = (status) => statusLabels[status] || status
const getStatusType = (status) => statusTypes[status] || ''


const documentTableData = computed(() => {
    if (!props.lamaran.dokumen) return []
    
    return [
        { field: 'ktp', label: 'KTP', file: props.lamaran.dokumen.ktp },
        { field: 'kk', label: 'Kartu Keluarga', file: props.lamaran.dokumen.kk },
        { field: 'akte_lahir', label: 'Akte Kelahiran', file: props.lamaran.dokumen.akte_lahir },
        { field: 'buku_nikah', label: 'Buku Nikah', file: props.lamaran.dokumen.buku_nikah },
        { field: 'surat_keterangan_sehat', label: 'Surat Keterangan Sehat', file: props.lamaran.dokumen.surat_keterangan_sehat },
        { field: 'surat_izin_keluarga', label: 'Surat Izin Keluarga', file: props.lamaran.dokumen.surat_izin_keluarga },
        { field: 'ijazah', label: 'Ijazah Terakhir', file: props.lamaran.dokumen.ijazah },
        { field: 'sertifikat_keahlian', label: 'Sertifikat Keahlian', file: props.lamaran.dokumen.sertifikat_keahlian },
        { field: 'paspor', label: 'Paspor', file: props.lamaran.dokumen.paspor },
        { field: 'surat_pengalaman', label: 'Surat Pengalaman Kerja', file: props.lamaran.dokumen.surat_pengalaman },
        { field: 'skck', label: 'SKCK', file: props.lamaran.dokumen.skck },
    ]
})

const formatCurrency = (amount) => {
    if (!amount) return '-'
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount)
}

// Step functions
const getActiveStep = () => {
    const stepMapping = {
        'pending': 0,
        'diterima': 1,
        'interview': 1,
        'medical': 2,
        'pelatihan': 3,
        'siap': 4,
        'selesai': 5,
        'ditolak': 0
    }
    return stepMapping[props.lamaran.status] || 0
}

const getProcessStatus = () => {
    if (props.lamaran.status === 'ditolak') return 'error'
    if (props.lamaran.status === 'selesai') return 'success'
    return 'process'
}


const getCurrentStepTitle = () => {
    const titles = {
        'pending': 'Tahap Verifikasi',
        'diterima': 'Persiapan Interview',
        'interview': 'Proses Interview',
        'medical': 'Medical Check Up',
        'pelatihan': 'Program Pelatihan',
        'siap': 'Upload Dokumen Kelengkapan',
        'selesai': 'Proses Selesai',
        'ditolak': 'Lamaran Ditolak'
    }
    return titles[props.lamaran.status] || 'Status Tidak Diketahui'
}

const format_date = (value) => {
  if (value) {
    return moment(String(value)).format('DD MMM YYYY');
  }
}

const format_time = (value) => {
  if (value) {
    return moment(value, "HH:mm:ss").format('HH:mm')
  }
}

const getMedicalStatus = (status) => {
  switch (status) {
    case 'pending':
      return 'warning';
    case 'valid':
      return 'success';
    default:
      return 'danger';
  }
};

const getCurrentStepDescription = () => {
    const descriptions = {
        'pending': 'Lamaran Anda sedang dalam tahap verifikasi. Mohon bersabar menunggu.',
        'diterima': 'Selamat! Lamaran Anda diterima. Kami akan menghubungi untuk jadwal interview.',
        'interview': 'Anda sedang dalam tahap interview. Pastikan persiapan Anda optimal.',
        'medical': 'Lakukan pemeriksaan kesehatan di fasilitas yang telah ditentukan.',
        'pelatihan': 'Ikuti program pelatihan sesuai jadwal yang telah diberikan.',
        'siap': 'Upload dokumen kelengkapan untuk proses keberangkatan.',
        'selesai': 'Selamat! Semua tahap telah selesai. Anda siap untuk penempatan kerja.',
        'ditolak': 'Lamaran tidak dapat diproses lebih lanjut.'
    }
    return descriptions[props.lamaran.status] || 'Tidak ada deskripsi tersedia.'
}

const getCurrentStepType = () => {
    const types = {
        'pending': 'info',
        'diterima': 'success',
        'interview': 'warning',
        'medical': 'info',
        'pelatihan': 'warning',
        'siap': 'info',
        'selesai': 'success',
        'ditolak': 'error'
    }
    return types[props.lamaran.status] || 'info'
}

// Event handlers
const cancelApplication = async () => {
    try {
        await ElMessageBox.confirm(
            'Apakah Anda yakin ingin membatalkan lamaran ini?',
            'Konfirmasi Pembatalan',
            {
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak',
                type: 'warning'
            }
        )

        router.post(route('user.lamaran.cancel', props.lamaran.id), {}, {
            onSuccess: () => {
                ElMessage.success('Lamaran berhasil dibatalkan')
                router.visit(route('user.lamaran.index'))
            },
            onError: () => {
                ElMessage.error('Gagal membatalkan lamaran')
            }
        })
    } catch (error) {
        // User cancelled
    }
}


const submitMedicalForm = async () => {
    if (!medicalFormRef.value) return
    
    await medicalFormRef.value.validate((valid) => {
        if (valid) {
            medicalLoading.value = true;
            
            try {
                medicalForm.lamaran_id = props.lamaran.id
                
                axios.post(route('user.lamaran.medical'), medicalForm,{
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                })
                
                ElMessage.success('Medical check up berhasil dijadwalkan')
                showMedicalModal.value = false
                
                // Refresh page data
                router.visit(route('user.lamaran.show', props.lamaran.id), {
                    preserveState: false,
                    preserveScroll: true
                })
            } catch (error) {
                console.error('Error scheduling medical:', error)
                ElMessage.error('Gagal menjadwalkan medical check up')
            } finally {
                medicalLoading.value = false
            }
        }
    })
}

const handleDokumenModalClose = (done) => {
    dokumenFormRef.value?.resetFields()
    Object.keys(dokumenForm).forEach(key => {
        if (key !== 'lamaran_id') {
            dokumenForm[key] = null
        }
    })
    done()
}

const openDocumentModal = () => {
    if (props.dokumen) {
        // Pre-fill form with existing data
        Object.keys(dokumenForm).forEach(key => {
            if (props.dokumen[key] && key !== 'lamaran_id') {
                dokumenForm[key] = props.dokumen[key]
            }
        })
    }
    showDokumenModal.value = true
}

// Form submissions
const submitDokumen = async () => {
    if (!dokumenFormRef.value) return
    
    await dokumenFormRef.value.validate((valid) => {
        if (valid) {
            uploadDokumen()
        }
    })
}

const uploadDokumen = async () => {
    dokumenLoading.value = true
    
    try {
        const formData = new FormData()
        
        Object.keys(dokumenForm).forEach(key => {
            if (dokumenForm[key] !== null) {
                formData.append(key, dokumenForm[key])
            }
        })
        
        await axios.post(route('user.lamaran.document'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        
        ElMessage.success('Dokumen berhasil diupload')
        showDokumenModal.value = false
        
        // Refresh page data
        router.visit(route('user.lamaran.show', props.lamaran.id), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error uploading documents:', error)
        ElMessage.error('Gagal mengupload dokumen')
    } finally {
        dokumenLoading.value = false
    }
}

</script>

<style scoped>
.lamaran-detail-container {
    padding: 24px;
    background: #f5f7fa;
    min-height: 100vh;
}

.header-section {
    margin-bottom: 32px;
}

.page-title {
    font-size: 32px;
    font-weight: 700;
    color: #303133;
    margin-bottom: 8px;
}

.page-subtitle {
    font-size: 16px;
    color: #606266;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 12px;
}

.job-info-card {
    border-radius: 12px;
    overflow: hidden;
}

.job-info-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.job-title {
    font-size: 24px;
    font-weight: 600;
    color: #303133;
    margin-bottom: 12px;
}

.job-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.meta-item {
    display: flex;
    align-items: center;
    color: #606266;
    font-size: 14px;
}

.status-tag {
    font-weight: 600;
    font-size: 16px;
    padding: 8px 16px;
}

.progress-card {
    border-radius: 12px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    color: #303133;
    font-weight: 600;
}

.progress-percentage {
    font-size: 18px;
    font-weight: 700;
    color: #409eff;
}

.steps-container {
    margin: 24px 0;
}

:deep(.el-steps) {
    margin: 20px 0;
}

:deep(.el-step__title) {
    font-weight: 600;
    font-size: 14px;
}

:deep(.el-step__description) {
    font-size: 12px;
    margin-top: 4px;
}

.current-step-details {
    border-top: 1px solid #ebeef5;
    padding-top: 20px;
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

.left-column,
.right-column {
    display: flex;
    flex-direction: column;
}

:deep(.el-card) {
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

:deep(.el-card__header) {
    background: #f8f9fa;
    border-bottom: 1px solid #ebeef5;
}

:deep(.el-card__header h3) {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #303133;
}

.interview-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.interview-item {
    border: 1px solid #ebeef5;
    border-radius: 8px;
    padding: 16px;
    background: #f8f9fa;
}

.interview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.interview-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #303133;
}

.interview-details p {
    margin: 4px 0;
    font-size: 14px;
    color: #606266;
}

.next-actions {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.action-item {
    padding: 0;
}

:deep(.el-alert) {
    border-radius: 8px;
}

:deep(.el-descriptions) {
    margin-top: 0;
}

:deep(.el-descriptions__label) {
    font-weight: 600;
    color: #606266;
}

:deep(.el-descriptions__content) {
    color: #303133;
}

@media (max-width: 1200px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .lamaran-detail-container {
        padding: 16px;
    }
    
    .header-section .d-flex {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }
    
    .header-actions {
        justify-content: stretch;
    }
    
    .header-actions .el-button {
        flex: 1;
    }
    
    .job-info-content {
        flex-direction: column;
        gap: 16px;
    }
    
    .job-meta {
        flex-direction: column;
    }
    
    :deep(.el-steps) {
        flex-direction: column;
        align-items: stretch;
    }
    
    :deep(.el-step) {
        flex-direction: row;
        margin-bottom: 20px;
    }
}
</style>