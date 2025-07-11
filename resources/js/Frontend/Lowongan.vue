<template>
    <base-layout>
        <!-- Hero Banner -->
        <div class="bg-primary mb-4 p-4 w-100">
            <div class="text-center text-white">
                <h1 class="fs-2 fw-bold mb-3">Lowongan Kerja</h1>
                <p class="lead">Temukan peluang karir terbaik untuk masa depan yang cerah</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Filter Section -->
                        <div class="block">
                            <div class="block-header block-header-default">
                                <h3 class="block-title">Filter Lowongan</h3>
                            </div>
                            <div class="block-content p-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <el-input v-model="filters.search"
                                            placeholder="Cari posisi, perusahaan, atau lokasi..." @input="filterJobs">
                                            <template #prefix>
                                                <i class="fa fa-search"></i>
                                            </template>
                                        </el-input>
                                    </div>
                                    <div class="col-md-3">
                                        <el-select v-model="filters.lokasi" placeholder="Pilih Lokasi"
                                            @change="filterJobs" clearable>
                                            <el-option v-for="lokasi in uniqueLocations" :key="lokasi" :label="lokasi"
                                                :value="lokasi" />
                                        </el-select>
                                    </div>
                                    <div class="col-md-3">
                                        <el-select v-model="filters.perusahaan" placeholder="Pilih Perusahaan"
                                            @change="filterJobs" clearable>
                                            <el-option v-for="perusahaan in uniqueCompanies" :key="perusahaan"
                                                :label="perusahaan" :value="perusahaan" />
                                        </el-select>
                                    </div>
                                    <div class="col-md-2">
                                        <el-button type="primary" @click="clearFilters" plain>
                                            <i class="fa fa-refresh me-1"></i>
                                            Reset
                                        </el-button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Job Listings -->
                        <div class="block">
                            <div class="block-header block-header-default">
                                <h3 class="block-title">
                                    Daftar Lowongan
                                    <small class="text-muted">({{ filteredJobs.length }} lowongan tersedia)</small>
                                </h3>
                            </div>
                            <div class="block-content">
                                <div v-if="filteredJobs.length === 0" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fa fa-search fa-3x text-muted"></i>
                                    </div>
                                    <h4 class="text-muted">Tidak ada lowongan ditemukan</h4>
                                    <p class="text-muted">Coba ubah filter pencarian Anda</p>
                                </div>

                                <div v-else class="row">
                                    <div v-for="job in filteredJobs" :key="job.id" class="col-lg-6 col-xl-4 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-img-top position-relative">
                                                <img :src="job.foto ? job.foto : '/images/placeholder.png'"
                                                    class="w-100" style="height: 200px; object-fit: cover;"
                                                    :alt="job.posisi" />
                                                <div class="position-absolute top-0 end-0 p-2">
                                                    <el-tag type="success" size="small">
                                                        {{ job.status_label }}
                                                    </el-tag>
                                                </div>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <h5 class="card-title fw-bold text-primary">{{ job.posisi }}</h5>
                                                <h6 class="card-subtitle mb-2 text-muted">
                                                    <i class="fa fa-building me-1"></i>
                                                    {{ job.perusahaan }}
                                                </h6>
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fa fa-map-marker-alt text-muted me-2"></i>
                                                        <span class="text-muted">{{ job.lokasi }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fa fa-users text-muted me-2"></i>
                                                        <span class="text-muted">{{ job.kuota }} posisi tersedia</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa fa-tools text-muted me-2"></i>
                                                        <span class="text-muted">{{ job.skill }}</span>
                                                    </div>
                                                </div>
                                                <p class="card-text text-muted flex-grow-1">
                                                    {{ truncateText(job.deskripsi, 100) }}
                                                </p>
                                                <div class="mt-auto">
                                                    <el-button type="primary" class="w-100" @click="viewJobDetail(job)">
                                                        <i class="fa fa-eye me-1"></i>
                                                        Lihat Detail
                                                    </el-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Job Detail Modal -->
        <el-dialog v-model="showDetailModal" :title="selectedJob?.posisi" width="80%" center>
            <div v-if="selectedJob" class="row">
                <div class="col-md-5">
                    <img :src="selectedJob.foto ?? '/images/placeholder.png'" class="w-100 rounded mb-3"
                        style="height: 250px; object-fit: cover;" :alt="selectedJob.posisi" />
                </div>
                <div class="col-md-7">
                    <div class="mb-3">
                        <h4 class="text-primary fw-bold mb-2">{{ selectedJob.posisi }}</h4>
                        <h5 class="text-muted">{{ selectedJob.perusahaan }}</h5>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-map-marker-alt text-primary me-2"></i>
                                <strong>Lokasi:</strong>
                                <span class="ms-2">{{ selectedJob.lokasi }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-users text-primary me-2"></i>
                                <strong>Kuota:</strong>
                                <span class="ms-2">{{ selectedJob.kuota }} posisi</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-tools text-primary me-2"></i>
                                <strong>Skill Required:</strong>
                            </div>
                            <span class="ms-4">{{ selectedJob.skill }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6><i class="fa fa-info-circle text-primary me-2"></i>Deskripsi Pekerjaan</h6>
                        <p class="text-muted">{{ selectedJob.deskripsi }}</p>
                    </div>

                    <div class="d-flex gap-2">
                        <el-button type="primary" size="large" @click="applyJob" :disabled="!$page.props.user">
                            <i class="fa fa-paper-plane me-1"></i>
                            {{ $page.props.user ? 'Lamar Sekarang' : 'Login untuk Melamar' }}
                        </el-button>
                        <el-button @click="showDetailModal = false" size="large">
                            Tutup
                        </el-button>
                    </div>
                </div>
            </div>
        </el-dialog>
    </base-layout>
</template>

<script setup>
import BaseLayout from './Layouts/BaseLayout.vue'
import { ref, computed, onMounted } from 'vue'
import { ElMessage } from 'element-plus'

const props = defineProps({
    data: Array
})

// Reactive data
const filters = ref({
    search: '',
    lokasi: '',
    perusahaan: ''
})

const showDetailModal = ref(false)
const selectedJob = ref(null)

// Computed properties
const uniqueLocations = computed(() => {
    return [...new Set(props.data.map(job => job.lokasi))].sort()
})

const uniqueCompanies = computed(() => {
    return [...new Set(props.data.map(job => job.perusahaan))].sort()
})

const filteredJobs = computed(() => {
    let jobs = props.data

    if (filters.value.search) {
        const searchTerm = filters.value.search.toLowerCase()
        jobs = jobs.filter(job =>
            job.posisi.toLowerCase().includes(searchTerm) ||
            job.perusahaan.toLowerCase().includes(searchTerm) ||
            job.lokasi.toLowerCase().includes(searchTerm) ||
            job.skill.toLowerCase().includes(searchTerm)
        )
    }

    if (filters.value.lokasi) {
        jobs = jobs.filter(job => job.lokasi === filters.value.lokasi)
    }

    if (filters.value.perusahaan) {
        jobs = jobs.filter(job => job.perusahaan === filters.value.perusahaan)
    }

    return jobs
})

// Methods
const filterJobs = () => {
    // Filter is handled by computed property
}

const clearFilters = () => {
    filters.value = {
        search: '',
        lokasi: '',
        perusahaan: ''
    }
}

const truncateText = (text, maxLength) => {
    if (text.length <= maxLength) return text
    return text.substring(0, maxLength) + '...'
}

const viewJobDetail = (job) => {
    // Navigate to job detail page instead of showing modal
    window.location.href = route('lowongan.detail', job.id)
}

const applyJob = () => {
    if (!selectedJob.value) return

    // Here you would typically redirect to application form or handle the application
    ElMessage.success(`Anda akan diarahkan untuk melamar posisi ${selectedJob.value.posisi}`)

    // For now, just show a message and close modal
    showDetailModal.value = false

    // You could redirect to registration/login or application form
    // window.location.href = route('register')
}

onMounted(() => {
    console.log('Lowongan data:', props.data)
})
</script>