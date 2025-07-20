<template>
    <base-layout title="Detail Lamaran">
        <div class="content">
            <!-- Header -->
            <div class="content-heading d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-0">Detail Lamaran #{{ lamaran?.id }}</h2>
                    <small class="text-muted">{{ lamaran?.user?.detail?.nama || lamaran?.user?.nama }}</small>
                </div>
                <div class="space-x-1">
                    <el-button :tag="Link" :href="route('admin.lamaran.index')" type="default">
                        <i class="fa fa-arrow-left me-1"></i>
                        Kembali
                    </el-button>
                    <el-dropdown trigger="click" v-if="lamaran?.status !== 'selesai'">
                        <el-button type="warning">
                            <i class="fa fa-exchange me-1"></i>
                            Ubah Status
                            <i class="fa fa-chevron-down ms-1"></i>
                        </el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item
                                    v-for="(label, status) in statusOptions"
                                    :key="status"
                                    @click="updateStatus(status)"
                                    :disabled="status === lamaran?.status"
                                >
                                    {{ label }}
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
            </div>

            <!-- Application Status Card -->
            <div class="block block-rounded mb-4">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-info-circle me-2"></i>
                        Status Lamaran
                    </h3>
                    <div class="block-options">
                        <el-tag :type="getStatusType(lamaran?.status)" size="large">
                            {{ getStatusLabel(lamaran?.status) }}
                        </el-tag>
                    </div>
                </div>
                <div class="block-content  p-4">
                    <!-- Progress Steps -->
                    <el-steps 
                        :active="getProgressStep(lamaran?.status)" 
                        :process-status="lamaran?.status === 'ditolak' ? 'error' : 'process'"
                        align-center
                        class="mb-4"
                    >
                        <el-step title="Lamaran Dikirim" />
                        <el-step title="Review" />
                        <el-step title="Interview" />
                        <el-step title="Medical Check" />
                        <el-step title="Pelatihan" />
                        <el-step title="Siap Berangkat" />
                        <el-step title="Selesai" />
                    </el-steps>

                    <!-- Rejection Alert -->
                    <el-alert
                        v-if="lamaran?.status === 'ditolak'"
                        title="Lamaran Ditolak"
                        type="error"
                        :description="lamaran?.catatan"
                        show-icon
                        :closable="false"
                        class="mb-4"
                    />

                    <!-- Application Details -->
                    <el-row :gutter="16">
                        <el-col :span="12">
                            <el-descriptions title="Detail Lamaran" :column="1" border>
                                <el-descriptions-item label="Tanggal Lamaran">
                                    {{ format_date(lamaran?.tanggal_lamaran) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Status">
                                    <el-tag :type="getStatusType(lamaran?.status)">
                                        {{ getStatusLabel(lamaran?.status) }}
                                    </el-tag>
                                </el-descriptions-item>
                                <el-descriptions-item label="CV File" v-if="lamaran?.cv_file">
                                    <el-button 
                                        tag="a" 
                                        :href="`/storage/${lamaran.cv_file}`" 
                                        target="_blank" 
                                        type="primary" 
                                        size="small"
                                    >
                                        <i class="fa fa-download me-1"></i>
                                        Download CV
                                    </el-button>
                                </el-descriptions-item>
                                <el-descriptions-item label="Catatan" v-if="lamaran?.catatan">
                                    {{ lamaran.catatan }}
                                </el-descriptions-item>
                            </el-descriptions>
                        </el-col>
                        <el-col :span="12">
                            <el-descriptions title="Informasi Lowongan" :column="1" border>
                                <el-descriptions-item label="Posisi">
                                    {{ lamaran?.lowongan?.posisi }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Perusahaan">
                                    {{ lamaran?.lowongan?.perusahaan }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Status Lowongan">
                                    <el-tag :type="lamaran?.lowongan?.status === 'buka' ? 'success' : 'danger'" size="small">
                                        {{ lamaran?.lowongan?.status === 'buka' ? 'Buka' : 'Tutup' }}
                                    </el-tag>
                                </el-descriptions-item>
                            </el-descriptions>
                        </el-col>
                    </el-row>
                </div>
            </div>

            <!-- Tabs Content -->
            <div class="block block-rounded">
                <div class="block-content p-0">
                    <el-tabs v-model="activeTab" class="demo-tabs p-2">
                        <!-- Applicant Information -->
                        <el-tab-pane label="Informasi Pelamar" name="applicant">
                            <div class="p-0">
                                <el-row :gutter="24">
                                    <el-col :span="16">
                                        <el-descriptions title="Data Pribadi" :column="1" border>
                                            <el-descriptions-item label="Nama Lengkap">
                                                {{ lamaran?.user?.detail?.nama || lamaran?.user?.nama }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Email">
                                                <el-link type="primary" :href="`mailto:${lamaran?.user?.email}`">
                                                    {{ lamaran?.user?.email }}
                                                </el-link>
                                            </el-descriptions-item>
                                            <el-descriptions-item label="NIK">
                                                {{ lamaran?.user?.detail?.nik || '-' }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="No. Handphone">
                                                <el-link type="primary" :href="`tel:${lamaran?.user?.detail?.phone}`">
                                                    {{ lamaran?.user?.detail?.phone || '-' }}
                                                </el-link>
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Tempat, Tanggal Lahir">
                                                {{ lamaran?.user?.detail?.tempat_lahir || '-' }}, {{ format_date(lamaran?.user?.detail?.tanggal_lahir) }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Jenis Kelamin">
                                                {{ lamaran?.user?.detail?.jenis_kelamin || '-' }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Alamat">
                                                {{ lamaran?.user?.detail?.alamat || '-' }}
                                            </el-descriptions-item>
                                        </el-descriptions>
                                    </el-col>
                                    <el-col :span="8">
                                        <div class="text-center">
                                            <el-image
                                                style="width: 200px; height: 250px"
                                                :src="lamaran?.user?.detail?.foto ? lamaran.user.detail.foto : '/images/placeholder.png'"
                                                fit="cover"
                                                :preview-src-list="[lamaran?.user?.detail?.foto ? `/storage/${lamaran.user.detail.foto}` : '/images/placeholder.png']"
                                            />
                                            <p class="mt-2 text-muted">Foto Pelamar</p>
                                        </div>
                                    </el-col>
                                </el-row>
                                
                                <el-button 
                                    :tag="Link" 
                                    :href="route('admin.talent.show', lamaran?.user?.id)" 
                                    type="primary"
                                    class="mt-3"
                                >
                                    <i class="fa fa-user me-1"></i>
                                    Lihat Profil Lengkap
                                </el-button>
                            </div>
                        </el-tab-pane>

                        <!-- Interview History -->
                        <el-tab-pane label="Interview" name="interview">
                            <InterviewSection
                                :interview="lamaran.interview"
                                :can-schedule="['diterima', 'interview'].includes(lamaran?.status)"
                                :lamaran-id="lamaran.id"
                            />
                        </el-tab-pane>

                        <!-- MCU -->
                        <el-tab-pane label="Medical Checkup" name="medical">
                            <MedicalSection
                                :medical="lamaran.medical"
                                :can-schedule="['medical', 'pelatihan'].includes(lamaran?.status)"
                                :lamaran-id="lamaran.id"
                                :user-id="lamaran.user_id"
                            />
                        </el-tab-pane>


                        <!-- Training History -->
                        <el-tab-pane label="Pelatihan" name="training">
                            <TrainingSection
                                :training="lamaran?.training"
                                :can-schedule="['medical', 'pelatihan'].includes(lamaran?.status)"
                                :lamaran-id="lamaran.id"
                                @view-training="(id) => router.visit(route('admin.training.show', id))"
                            />
                        </el-tab-pane>

                        <!-- Documents -->
                        <el-tab-pane label="Dokumen" name="documents">
                            <DocumentSection
                                :dokumen="lamaran.dokumen"
                                :lamaran-id="lamaran.id"
                            />
                        </el-tab-pane>
                    </el-tabs>
                </div>
            </div>
        </div>


    </base-layout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { ElMessage, ElMessageBox, ElLoading } from 'element-plus'
import axios from 'axios'
import moment from 'moment'
import InterviewSection from '@/Components/Admin/InterviewSection.vue'
import MedicalSection from '@/Components/Admin/MedicalSection.vue'
import TrainingSection from '@/Components/Admin/TrainingSection.vue'
import DocumentSection from '@/Components/Admin/DocumentSection.vue'


const activeTab = ref('applicant')


const format_date = (value) => {
  if (value) {
    return moment(String(value)).format('DD MMM YYYY');
  }
}

const format_time = (value) => {
  if (value) {
    return moment(String(value)).format('HH:mm');
  }
}


const getStatusInterview = (status) => {
  switch (status) {
    case 'dijadwalkan':
      return 'info';
    case 'selesai':
      return 'success';
    case 'dijadwal_ulang':
      return 'warning';
    default:
      return 'danger';
  }
};

const hasilInterviewRules = {
    skor_interview: [{ required: true, message: 'Penilaian Interview harus diisi', trigger: 'blur' }],
    skor_psikotes: [{ required: true, message: 'Penilaian Psikotes harus diisi', trigger: 'blur' }],
    kemampuan_komunikasi: [{ required: true, message: 'Kemampuan Komunikasi harus diisi', trigger: 'change' }],
    kemampuan_teknis: [{ required: true, message: 'Kemampuan Teknis harus diisi', trigger: 'change' }],
    kepribadian: [{ required: true, message: 'Kepribadian harus diisi', trigger: 'blur' }]
}

const formHasilInterview = reactive({
    user_id: null,
    lamaran_id: null,
    jadwal_id: null,
    skor_interview: null,
    skor_psikotes: null,
    kemampuan_komunikasi: null,
    kemampuan_teknis: null,
    kepribadian: null,
    rekomendasi: null,
    catatan: null,
});

const trainingRules = {
    program_id: [{ required: true, message: 'Program pelatihan wajib dipilih', trigger: 'change' }],
    tanggal_daftar: [{ required: true, message: 'Tanggal daftar wajib diisi', trigger: 'blur' }],
    tanggal_mulai: [{ required: true, message: 'Tanggal mulai wajib diisi', trigger: 'blur' }],
    tanggal_selesai: [{ required: true, message: 'Tanggal selesai wajib diisi', trigger: 'blur' }],
    status: [{ required: true, message: 'Status wajib dipilih', trigger: 'change' }]
}

const dokumenRules = {
    ktp: [{ required: true, message: 'KTP wajib diisi', trigger: 'change' }],
    kk: [{ required: true, message: 'Kartu keluarga wajib diisi', trigger: 'change' }],
    akte_lahir: [{ required: true, message: 'Akte lahir wajib diisi', trigger: 'change' }],
    surat_keterangan_sehat: [{ required: true, message: 'Surat Keterangan Sehat wajib diisi', trigger: 'change' }],
    surat_izin_keluarga: [{ required: true, message: 'Surat izin keluarga wajib diisi', trigger: 'change' }],
    ijazah: [{ required: true, message: 'Ijazah Terakhir wajib diisi', trigger: 'change' }],
    sertifikat_keahlian: [{ required: false, message: 'Sertifikat keahlian wajib diisi', trigger: 'change' }],
    paspor: [{ required: true, message: 'Paspor wajib diisi', trigger: 'change' }],
    surat_pengalaman: [{ required: false, message: 'Surat pengalaman kerja wajib diisi', trigger: 'change' }],
    skck: [{ required: true, message: 'SKCK wajib diisi', trigger: 'change' }],
}
const props = defineProps({
    lamaran: Object,
    completionStats: Object,
    statusOptions: Object,
    requiredDocuments: Array,
    adminList: Array,
    trainingPrograms: Array
})

// Status helpers
const statusLabels = {
    'pending': 'Menunggu Review',
    'diterima': 'Diterima', 
    'ditolak': 'Ditolak',
    'interview': 'Tahap Interview',
    'medical': 'Medical Check Up',
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

const getStatusLabel = (status) => statusLabels[status] || status
const getStatusType = (status) => statusTypes[status] || ''

const getProgressStep = (status) => {
    const steps = {
        'pending': 1,
        'diterima': 2,
        'interview': 3,
        'medical': 4,
        'pelatihan': 5,
        'siap': 6,
        'selesai': 7,
        'ditolak': 0
    }
    return steps[status] || 1
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



const getDocumentCompletionPercentage = () => {
    if (!props.completionStats?.documents) return 0
    const { completed, total } = props.completionStats.documents
    return total > 0 ? Math.round((completed / total) * 100) : 0
}

// Training helpers
const getTrainingStatusType = (status) => {
    const types = {
        'terdaftar': 'info',
        'sedang_pelatihan': 'warning',
        'selesai': 'success',
        'mengundurkan_diri': 'danger'
    }
    return types[status] || 'info'
}

const getTrainingStatusLabel = (status) => {
    const labels = {
        'terdaftar': 'Terdaftar',
        'sedang_pelatihan': 'Sedang Pelatihan',
        'selesai': 'Selesai',
        'mengundurkan_diri': 'Mengundurkan Diri'
    }
    return labels[status] || status
}

const formatCurrency = (amount) => {
    if (!amount) return '-'
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount)
}


// Status update
const updateStatus = async (status) => {
    let catatan = null
    
    if (status === 'ditolak') {
        try {
            const { value } = await ElMessageBox.prompt('Alasan penolakan:', 'Tolak Lamaran', {
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal',
                inputType: 'textarea'
            })
            catatan = value
        } catch {
            return
        }
    } else {
        try {
            await ElMessageBox.confirm(
                `Ubah status lamaran menjadi "${statusLabels[status]}"?`,
                'Konfirmasi',
                {
                    confirmButtonText: 'Ya, Ubah',
                    cancelButtonText: 'Batal',
                    type: 'warning'
                }
            )
        } catch {
            return
        }
    }

    const loading = ElLoading.service({ lock: true, text: 'Memperbarui status...' })
    
    try {
        await axios.post(route('admin.lamaran.update-status', props.lamaran.id), {
            status,
            catatan
        })
        
        ElMessage.success('Status berhasil diperbarui')
        router.visit(route('admin.lamaran.show', props.lamaran.id), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error updating status:', error)
        ElMessage.error('Gagal memperbarui status')
    } finally {
        loading.close()
    }
}

</script>

<style scoped>
.space-x-1 > * + * {
    margin-left: 0.25rem;
}

.percentage-value {
    font-size: 16px;
    font-weight: bold;
}

.gap-1 {
    gap: 0.25rem;
}
</style>