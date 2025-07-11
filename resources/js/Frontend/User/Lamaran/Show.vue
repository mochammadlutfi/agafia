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
                                <span>Dilamar pada {{ formatDate(lamaran.tanggal_lamaran) }}</span>
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
                        :description="getCurrentStepDescription()"
                        :type="getCurrentStepType()"
                        show-icon
                        :closable="false"
                    />
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
                        {{ formatDate(lamaran.tanggal_lamaran) }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Status">
                        <el-tag :type="getStatusType(lamaran.status)">
                            {{ getStatusLabel(lamaran.status) }}
                        </el-tag>
                    </el-descriptions-item>
                    <el-descriptions-item label="Terakhir Diperbarui">
                        {{ formatDateTime(lamaran.updated_at) }}
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
                            <p><strong>Tanggal:</strong> {{ formatDateTime(lamaran.interview.tanggal) }}</p>
                            <p><strong>Lokasi:</strong> {{ lamaran.interview.lokasi || 'Online' }}</p>
                            <p v-if="lamaran.interview.catatan"><strong>Catatan:</strong> {{ lamaran.interview.catatan }}</p>
                        </div>
                    </div>
                </div>
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
const showDocumentModal = ref(false)

const medicalLoading = ref(false)
const documentLoading = ref(false)

const medicalFormRef = ref()
const documentFormRef = ref()



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

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

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
    return moment(String(value)).format('HH:mm');
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
        'ditolak': 'Lamaran tidak dapat diproses lebih lanjut. Silakan coba lamar posisi lain.'
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