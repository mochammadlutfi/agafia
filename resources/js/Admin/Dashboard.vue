<template>
    <base-layout>
        <div class="content">
            <el-row :gutter="24">
                <el-col :span="8" v-for="(d,i) in stats">
                    <el-card class="mb-2 rounded-lg">
                        <el-statistic :value="d.value" :value-style="{ color: d.color, 'font-weight': 700 }">
                        <template #title>
                            <div class="fs-5 fw-semibold">
                            {{ d.label }}
                            </div>
                        </template>
                        </el-statistic>
                    </el-card>
                </el-col>
            </el-row>
            
            <el-card class="mb-4 rounded">
                <el-row justify="space-between" align="middle" class="mb-4">
                    <el-col :span="12">
                        <div class="fs-base fw-semibold">Program Pelatihan</div>
                    </el-col>
                    <el-col :span="12" class="text-end">
                        <el-button class="my-auto" :tag="Link" :href="route('admin.training.program.index')" type="primary">Kelola Program</el-button>
                    </el-col>
                </el-row>
                <div class="training-grid">
                    <div class="training-card" v-for="program in trainingPrograms" :key="program.id">
                        <div class="training-title">{{ program.name }}</div>
                        <div class="training-info">
                            <span>{{ program.participants }}/{{ program.capacity }} peserta</span>
                            <span>{{ program.progress }}%</span>
                        </div>
                        <el-progress :percentage="program.progress"
                            :color="program.progress >= 80 ? '#67c23a' : program.progress >= 60 ? '#e6a23c' : '#f56c6c'"
                            :stroke-width="6" />
                    </div>
                </div>
            </el-card>
        </div>
    </base-layout>
</template>
<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { Link } from '@inertiajs/vue3'
// import { echarts } from 'echarts/core';
// Refs for charts
const statusChart = ref(null);
const monthlyChart = ref(null);
const props = defineProps({
    stats : Array
});

const activities = ref([
    {
        id: 1,
        action: 'Pendaftaran baru',
        user: 'Siti Nurhaliza',
        time: '2 jam lalu',
        color: '#409eff'
    },
    {
        id: 2,
        action: 'Interview selesai',
        user: 'Ahmad Wijaya',
        time: '3 jam lalu',
        color: '#67c23a'
    },
    {
        id: 3,
        action: 'Dokumen diverifikasi',
        user: 'Maria Santos',
        time: '5 jam lalu',
        color: '#e6a23c'
    },
    {
        id: 4,
        action: 'Training dimulai',
        user: 'Budi Santoso',
        time: '1 hari lalu',
        color: '#909399'
    },
    {
        id: 5,
        action: 'Status siap kerja',
        user: 'Indah Permata',
        time: '2 hari lalu',
        color: '#67c23a'
    }
]);

const trainingPrograms = ref([
    {
        id: 1,
        name: 'Bahasa Arab Dasar',
        participants: 25,
        capacity: 25,
        progress: 100
    },
    {
        id: 2,
        name: 'Bahasa Inggris Dasar',
        participants: 18,
        capacity: 25,
        progress: 72
    },
    {
        id: 3,
        name: 'Keterampilan Housekeeping',
        participants: 22,
        capacity: 30,
        progress: 73
    },
    {
        id: 4,
        name: 'Perawatan Lansia',
        participants: 15,
        capacity: 20,
        progress: 75
    },
    {
        id: 5,
        name: 'Keterampilan Dapur',
        participants: 12,
        capacity: 20,
        progress: 60
    }
]);

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

onMounted(async () => {
    await nextTick();
    // initStatusChart();
    // initMonthlyChart();
});

</script>

<style scoped>
        .training-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        .training-card {
            background: white;
            border-radius: 8px;
            padding: 16px;
            border: 1px solid #ebeef5;
        }
        .training-title {
            font-weight: 500;
            margin-bottom: 8px;
            color: #303133;
        }
        .training-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
            color: #909399;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 16px;
        }
        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background: white;
            border: 1px solid #ebeef5;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: #303133;
        }
        .action-btn:hover {
            border-color: #409eff;
            background: #f0f9ff;
            transform: translateY(-2px);
        }
        .action-icon {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .action-text {
            font-size: 12px;
            text-align: center;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
        }
        @media (max-width: 768px) {
            .grid-2, .grid-3 {
                grid-template-columns: 1fr;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

</style>