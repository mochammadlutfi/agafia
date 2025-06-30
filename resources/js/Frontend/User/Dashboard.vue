<template>
    <user-layout>
        <div class="dashboard-container">
            <!-- Welcome Section -->
            <!-- <div class="welcome-section">
                <h1 class="dashboard-title">Selamat Datang, {{ $page.props.auth.user.nama }}!</h1>
                <p class="dashboard-subtitle">Kelola lamaran dan pantau progres Anda di sini</p>
            </div> -->

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card" v-for="(stat, key) in stats" :key="key">
                    <div class="stat-icon" :style="{ backgroundColor: getStatColor(key) }">
                        <el-icon :size="24" color="white">
                            <component :is="getStatIcon(key)" />
                        </el-icon>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ stat }}</div>
                        <div class="stat-label">{{ getStatLabel(key) }}</div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="content-grid">
                <!-- Recent Applications -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Lamaran Terbaru</h3>
                        <el-button :tag="Link" :href="route('user.lamaran.index')" type="primary" size="small">
                            Lihat Semua
                        </el-button>
                    </div>
                    <div class="card-content">
                        <div v-if="recentApplications && recentApplications.length > 0" class="applications-list">
                            <div v-for="application in recentApplications" :key="application.id" class="application-item">
                                <div class="application-info">
                                    <h4>{{ application.lowongan?.posisi }}</h4>
                                    <p>{{ application.lowongan?.perusahaan }}</p>
                                    <span class="application-date">{{ formatDate(application.tanggal_lamaran) }}</span>
                                </div>
                                <div class="application-status">
                                    <el-tag :type="getStatusType(application.status)" size="small">
                                        {{ getStatusLabel(application.status) }}
                                    </el-tag>
                                    <el-button 
                                        :tag="Link" 
                                        :href="route('user.lamaran.show', application.id)" 
                                        type="text" 
                                        size="small">
                                        Detail
                                    </el-button>
                                </div>
                            </div>
                        </div>
                        <el-empty v-else description="Belum ada lamaran" :image-size="60">
                            <el-button :tag="Link" :href="route('lowongan')" type="primary">
                                Lihat Lowongan
                            </el-button>
                        </el-empty>
                    </div>
                </div>

                <!-- Upcoming Interviews -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Interview Mendatang</h3>
                        <el-button :tag="Link" :href="route('user.interview')" type="primary" size="small">
                            Lihat Semua
                        </el-button>
                    </div>
                    <div class="card-content">
                        <div v-if="upcomingInterviews && upcomingInterviews.length > 0" class="interviews-list">
                            <div v-for="interview in upcomingInterviews" :key="interview.id" class="interview-item">
                                <div class="interview-info">
                                    <h4>{{ interview.lamaran?.lowongan?.posisi }}</h4>
                                    <p>{{ formatDateTime(interview.tanggal) }}</p>
                                    <span class="interview-location">{{ interview.lokasi || 'Online' }}</span>
                                </div>
                                <div class="interview-actions">
                                    <el-tag type="primary" size="small">
                                        {{ interview.status === 'selesai' ? 'Selesai' : 'Terjadwal' }}
                                    </el-tag>
                                </div>
                            </div>
                        </div>
                        <el-empty v-else description="Tidak ada interview terjadwal" :image-size="60" />
                    </div>
                </div>

                <!-- Active Applications Progress -->
                <div class="dashboard-card full-width">
                    <div class="card-header">
                        <h3>Progress Lamaran Aktif</h3>
                    </div>
                    <div class="card-content">
                        <div v-if="activeApplications && activeApplications.length > 0" class="progress-list">
                            <div v-for="app in activeApplications" :key="app.lamaran.id" class="progress-item">
                                <div class="progress-header">
                                    <div class="progress-info">
                                        <h4>{{ app.lamaran.lowongan?.posisi }}</h4>
                                        <p>{{ app.lamaran.lowongan?.perusahaan }}</p>
                                    </div>
                                    <el-tag :type="getStatusType(app.lamaran.status)" size="small">
                                        {{ getStatusLabel(app.lamaran.status) }}
                                    </el-tag>
                                </div>
                                <div class="progress-details">
                                    <div class="document-progress">
                                        <span class="progress-label">Kelengkapan Dokumen:</span>
                                        <div class="progress-bar-container">
                                            <el-progress 
                                                :percentage="app.documents.completion_percentage" 
                                                :color="getProgressColor(app.documents.completion_percentage)"
                                                :stroke-width="8">
                                                <template #default="{ percentage }">
                                                    <span class="progress-text">{{ app.documents.uploaded }}/{{ app.documents.required }}</span>
                                                </template>
                                            </el-progress>
                                        </div>
                                    </div>
                                    <div class="progress-actions">
                                        <el-button :tag="Link" :href="route('user.lamaran.show', app.lamaran.id)" type="primary" size="small">
                                            Lihat Detail
                                        </el-button>
                                        <el-button :tag="Link" :href="route('user.dokumen.index', app.lamaran.id)" type="success" size="small">
                                            Kelola Dokumen
                                        </el-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <el-empty v-else description="Tidak ada lamaran aktif" :image-size="60">
                            <el-button :tag="Link" :href="route('lowongan')" type="primary">
                                Cari Lowongan
                            </el-button>
                        </el-empty>
                    </div>
                </div>

                <!-- Job Opportunities -->
                <div class="dashboard-card full-width">
                    <div class="card-header">
                        <h3>Lowongan Terbaru</h3>
                        <el-button :tag="Link" :href="route('lowongan')" type="primary" size="small">
                            Lihat Semua
                        </el-button>
                    </div>
                    <div class="card-content">
                        <div v-if="activeJobs && activeJobs.length > 0" class="jobs-grid">
                            <div v-for="job in activeJobs" :key="job.id" class="job-card">
                                <div class="job-header">
                                    <h4>{{ job.posisi }}</h4>
                                    <el-tag type="success" size="small">{{ job.status }}</el-tag>
                                </div>
                                <div class="job-content">
                                    <p class="job-company">{{ job.perusahaan }}</p>
                                    <p class="job-location">{{ job.negara_tujuan }}</p>
                                    <p class="job-salary">{{ formatCurrency(job.gaji) }}</p>
                                </div>
                                <div class="job-actions">
                                    <el-button :tag="Link" :href="route('lowongan.detail', job.id)" type="text" size="small">
                                        Detail
                                    </el-button>
                                    <el-button :tag="Link" :href="route('user.lamaran.create', job.id)" type="primary" size="small">
                                        Lamar
                                    </el-button>
                                </div>
                            </div>
                        </div>
                        <el-empty v-else description="Tidak ada lowongan tersedia" :image-size="60" />
                    </div>
                </div>
            </div>
        </div>
    </user-layout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { 
    Document, 
    User, 
    Clock, 
    Check, 
    Close, 
    Calendar, 
    Folder,
    Warning,
    SuccessFilled
} from '@element-plus/icons-vue'

const props = defineProps({
    stats: Object,
    recentApplications: Array,
    upcomingInterviews: Array,
    activeJobs: Array,
    activeApplications: Array
})

// Status mappings
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

const statLabels = {
    'total_lamaran': 'Total Lamaran',
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
    'total_lamaran': '#409eff',
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
    'total_lamaran': Document,
    'pending': Clock,
    'diterima': Check,
    'ditolak': Close,
    'interview': Calendar,
    'medical': Folder,
    'pelatihan': User,
    'siap': SuccessFilled,
    'selesai': SuccessFilled
}

// Helper functions
const getStatusLabel = (status) => statusLabels[status] || status
const getStatusType = (status) => statusTypes[status] || ''
const getStatLabel = (key) => statLabels[key] || key
const getStatColor = (key) => statColors[key] || '#909399'
const getStatIcon = (key) => statIcons[key] || Document

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })
}

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount)
}

const getProgressColor = (percentage) => {
    if (percentage >= 80) return '#67c23a'
    if (percentage >= 60) return '#e6a23c'
    return '#f56c6c'
}
</script>

<style scoped>
.dashboard-container {
    padding: 24px;
    background: #f5f7fa;
    min-height: 100vh;
}

.welcome-section {
    margin-bottom: 32px;
}

.dashboard-title {
    font-size: 32px;
    font-weight: 700;
    color: #303133;
    margin-bottom: 8px;
}

.dashboard-subtitle {
    font-size: 16px;
    color: #606266;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
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

.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
}

.dashboard-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.dashboard-card.full-width {
    grid-column: 1 / -1;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #ebeef5;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #303133;
}

.card-content {
    padding: 24px;
}

.applications-list > * + *,
.interviews-list > * + * {
    margin-top: 16px;
}

.application-item,
.interview-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 12px;
}

.application-info h4,
.interview-info h4 {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 600;
    color: #303133;
}

.application-info p,
.interview-info p {
    margin: 0 0 4px 0;
    color: #606266;
    font-size: 14px;
}

.application-date,
.interview-location {
    font-size: 12px;
    color: #909399;
}

.application-status,
.interview-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}

.progress-list > * + * {
    margin-top: 20px;
}

.progress-item {
    border: 1px solid #ebeef5;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 16px;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.progress-info h4 {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 600;
    color: #303133;
}

.progress-info p {
    margin: 0;
    color: #606266;
    font-size: 14px;
}

.progress-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.document-progress {
    flex: 1;
}

.progress-label {
    font-size: 14px;
    color: #606266;
    margin-bottom: 8px;
    display: block;
}

.progress-bar-container {
    display: flex;
    align-items: center;
    gap: 12px;
}

.progress-text {
    font-size: 12px;
    color: #606266;
    min-width: 40px;
}

.progress-actions {
    display: flex;
    gap: 8px;
}

.jobs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
}

.job-card {
    border: 1px solid #ebeef5;
    border-radius: 8px;
    padding: 16px;
    transition: all 0.3s ease;
}

.job-card:hover {
    border-color: #409eff;
    box-shadow: 0 2px 8px rgba(64, 158, 255, 0.2);
}

.job-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.job-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #303133;
}

.job-content {
    margin-bottom: 16px;
}

.job-company,
.job-location,
.job-salary {
    margin: 0 0 4px 0;
    font-size: 14px;
    color: #606266;
}

.job-salary {
    font-weight: 600;
    color: #67c23a;
}

.job-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 16px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .progress-details {
        flex-direction: column;
        align-items: stretch;
    }
    
    .progress-actions {
        justify-content: stretch;
    }
    
    .progress-actions .el-button {
        flex: 1;
    }
    
    .jobs-grid {
        grid-template-columns: 1fr;
    }
}
</style>