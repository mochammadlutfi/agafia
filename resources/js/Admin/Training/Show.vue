<template>
    <base-layout title="Detail Training">
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-0">Detail Training #{{ data?.id }}</h2>
                    <small class="text-muted">{{ data?.user?.nama }}</small>
                </div>
                <div class="space-x-1">
                    <el-button :tag="Link" :href="route('admin.training.index')" type="default" size="large">
                        <i class="fa fa-arrow-left me-1"></i>
                        Kembali
                    </el-button>
                    <el-button type="danger" @click.prevent="hapus(data.id)">
                        <i class="si si-trash me-1"></i>
                        Hapus
                    </el-button>
                </div>
            </div>

            <!-- Training Info Card -->
            <div class="block block-rounded mb-4">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-graduation-cap me-2"></i>
                        Informasi Training
                    </h3>
                    <div class="block-options">
                        <el-tag :type="getStatusType(data?.status)" size="large">
                            {{ getStatusLabel(data?.status) }}
                        </el-tag>
                    </div>
                </div>
                <div class="block-content p-4">
                    <el-row :gutter="24">
                        <el-col :span="12">
                            <el-descriptions title="Detail Training" column="1" border>
                                <el-descriptions-item label="Program">
                                    {{ data?.program?.nama }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Tanggal Daftar">
                                    {{ formatDate(data?.tanggal_daftar) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Tanggal Mulai">
                                    {{ formatDate(data?.tanggal_mulai) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Tanggal Selesai">
                                    {{ formatDate(data?.tanggal_selesai) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Status">
                                    <el-tag :type="getStatusType(data?.status)">
                                        {{ getStatusLabel(data?.status) }}
                                    </el-tag>
                                </el-descriptions-item>
                                <el-descriptions-item label="Nilai Akhir">
                                    {{ data?.nilai_akhir || '-' }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Sertifikat">
                                    <el-tag :type="data?.sertifikat_diterbitkan ? 'success' : 'info'" size="small">
                                        {{ data?.sertifikat_diterbitkan ? 'Sudah Diterbitkan' : 'Belum Diterbitkan' }}
                                    </el-tag>
                                </el-descriptions-item>
                                <el-descriptions-item label="No. Sertifikat" v-if="data?.sertifikat_diterbitkan">
                                    {{ data?.nomor_sertifikat || '-' }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Catatan" v-if="data?.catatan">
                                    {{ data?.catatan }}
                                </el-descriptions-item>
                            </el-descriptions>
                        </el-col>
                        <el-col :span="12">
                            <el-descriptions title="Informasi Peserta" column="1" border>
                                <el-descriptions-item label="Nama Lengkap">
                                    {{ data?.user?.nama }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Email">
                                    <el-link type="primary" :href="`mailto:${data?.user?.email}`">
                                        {{ data?.user?.email }}
                                    </el-link>
                                </el-descriptions-item>
                                <el-descriptions-item label="No. Handphone">
                                    <el-link type="primary" :href="`tel:${data?.user?.detail?.phone}`">
                                        {{ data?.user?.detail?.phone || '-' }}
                                    </el-link>
                                </el-descriptions-item>
                                <el-descriptions-item label="Didaftarkan Oleh">
                                    {{ data?.staff?.nama || '-' }}
                                </el-descriptions-item>
                            </el-descriptions>
                        </el-col>
                    </el-row>
                </div>
            </div>

            <!-- Program Details -->
            <div class="block block-rounded" v-if="data?.program">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-book me-2"></i>
                        Detail Program
                    </h3>
                </div>
                <div class="block-content p-4">
                    <el-descriptions :column="2" border>
                        <el-descriptions-item label="Nama Program">
                            {{ data?.program?.nama }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Durasi">
                            {{ data?.program?.durasi || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Deskripsi" :span="2">
                            {{ data?.program?.deskripsi || '-' }}
                        </el-descriptions-item>
                    </el-descriptions>
                </div>
            </div>
        </div>
    </base-layout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ElMessage, ElMessageBox } from 'element-plus'
import moment from 'moment'

const props = defineProps({
    data: Object
})

// Status helpers
const getStatusLabel = (status) => {
    const labels = {
        'terdaftar': 'Terdaftar',
        'sedang_pelatihan': 'Sedang Pelatihan',
        'selesai': 'Selesai',
        'mengundurkan_diri': 'Mengundurkan Diri'
    }
    return labels[status] || status
}

const getStatusType = (status) => {
    const types = {
        'terdaftar': 'info',
        'sedang_pelatihan': 'warning',
        'selesai': 'success',
        'mengundurkan_diri': 'danger'
    }
    return types[status] || 'info'
}

// Format helpers
const formatDate = (date) => {
    if (!date) return '-'
    return moment(date).format('DD MMMM YYYY')
}

// Delete function
const hapus = (id) => {
    ElMessageBox.alert('Data yang dihapus tidak bisa dikembalikan!', 'Peringatan', {
        showCancelButton: true,
        confirmButtonText: 'Ya!',
        cancelButtonText: 'Tidak!',
        type: 'warning',
    }).then(() => {
        router.delete(`/admin/training/${id}`, {
            preserveScroll: true,
            onSuccess: () => {
                ElMessage({
                    type: 'success',
                    message: 'Data Berhasil Dihapus!',
                })
                router.visit(route('admin.training.index'))
            },
        })
    })
}
</script>

<style scoped>
.space-x-1 > * + * {
    margin-left: 0.25rem;
}
</style>