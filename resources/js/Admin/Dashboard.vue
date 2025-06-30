<template>
    <base-layout>
        <div class="content">
            <!-- Main Statistics Cards -->
            <el-row :gutter="24" class="mb-4">
                <el-col :span="8" v-for="(d,i) in stats" :key="i">
                    <el-card class="mb-2 rounded-lg stat-card" shadow="hover">
                        <el-statistic :value="d.value" :value-style="{ color: d.color, 'font-weight': 700 }">
                        <template #title>
                            <div class="fs-5 fw-semibold d-flex align-items-center">
                                <el-icon class="me-2" :color="d.color" size="20">
                                    <component :is="d.icon" />
                                </el-icon>
                                {{ d.label }}
                            </div>
                        </template>
                        </el-statistic>
                    </el-card>
                </el-col>
            </el-row>

            <el-row :gutter="24" class="mb-4">
                <!-- Recent Applications -->
                <el-col :span="12">
                    <el-card class="rounded" shadow="hover">
                        <template #header>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Lamaran Terbaru</span>
                                <el-button :tag="Link" :href="route('admin.lamaran.index')" type="primary" size="small">Lihat Semua</el-button>
                            </div>
                        </template>
                        <div v-if="recentApplications && recentApplications.length > 0">
                            <div v-for="application in recentApplications" :key="application.id" class="application-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-medium">{{ application.user?.detail?.nama || application.user?.nama }}</div>
                                        <div class="text-muted small">{{ application.lowongan?.posisi }}</div>
                                        <div class="text-muted small">{{ formatDate(application.tanggal_lamaran) }}</div>
                                    </div>
                                    <el-tag :type="getStatusType(application.status)" size="small">
                                        {{ getStatusLabel(application.status) }}
                                    </el-tag>
                                </div>
                            </div>
                        </div>
                        <el-empty v-else description="Belum ada lamaran" :image-size="60" />
                    </el-card>
                </el-col>

                <!-- Status Distribution Chart -->
                <el-col :span="12">
                    <el-card class="rounded" shadow="hover">
                        <template #header>
                            <span class="fw-semibold">Distribusi Status Lamaran</span>
                        </template>
                        <div class="status-distribution">
                            <div v-for="(value, key) in statusDistribution" :key="key" class="status-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-capitalize">{{ getStatusLabel(key) }}</span>
                                    <span class="fw-medium">{{ value }}</span>
                                </div>
                                <el-progress 
                                    :percentage="getPercentage(value, getTotalApplications())" 
                                    :color="getStatusColor(key)"
                                    :stroke-width="8"
                                    :show-text="false"
                                    class="mt-1" />
                            </div>
                        </div>
                    </el-card>
                </el-col>
            </el-row>

            <el-row :gutter="24" class="mb-4">
                <!-- Monthly Stats Chart -->
                <el-col :span="12">
                    <el-card class="rounded" shadow="hover">
                        <template #header>
                            <span class="fw-semibold">Statistik Bulanan</span>
                        </template>
                        <div ref="monthlyChart" style="height: 300px;"></div>
                    </el-card>
                </el-col>

                <!-- Additional Statistics -->
                <el-col :span="12">
                    <el-card class="rounded" shadow="hover">
                        <template #header>
                            <span class="fw-semibold">Statistik Lainnya</span>
                        </template>
                        <div class="additional-stats">
                            <div class="stat-group">
                                <div class="stat-title">Pengguna</div>
                                <div class="stat-row">
                                    <span>Total: {{ additionalStats?.users?.total || 0 }}</span>
                                    <span>Verified: {{ additionalStats?.users?.verified || 0 }}</span>
                                </div>
                                <el-progress 
                                    :percentage="additionalStats?.users?.completion_rate || 0" 
                                    color="#409eff" 
                                    :stroke-width="6" />
                            </div>
                            
                            <div class="stat-group">
                                <div class="stat-title">Lowongan</div>
                                <div class="stat-row">
                                    <span>Total: {{ additionalStats?.lowongan?.total || 0 }}</span>
                                    <span>Aktif: {{ additionalStats?.lowongan?.aktif || 0 }}</span>
                                </div>
                                <el-progress 
                                    :percentage="100 - (additionalStats?.lowongan?.closure_rate || 0)" 
                                    color="#67c23a" 
                                    :stroke-width="6" />
                            </div>
                            
                            <div class="stat-group">
                                <div class="stat-title">Dokumen</div>
                                <div class="stat-row">
                                    <span>Total: {{ additionalStats?.documents?.total || 0 }}</span>
                                    <span>Approved: {{ additionalStats?.documents?.approved || 0 }}</span>
                                </div>
                                <el-progress 
                                    :percentage="additionalStats?.documents?.approval_rate || 0" 
                                    color="#e6a23c" 
                                    :stroke-width="6" />
                            </div>
                        </div>
                    </el-card>
                </el-col>
            </el-row>
            
            <!-- Training Programs -->
            <el-card class="mb-4 rounded" shadow="hover">
                <template #header>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Program Pelatihan</span>
                        <el-button :tag="Link" :href="route('admin.training.program.index')" type="primary">Kelola Program</el-button>
                    </div>
                </template>
                <div class="training-grid">
                    <div class="training-card" v-for="program in props.program" :key="program.id">
                        <div class="training-title">{{ program.nama }}</div>
                        <div class="training-info">
                            <span>{{ program.training_count || 0 }} peserta</span>
                            <span>{{ program.durasi }} hari</span>
                        </div>
                        <div class="training-location">{{ program.lokasi }}</div>
                        <div class="training-instructor">Instruktur: {{ program.instruktur }}</div>
                    </div>
                </div>
            </el-card>

            <!-- Quick Actions -->
            <el-card class="rounded" shadow="hover">
                <template #header>
                    <span class="fw-semibold">Aksi Cepat</span>
                </template>
                <div class="quick-actions">
                    <el-button-group>
                        <el-button :tag="Link" :href="route('admin.lamaran.index')" type="primary" size="large">
                            <el-icon><Document /></el-icon>
                            Kelola Lamaran
                        </el-button>
                        <el-button :tag="Link" :href="route('admin.dokumen-lamaran.index')" type="success" size="large">
                            <el-icon><Folder /></el-icon>
                            Review Dokumen
                        </el-button>
                        <el-button :tag="Link" :href="route('admin.interview.index')" type="warning" size="large">
                            <el-icon><Calendar /></el-icon>
                            Interview
                        </el-button>
                        <el-button :tag="Link" :href="route('admin.talent.index')" type="info" size="large">
                            <el-icon><User /></el-icon>
                            Kelola Talent
                        </el-button>
                    </el-button-group>
                </div>
            </el-card>
        </div>
    </base-layout>
</template>
<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Document, Folder, Calendar, User } from '@element-plus/icons-vue'

// import { echarts } from 'echarts/core';
// Refs for charts
const statusChart = ref(null);
const monthlyChart = ref(null);
const props = defineProps({
    stats: Array,
    monthlyData: Array,
    statusDistribution: Object,
    recentApplications: Array,
    program: Array,
    additionalStats: Object
});

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
};

const statusTypes = {
    'pending': 'warning',
    'diterima': 'success',
    'ditolak': 'danger', 
    'interview': 'primary',
    'medical': 'info',
    'pelatihan': '',
    'siap': 'success',
    'selesai': 'success'
};

const statusColors = {
    'pending': '#e6a23c',
    'diterima': '#67c23a',
    'ditolak': '#f56c6c',
    'interview': '#409eff', 
    'medical': '#909399',
    'pelatihan': '#8b5cf6',
    'siap': '#67c23a',
    'selesai': '#67c23a'
};

// Helper functions
const getStatusLabel = (status) => statusLabels[status] || status;
const getStatusType = (status) => statusTypes[status] || '';
const getStatusColor = (status) => statusColors[status] || '#909399';

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const getTotalApplications = () => {
    if (!props.statusDistribution) return 1;
    return Object.values(props.statusDistribution).reduce((sum, count) => sum + count, 0) || 1;
};

const getPercentage = (value, total) => {
    return total > 0 ? Math.round((value / total) * 100) : 0;
};



const quickActions = ref([
    {
        key: 'register',
        label: 'Daftar TKI Baru',
        icon: 'User',
        color: '#409eff'
    },
    {
        key: 'interview',
        label: 'Jadwal Interview',
        icon: 'Calendar',
        color: '#67c23a'
    },
    {
        key: 'documents',
        label: 'Verifikasi Dokumen',
        icon: 'Document',
        color: '#e6a23c'
    },
    {
        key: 'training',
        label: 'Kelola Training',
        icon: 'School',
        color: '#909399'
    },
    {
        key: 'results',
        label: 'Hasil Interview',
        icon: 'Check',
        color: '#67c23a'
    },
    {
        key: 'reports',
        label: 'Laporan',
        icon: 'Histogram',
        color: '#f56c6c'
    }
]);

// // Chart initialization
// const initStatusChart = () => {
//     const chart = echarts.init(statusChart.value);
//     const option = {
//         tooltip: {
//             trigger: 'item',
//             formatter: '{a} <br/>{b}: {c} ({d}%)'
//         },
//         legend: {
//             orient: 'vertical',
//             left: 10,
//             data: ['Draft', 'Tervalidasi', 'Sudah Interview', 'Medical', 'Pelatihan', 'Siap']
//         },
//         series: [
//             {
//                 name: 'Status TKI',
//                 type: 'pie',
//                 radius: ['40%', '70%'],
//                 center: ['60%', '50%'],
//                 data: [
//                     { value: 125, name: 'Draft', itemStyle: { color: '#e6a23c' } },
//                     { value: 234, name: 'Tervalidasi', itemStyle: { color: '#67c23a' } },
//                     { value: 189, name: 'Sudah Interview', itemStyle: { color: '#409eff' } },
//                     { value: 98, name: 'Medical', itemStyle: { color: '#f56c6c' } },
//                     { value: 67, name: 'Pelatihan', itemStyle: { color: '#909399' } },
//                     { value: 156, name: 'Siap', itemStyle: { color: '#67c23a' } }
//                 ],
//                 emphasis: {
//                     itemStyle: {
//                         shadowBlur: 10,
//                         shadowOffsetX: 0,
//                         shadowColor: 'rgba(0, 0, 0, 0.5)'
//                     }
//                 }
//             }
//         ]
//     };
//     chart.setOption(option);
// };

// const initMonthlyChart = () => {
//     const chart = echarts.init(monthlyChart.value);
//     const option = {
//         tooltip: {
//             trigger: 'axis'
//         },
//         grid: {
//             left: '3%',
//             right: '4%',
//             bottom: '3%',
//             containLabel: true
//         },
//         xAxis: {
//             type: 'category',
//             boundaryGap: false,
//             data: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun']
//         },
//         yAxis: {
//             type: 'value'
//         },
//         series: [
//             {
//                 name: 'Pendaftaran',
//                 type: 'line',
//                 stack: 'Total',
//                 smooth: true,
//                 data: [45, 52, 38, 65, 71, 58],
//                 itemStyle: {
//                     color: '#409eff'
//                 },
//                 areaStyle: {
//                     color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
//                         {
//                             offset: 0,
//                             color: 'rgba(64, 158, 255, 0.3)'
//                         },
//                         {
//                             offset: 1,
//                             color: 'rgba(64, 158, 255, 0.1)'
//                         }
//                     ])
//                 }
//             }
//         ]
//     };
//     chart.setOption(option);
// };

// Chart initialization
const initMonthlyChart = async () => {
    if (!props.monthlyData || !monthlyChart.value) return;
    
    // Dynamic import for echarts
    const echarts = await import('echarts');
    
    const chart = echarts.init(monthlyChart.value);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    
    const option = {
        tooltip: {
            trigger: 'axis',
            formatter: 'Bulan {b}: {c} lamaran'
        },
        grid: {
            left: '3%',
            right: '4%', 
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: months
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name: 'Lamaran',
                type: 'line',
                smooth: true,
                data: props.monthlyData,
                itemStyle: {
                    color: '#409eff'
                },
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0,
                        y: 0,
                        x2: 0,
                        y2: 1,
                        colorStops: [{
                            offset: 0, color: 'rgba(64, 158, 255, 0.3)'
                        }, {
                            offset: 1, color: 'rgba(64, 158, 255, 0.1)'
                        }]
                    }
                }
            }
        ]
    };
    chart.setOption(option);
    
    // Handle window resize
    window.addEventListener('resize', () => {
        chart.resize();
    });
};

onMounted(async () => {
    await nextTick();
    initMonthlyChart();
});

</script>

<style scoped>
.stat-card {
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.application-item {
    padding: 12px 0;
    border-bottom: 1px solid #ebeef5;
}

.application-item:last-child {
    border-bottom: none;
}

.status-distribution {
    space-y: 16px;
}

.status-item {
    margin-bottom: 16px;
}

.additional-stats {
    space-y: 20px;
}

.stat-group {
    margin-bottom: 20px;
}

.stat-title {
    font-weight: 600;
    margin-bottom: 8px;
    color: #303133;
}

.stat-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 14px;
    color: #606266;
}

.training-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
}

.training-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    border: 1px solid #ebeef5;
    transition: all 0.3s ease;
}

.training-card:hover {
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.training-title {
    font-weight: 600;
    margin-bottom: 8px;
    color: #303133;
    font-size: 16px;
}

.training-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 12px;
    color: #909399;
}

.training-location {
    font-size: 13px;
    color: #606266;
    margin-bottom: 4px;
}

.training-instructor {
    font-size: 12px;
    color: #909399;
    font-style: italic;
}

.quick-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: center;
}

.quick-actions .el-button {
    flex: 1;
    min-width: 160px;
}

@media (max-width: 768px) {
    .training-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        flex-direction: column;
    }
    
    .quick-actions .el-button {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .el-col {
        margin-bottom: 16px;
    }
}
</style>