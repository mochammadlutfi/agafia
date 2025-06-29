<template>
    <base-layout>
        <!-- Breadcrumb -->
        <div class="bg-light py-3 mb-4">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <Link :href="route('home')" class="text-decoration-none">
                                <i class="fa fa-home me-1"></i>Home
                            </Link>
                        </li>
                        <li class="breadcrumb-item">
                            <Link :href="route('lowongan')" class="text-decoration-none">
                                Lowongan
                            </Link>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ job.posisi }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="container">
                <div class="row">
                    <!-- Job Details -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4">
                            <!-- Job Header -->
                            <div class="card-header bg-primary text-white">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h2 class="mb-1 text-white">{{ job.posisi }}</h2>
                                        <h5 class="mb-0 text-white-50">
                                            <i class="fa fa-building me-2"></i>
                                            {{ job.perusahaan }}
                                        </h5>
                                    </div>
                                    <div class="col-auto">
                                        <el-tag :type="job.status === 'buka' ? 'success' : 'danger'" size="large">
                                            {{ job.status_label }}
                                        </el-tag>
                                    </div>
                                </div>
                            </div>

                            <!-- Job Image -->
                            <div v-if="job.foto" class="p-0">
                                <img 
                                    :src="job.foto" 
                                    class="w-100" 
                                    style="height: 300px; object-fit: cover;"
                                    :alt="job.posisi"
                                />
                            </div>

                            <!-- Job Info -->
                            <div class="card-body">
                                <!-- Quick Info -->
                                <div class="row mb-4">
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="fa fa-map-marker-alt text-primary fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-muted">Lokasi</h6>
                                                <strong>{{ job.lokasi }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="fa fa-users text-success fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-muted">Kuota</h6>
                                                <strong>{{ job.kuota }} Posisi</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="fa fa-tools text-info fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-muted">Skill Required</h6>
                                                <strong>{{ job.skill }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Job Description -->
                                <div class="mb-4">
                                    <h4 class="fw-bold mb-3">
                                        <i class="fa fa-info-circle text-primary me-2"></i>
                                        Deskripsi Pekerjaan
                                    </h4>
                                    <div class="bg-light rounded p-4">
                                        <p class="mb-0 lh-lg" style="white-space: pre-line;">{{ job.deskripsi }}</p>
                                    </div>
                                </div>

                                <!-- Requirements Section -->
                                <div class="mb-4">
                                    <h4 class="fw-bold mb-3">
                                        <i class="fa fa-check-circle text-success me-2"></i>
                                        Persyaratan
                                    </h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fa fa-check text-success me-2"></i>
                                                    Memiliki skill {{ job.skill }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fa fa-check text-success me-2"></i>
                                                    Siap ditempatkan di {{ job.lokasi }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fa fa-check text-success me-2"></i>
                                                    Bersedia mengikuti pelatihan
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fa fa-check text-success me-2"></i>
                                                    Memiliki dokumen lengkap
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fa fa-check text-success me-2"></i>
                                                    Sehat jasmani dan rohani
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fa fa-check text-success me-2"></i>
                                                    Komitmen bekerja profesional
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Application Process -->
                                <div class="mb-4">
                                    <h4 class="fw-bold mb-3">
                                        <i class="fa fa-route text-warning me-2"></i>
                                        Alur Pendaftaran
                                    </h4>
                                    <div class="row">
                                        <div class="col-md-3 text-center mb-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" 
                                                 style="width: 50px; height: 50px;">
                                                <span class="fw-bold">1</span>
                                            </div>
                                            <small class="text-muted">Daftar Online</small>
                                        </div>
                                        <div class="col-md-3 text-center mb-3">
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" 
                                                 style="width: 50px; height: 50px;">
                                                <span class="fw-bold">2</span>
                                            </div>
                                            <small class="text-muted">Seleksi & Interview</small>
                                        </div>
                                        <div class="col-md-3 text-center mb-3">
                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" 
                                                 style="width: 50px; height: 50px;">
                                                <span class="fw-bold">3</span>
                                            </div>
                                            <small class="text-muted">Medical & Training</small>
                                        </div>
                                        <div class="col-md-3 text-center mb-3">
                                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" 
                                                 style="width: 50px; height: 50px;">
                                                <span class="fw-bold">4</span>
                                            </div>
                                            <small class="text-muted">Penempatan Kerja</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Apply Card -->
                        <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fa fa-paper-plane me-2"></i>
                                    Lamar Posisi Ini
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <h4 class="text-primary fw-bold">{{ job.posisi }}</h4>
                                    <p class="text-muted mb-0">di {{ job.perusahaan }}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="bg-light rounded p-3">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="fw-bold text-primary">{{ job.kuota }}</div>
                                                <small class="text-muted">Kuota Tersedia</small>
                                            </div>
                                            <div class="col-6">
                                                <div class="fw-bold text-success">{{ formatDate(job.created_at) }}</div>
                                                <small class="text-muted">Dipublikasikan</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <el-button 
                                        type="primary" 
                                        size="large"
                                        @click="applyJob"
                                        :disabled="!page.props.user || job.status !== 'buka'"
                                        class="w-100"
                                    >
                                        <i class="fa fa-paper-plane me-2"></i>
                                        {{ getApplyButtonText() }}
                                    </el-button>
                                    
                                    <el-button 
                                        v-if="!page.props.user"
                                        type="success" 
                                        plain
                                        size="large"
                                        :tag="Link"
                                        :href="route('register')"
                                        class="w-100"
                                    >
                                        <i class="fa fa-user-plus me-2"></i>
                                        Daftar Akun Dulu
                                    </el-button>
                                </div>
                            </div>
                        </div>

                        <!-- Company Info -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fa fa-building me-2"></i>
                                    Tentang Perusahaan
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold">{{ job.perusahaan }}</h6>
                                <div class="mb-2">
                                    <i class="fa fa-map-marker-alt text-muted me-2"></i>
                                    <span class="text-muted">{{ job.lokasi }}</span>
                                </div>
                                <p class="text-muted small mb-0">
                                    Perusahaan terpercaya yang bekerja sama dengan PT. Agafia Adda Mandiri 
                                    untuk penyediaan tenaga kerja profesional.
                                </p>
                            </div>
                        </div>

                        <!-- Related Jobs -->
                        <div v-if="relatedJobs.length > 0" class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fa fa-list me-2"></i>
                                    Lowongan Lainnya
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div 
                                    v-for="relatedJob in relatedJobs" 
                                    :key="relatedJob.id"
                                    class="border-bottom p-3"
                                >
                                    <h6 class="mb-1">
                                        <Link 
                                            :href="route('lowongan.detail', relatedJob.id)" 
                                            class="text-decoration-none text-primary"
                                        >
                                            {{ relatedJob.posisi }}
                                        </Link>
                                    </h6>
                                    <p class="text-muted small mb-1">{{ relatedJob.perusahaan }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fa fa-map-marker-alt me-1"></i>
                                            {{ relatedJob.lokasi }}
                                        </small>
                                        <el-tag size="small" :type="relatedJob.status === 'buka' ? 'success' : 'danger'">
                                            {{ relatedJob.status_label }}
                                        </el-tag>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </base-layout>
</template>

<script setup>
import BaseLayout from './Layouts/BaseLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ElMessage } from 'element-plus'

const props = defineProps({
    job: Object,
    relatedJobs: Array
})

const page = usePage()

// Methods
const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}

const getApplyButtonText = () => {
    if (!props.job) return 'Loading...'
    
    if (props.job.status !== 'buka') {
        return 'Lowongan Ditutup'
    }
    
    if (!page.props.user) {
        return 'Login untuk Melamar'
    }
    
    return 'Lamar Sekarang'
}

const applyJob = () => {
    if (!props.job) return
    
    if (props.job.status !== 'buka') {
        ElMessage.warning('Maaf, lowongan ini sudah ditutup')
        return
    }
    
    if (!page.props.user) {
        ElMessage.info('Silakan login terlebih dahulu untuk melamar pekerjaan')
        return
    }
    
    // Here you would typically redirect to application form or handle the application
    ElMessage.success(`Anda akan diarahkan untuk melamar posisi ${props.job.posisi}`)
    
    // You could redirect to registration/login or application form
    // window.location.href = route('register')
}
</script>

<style scoped>
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.bg-light {
    background-color: #f8f9fa !important;
}

.sticky-top {
    position: sticky;
    z-index: 1020;
}
</style>