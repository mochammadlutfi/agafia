<template>
    <user-layout>
        <div class="align-middle border-2 border-bottom border-primary d-flex justify-content-between mb-4 py-2">
            <span class="fw-bold fs-lg">Training Saya</span>
        </div>
        
        <!-- Training List -->
        <div class="training-section">
            <template v-if="data && data.length > 0">
                <div class="training-list">
                    <div 
                        v-for="training in data" 
                        :key="training.id"
                        class="training-item"
                    >
                        <!-- Training Header -->
                        <div class="training-header">
                            <div class="training-info">
                                <h3 class="training-title">{{ training.program?.nama || 'Program Tidak Tersedia' }}</h3>
                                <p class="training-company">
                                    <i class="fa fa-briefcase me-2"></i>
                                    {{ training.lamaran?.lowongan?.posisi || 'Posisi tidak tersedia' }}
                                    <span v-if="training.lamaran?.lowongan?.perusahaan">
                                        - {{ training.lamaran.lowongan.perusahaan }}
                                    </span>
                                </p>
                            </div>
                            <div class="training-status">
                                <el-tag 
                                    :type="getStatusType(training.status)" 
                                    size="large"
                                    class="status-tag"
                                >
                                    {{ getStatusLabel(training.status) }}
                                </el-tag>
                            </div>
                        </div>

                        <!-- Training Details -->
                        <div class="training-details">
                            <div class="detail-row">
                                <div class="detail-item">
                                    <span class="detail-label">Tanggal Daftar:</span>
                                    <span class="detail-value">{{ formatDate(training.tanggal_daftar) }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Tanggal Mulai:</span>
                                    <span class="detail-value">{{ formatDate(training.tanggal_mulai) }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Tanggal Selesai:</span>
                                    <span class="detail-value">{{ formatDate(training.tanggal_selesai) }}</span>
                                </div>
                                <div class="detail-item" v-if="training.nilai_akhir">
                                    <span class="detail-label">Nilai Akhir:</span>
                                    <span class="detail-value score" :class="getScoreClass(training.nilai_akhir)">{{ training.nilai_akhir }}/100</span>
                                </div>
                                <div class="detail-item" v-if="training.sertifikat_diterbitkan">
                                    <span class="detail-label">Sertifikat:</span>
                                    <span class="detail-value certificate">{{ training.nomor_sertifikat || 'Sudah Diterbitkan' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="training-actions">
                            <el-button 
                                :tag="Link" 
                                :href="route('user.training.show', training.id)"
                                type="primary"
                                size="small"
                            >
                                <i class="fa fa-eye me-2"></i>
                                Lihat Detail
                            </el-button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <el-card v-else>
                <el-empty 
                    description="Belum ada program training"
                    :image-size="120"
                >
                    <el-button :tag="Link" :href="route('user.lamaran.index')" type="primary" size="large">
                        <i class="fa fa-list me-2"></i>
                        Lihat Lamaran Saya
                    </el-button>
                </el-empty>
            </el-card>
        </div>
    </user-layout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    data: Array
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
</script>

<style scoped>
.training-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.training-item {
    background-color: #ffffff;
    border: 1px solid #ebeef5;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
}

.training-item:hover {
    border-color: #409eff;
    box-shadow: 0 2px 12px rgba(64, 158, 255, 0.1);
}

.training-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.training-title {
    font-size: 20px;
    font-weight: 600;
    color: #303133;
    margin-bottom: 8px;
}

.training-company {
    margin: 4px 0;
    color: #606266;
    font-size: 14px;
}

.status-tag {
    font-weight: 600;
}

.training-details {
    margin-bottom: 16px;
}

.detail-row {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    min-width: 200px;
}

.detail-label {
    font-size: 12px;
    color: #909399;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.detail-value {
    font-size: 14px;
    color: #303133;
    font-weight: 500;
}

.detail-value.score {
    font-weight: 700;
}

.detail-value.score.excellent {
    color: #67c23a;
}

.detail-value.score.good {
    color: #409eff;
}

.detail-value.score.fair {
    color: #e6a23c;
}

.detail-value.score.poor {
    color: #f56c6c;
}

.detail-value.certificate {
    color: #67c23a;
    font-weight: 600;
}

.training-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .training-header {
        flex-direction: column;
        gap: 12px;
    }
    
    .detail-row {
        flex-direction: column;
        gap: 8px;
    }
    
    .detail-item {
        min-width: auto;
    }
    
    .training-actions {
        justify-content: stretch;
    }
    
    .training-actions .el-button {
        flex: 1;
    }
}
</style>