<template>
    <user-layout>
        <div class="align-middle border-2 border-bottom border-primary d-flex justify-content-between mb-4 py-2">
            <span class="fw-bold fs-lg">Detail Training</span>
            <div>
                <el-button :tag="Link" :href="route('user.training.index')" type="default">
                    <i class="fa fa-arrow-left me-2"></i>
                    Kembali
                </el-button>
            </div>
        </div>
        
        <!-- Training Information Card -->
        <el-card class="training-info-card mb-4">
            <div class="training-info-content">
                <div class="training-details">
                    <h2 class="training-title">{{ data.program?.nama || 'Program Training' }}</h2>
                    <div class="training-meta">
                        <div class="meta-item">
                            <i class="fa fa-briefcase text-primary me-2"></i>
                            <span>{{ data.lamaran?.lowongan?.posisi || 'Posisi tidak tersedia' }}</span>
                        </div>
                        <div class="meta-item" v-if="data.lamaran?.lowongan?.perusahaan">
                            <i class="fa fa-building text-success me-2"></i>
                            <span>{{ data.lamaran.lowongan.perusahaan }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fa fa-calendar text-info me-2"></i>
                            <span>Terdaftar pada {{ formatDate(data.tanggal_daftar) }}</span>
                        </div>
                    </div>
                </div>
                <div class="training-status">
                    <el-tag 
                        :type="getStatusType(data.status)" 
                        size="large"
                        class="status-tag"
                    >
                        {{ getStatusLabel(data.status) }}
                    </el-tag>
                </div>
            </div>
        </el-card>

        <!-- Training Information -->
        <el-card class="mb-4 rounded">
            <template #header>
                <div class="fs-4">
                    <i class="fa fa-info-circle me-2"></i>
                    Informasi Training
                </div>
            </template>
            
            <el-descriptions :column="1" border>
                <el-descriptions-item label="ID Training">
                    #{{ data.id }}
                </el-descriptions-item>
                <el-descriptions-item label="Program Training">
                    {{ data.program?.nama || '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Deskripsi Program" v-if="data.program?.deskripsi">
                    {{ data.program.deskripsi }}
                </el-descriptions-item>
                <el-descriptions-item label="Durasi Program" v-if="data.program?.durasi">
                    {{ data.program.durasi }} hari
                </el-descriptions-item>
                <el-descriptions-item label="Tanggal Daftar">
                    {{ formatDate(data.tanggal_daftar) }}
                </el-descriptions-item>
                <el-descriptions-item label="Tanggal Mulai">
                    {{ formatDate(data.tanggal_mulai) }}
                </el-descriptions-item>
                <el-descriptions-item label="Tanggal Selesai">
                    {{ formatDate(data.tanggal_selesai) }}
                </el-descriptions-item>
                <el-descriptions-item label="Status">
                    <el-tag :type="getStatusType(data.status)">
                        {{ getStatusLabel(data.status) }}
                    </el-tag>
                </el-descriptions-item>
                <el-descriptions-item label="Nilai Akhir" v-if="data.nilai_akhir">
                    <span class="score-value" :class="getScoreClass(data.nilai_akhir)">
                        {{ data.nilai_akhir }}/100 ({{ getGradeText(data.nilai_akhir) }})
                    </span>
                </el-descriptions-item>
                <el-descriptions-item label="Sertifikat" v-if="data.sertifikat_diterbitkan">
                    <span class="certificate-status">
                        <i class="fa fa-certificate text-success me-2"></i>
                        Sudah Diterbitkan
                        <span v-if="data.nomor_sertifikat" class="d-block small text-muted">
                            No: {{ data.nomor_sertifikat }}
                        </span>
                    </span>
                </el-descriptions-item>
                <el-descriptions-item label="Didaftarkan Oleh" v-if="data.staff">
                    {{ data.staff.nama }}
                </el-descriptions-item>
                <el-descriptions-item label="Catatan" v-if="data.catatan">
                    {{ data.catatan }}
                </el-descriptions-item>
            </el-descriptions>
        </el-card>

        <!-- Related Application (if exists) -->
        <el-card v-if="data.lamaran" class="mb-4 rounded">
            <template #header>
                <div class="fs-4">
                    <i class="fa fa-file-text me-2"></i>
                    Lamaran Terkait
                </div>
            </template>
            
            <div class="related-application">
                <div class="application-header">
                    <h4>{{ data.lamaran.lowongan?.posisi || 'Posisi tidak tersedia' }}</h4>
                    <el-tag 
                        :type="getStatusType(data.lamaran.status)"
                        size="small"
                    >
                        {{ getStatusLabel(data.lamaran.status) }}
                    </el-tag>
                </div>
                <div class="application-details">
                    <p><strong>Perusahaan:</strong> {{ data.lamaran.lowongan?.perusahaan || '-' }}</p>
                    <p><strong>Lokasi:</strong> {{ data.lamaran.lowongan?.lokasi || '-' }}</p>
                    <p><strong>Tanggal Lamaran:</strong> {{ formatDate(data.lamaran.tanggal_lamaran) }}</p>
                    <el-button 
                        :tag="Link" 
                        :href="route('user.lamaran.show', data.lamaran.id)"
                        type="primary"
                        size="small"
                    >
                        <i class="fa fa-eye me-2"></i>
                        Lihat Detail Lamaran
                    </el-button>
                </div>
            </div>
        </el-card>

        <!-- Training Progress -->
        <el-card>
            <template #header>
                <h3>
                    <i class="fa fa-tasks me-2"></i>
                    Status Training
                </h3>
            </template>
            
            <div class="training-progress">
                <el-alert
                    :title="getCurrentStatusTitle()"
                    :type="getCurrentStatusType()"
                    show-icon
                    :closable="false"
                >
                    <template #default>
                        {{ getCurrentStatusDescription() }}
                    </template>
                </el-alert>
            </div>
        </el-card>
    </user-layout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    data: Object
})

// Status mappings
const statusLabels = {
    'terdaftar': 'Terdaftar',
    'sedang_pelatihan': 'Sedang Pelatihan',
    'selesai': 'Selesai',
    'mengundurkan_diri': 'Mengundurkan Diri'
}

const statusTypes = {
    'terdaftar': 'warning',
    'sedang_pelatihan': 'primary',
    'selesai': 'success',
    'mengundurkan_diri': 'danger'
}

// Helper functions
const getStatusLabel = (status) => statusLabels[status] || status
const getStatusType = (status) => statusTypes[status] || ''

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}

const getScoreClass = (score) => {
    if (score >= 80) return 'excellent'
    if (score >= 70) return 'good'
    if (score >= 60) return 'fair'
    return 'poor'
}

const getGradeText = (score) => {
    if (score >= 80) return 'Sangat Baik'
    if (score >= 70) return 'Baik'
    if (score >= 60) return 'Cukup'
    return 'Perlu Perbaikan'
}

const getCurrentStatusTitle = () => {
    const titles = {
        'terdaftar': 'Terdaftar dalam Program',
        'sedang_pelatihan': 'Sedang Menjalani Pelatihan',
        'selesai': 'Training Selesai',
        'mengundurkan_diri': 'Mengundurkan Diri'
    }
    return titles[props.data.status] || 'Status Tidak Diketahui'
}

const getCurrentStatusDescription = () => {
    const descriptions = {
        'terdaftar': 'Anda telah terdaftar dalam program training ini. Persiapkan diri untuk memulai pelatihan.',
        'sedang_pelatihan': 'Anda sedang menjalani program training. Ikuti semua sesi dengan baik dan aktif berpartisipasi.',
        'selesai': 'Selamat! Anda telah menyelesaikan program training dengan baik.',
        'mengundurkan_diri': 'Anda telah mengundurkan diri dari program training ini.'
    }
    return descriptions[props.data.status] || 'Tidak ada deskripsi tersedia.'
}

const getCurrentStatusType = () => {
    const types = {
        'terdaftar': 'info',
        'sedang_pelatihan': 'warning',
        'selesai': 'success',
        'mengundurkan_diri': 'error'
    }
    return types[props.data.status] || 'info'
}
</script>

<style scoped>
.training-info-card {
    border-radius: 12px;
    overflow: hidden;
}

.training-info-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.training-title {
    font-size: 24px;
    font-weight: 600;
    color: #303133;
    margin-bottom: 12px;
}

.training-meta {
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

.score-value {
    font-weight: 600;
}

.score-value.excellent {
    color: #67c23a;
}

.score-value.good {
    color: #409eff;
}

.score-value.fair {
    color: #e6a23c;
}

.score-value.poor {
    color: #f56c6c;
}

.certificate-status {
    color: #67c23a;
    font-weight: 600;
}

.related-application {
    padding: 0;
}

.application-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.application-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #303133;
}

.application-details p {
    margin: 4px 0;
    font-size: 14px;
    color: #606266;
}

.training-progress {
    padding: 0;
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

@media (max-width: 768px) {
    .training-info-content {
        flex-direction: column;
        gap: 16px;
    }
    
    .training-meta {
        flex-direction: column;
    }
    
    .application-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
}
</style>