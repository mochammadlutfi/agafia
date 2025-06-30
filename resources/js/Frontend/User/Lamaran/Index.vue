<template>
    <user-layout>
        <div class="align-middle border-2 border-bottom border-primary d-flex justify-content-between mb-4 py-2">
            <span class="fw-bold fs-lg">Lamaran Saya</span>
            <div>
                <el-button :tag="Link" :href="route('lowongan')" type="primary" size="large">
                    <i class="fa fa-plus me-2"></i>
                    Cari Lowongan
                </el-button>
            </div>
        </div>
        
            <!-- Filters -->
            <div class="filters-section mb-4">
                <el-card>
                    <div class="d-flex align-items-center gap-3">
                        <span class="filter-label">Filter Status:</span>
                        <el-select 
                            v-model="selectedStatus" 
                            placeholder="Semua Status" 
                            clearable
                            @change="handleStatusFilter"
                            style="width: 200px"
                        >
                            <el-option label="Semua Status" value="" />
                            <el-option label="Menunggu Review" value="pending" />
                            <el-option label="Diterima" value="diterima" />
                            <el-option label="Ditolak" value="ditolak" />
                            <el-option label="Interview" value="interview" />
                            <el-option label="Medical Check" value="medical" />
                            <el-option label="Pelatihan" value="pelatihan" />
                            <el-option label="Siap Berangkat" value="siap" />
                            <el-option label="Selesai" value="selesai" />
                        </el-select>
                        <el-button @click="resetFilters" type="default">
                            <i class="fa fa-refresh me-2"></i>
                            Reset
                        </el-button>
                    </div>
                </el-card>
            </div>

            <!-- Applications List -->
            <div class="applications-section">
                <template v-if="lamaran.data && lamaran.data.length > 0">
                    <div class="applications-list">
                        <div 
                            v-for="application in lamaran.data" 
                            :key="application.id"
                            class="application-item"
                        >
                            <!-- Application Header -->
                            <div class="application-header">
                                <div class="application-info">
                                    <h3 class="application-title">{{ application.lowongan?.posisi }}</h3>
                                    <p class="application-company">
                                        <i class="fa fa-building me-2"></i>
                                        {{ application.lowongan?.perusahaan }}
                                    </p>
                                    <p class="application-location">
                                        <i class="fa fa-map-marker-alt me-2"></i>
                                        {{ application.lowongan?.lokasi }}
                                    </p>
                                </div>
                                <div class="application-status">
                                    <el-tag 
                                        :type="getStatusType(application.status)" 
                                        size="large"
                                        class="status-tag"
                                    >
                                        {{ getStatusLabel(application.status) }}
                                    </el-tag>
                                </div>
                            </div>

                            <!-- Application Details -->
                            <div class="application-details">
                                <div class="detail-row">
                                    <div class="detail-item">
                                        <span class="detail-label">Tanggal Lamaran:</span>
                                        <span class="detail-value">{{ formatDate(application.tanggal_lamaran) }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Diperbarui:</span>
                                        <span class="detail-value">{{ formatDate(application.updated_at) }}</span>
                                    </div>
                                    <div class="detail-item" v-if="application.catatan">
                                        <span class="detail-label">Catatan:</span>
                                        <span class="detail-value">{{ application.catatan }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="application-progress mb-3">
                                <div class="progress-label mb-2">
                                    <span>Progress Lamaran</span>
                                    <span class="progress-percentage">{{ getProgressPercentage(application.status) }}%</span>
                                </div>
                                <el-progress 
                                    :percentage="getProgressPercentage(application.status)"
                                    :color="getProgressColor(application.status)"
                                    :stroke-width="8"
                                />
                            </div>

                            <!-- Action Buttons -->
                            <div class="application-actions">
                                <el-button 
                                    :tag="Link" 
                                    :href="route('user.lamaran.show', application.id)"
                                    type="primary"
                                    size="small"
                                >
                                    <i class="fa fa-eye me-2"></i>
                                    Lihat Detail
                                </el-button>
                                
                                <el-button 
                                    v-if="application.status === 'pending'"
                                    @click="cancelApplication(application.id)"
                                    type="danger"
                                    size="small"
                                    plain
                                >
                                    <i class="fa fa-times me-2"></i>
                                    Batalkan
                                </el-button>

                                <el-button 
                                    v-if="application.cv_file"
                                    :tag="'a'"
                                    :href="'/storage/' + application.cv_file"
                                    target="_blank"
                                    type="info"
                                    size="small"
                                    plain
                                >
                                    <i class="fa fa-download me-2"></i>
                                    Download CV
                                </el-button>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-wrapper mt-4" v-if="lamaran.last_page > 1">
                        <el-pagination
                            v-model:current-page="currentPage"
                            :page-size="lamaran.per_page"
                            :total="lamaran.total"
                            layout="prev, pager, next, jumper, total"
                            @current-change="handlePageChange"
                        />
                    </div>
                </template>

                <!-- Empty State -->
                <el-card v-else>
                    <el-empty 
                        description="Belum ada lamaran yang dikirim"
                        :image-size="120"
                    >
                        <el-button :tag="Link" :href="route('lowongan')" type="primary" size="large">
                            <i class="fa fa-search me-2"></i>
                            Cari Lowongan Sekarang
                        </el-button>
                    </el-empty>
                </el-card>
            </div>
    </user-layout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { ElMessage, ElMessageBox } from 'element-plus'

const props = defineProps({
    lamaran: Object,
    stats: Object,
    filters: Object
})

// Reactive data
const selectedStatus = ref(props.filters?.status || '')
const currentPage = ref(props.lamaran?.current_page || 1)

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

const statLabels = {
    'total': 'Total Lamaran',
    'pending': 'Menunggu Review',
    'diterima': 'Diterima',
    'ditolak': 'Ditolak',
    'interview': 'Interview',
    'medical': 'Medical Check',
    'pelatihan': 'Pelatihan',
    'siap': 'Siap Berangkat',
    'selesai': 'Selesai'
}

const statColors = {
    'total': '#409eff',
    'pending': '#e6a23c',
    'diterima': '#67c23a',
    'ditolak': '#f56c6c',
    'interview': '#409eff',
    'medical': '#909399',
    'pelatihan': '#8b5cf6',
    'siap': '#67c23a',
    'selesai': '#67c23a'
}

const statIcons = {
    'total': 'fa-file-text',
    'pending': 'fa-clock',
    'diterima': 'fa-check',
    'ditolak': 'fa-times',
    'interview': 'fa-calendar',
    'medical': 'fa-stethoscope',
    'pelatihan': 'fa-graduation-cap',
    'siap': 'fa-plane',
    'selesai': 'fa-check-circle'
}

// Helper functions
const getStatusLabel = (status) => statusLabels[status] || status
const getStatusType = (status) => statusTypes[status] || ''
const getStatLabel = (key) => statLabels[key] || key
const getStatColor = (key) => statColors[key] || '#909399'
const getStatIcon = (key) => statIcons[key] || 'fa-file'

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}

const getProgressPercentage = (status) => {
    const percentages = {
        'pending': 15,
        'diterima': 30,
        'interview': 50,
        'medical': 70,
        'pelatihan': 85,
        'siap': 95,
        'selesai': 100,
        'ditolak': 0
    }
    return percentages[status] || 15
}

const getProgressColor = (status) => {
    if (status === 'ditolak') return '#f56c6c'
    if (status === 'selesai') return '#67c23a'
    return '#409eff'
}

// Event handlers
const handleStatusFilter = () => {
    router.get(route('user.lamaran.index'), {
        status: selectedStatus.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const resetFilters = () => {
    selectedStatus.value = ''
    router.get(route('user.lamaran.index'), {}, {
        preserveState: true,
        preserveScroll: true
    })
}

const handlePageChange = (page) => {
    router.get(route('user.lamaran.index'), {
        page: page,
        status: selectedStatus.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const cancelApplication = async (id) => {
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

        router.post(route('user.lamaran.cancel', id), {}, {
            preserveState: false,
            onSuccess: () => {
                ElMessage.success('Lamaran berhasil dibatalkan')
            },
            onError: () => {
                ElMessage.error('Gagal membatalkan lamaran')
            }
        })
    } catch (error) {
        // User cancelled
    }
}
</script>

<style scoped>
.lamaran-container {
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #303133;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 14px;
    color: #606266;
}

.filters-section .filter-label {
    font-weight: 600;
    color: #606266;
}

.applications-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.application-item {
    background-color: #ffffff;
    border: 1px solid #ebeef5;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
}

.application-item:hover {
    border-color: #409eff;
    box-shadow: 0 2px 12px rgba(64, 158, 255, 0.1);
}

.application-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.application-title {
    font-size: 20px;
    font-weight: 600;
    color: #303133;
    margin-bottom: 8px;
}

.application-company,
.application-location {
    margin: 4px 0;
    color: #606266;
    font-size: 14px;
}

.status-tag {
    font-weight: 600;
}

.application-details {
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

.application-progress {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 8px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    color: #606266;
    font-weight: 600;
}

.progress-percentage {
    color: #409eff;
}

.application-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .lamaran-container {
        padding: 16px;
    }
    
    .header-section .d-flex {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .application-header {
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
    
    .application-actions {
        justify-content: stretch;
    }
    
    .application-actions .el-button {
        flex: 1;
    }
}
</style>