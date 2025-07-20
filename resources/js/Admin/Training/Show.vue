<template>
    <base-layout title="Detail Training">
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-0">Detail Training #{{ data?.id }}</h2>
                    <small class="text-muted">{{ data?.user?.nama }}</small>
                </div>
                <div class="space-x-1">
                    <el-button type="danger" @click.prevent="openHasilModal">
                        <i class="fa fa-certificate me-1"></i>
                        Hasil Training
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
        <el-dialog
            v-model="hasilModal"
            title="Hasil Training"
            width="500">
            <el-form label-position="top" ref="hasilFormRef" :model="hasilForm" :rules="hasilRules" label-width="100%" @submit.prevent="onSubmit" v-loading="loadingForm">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <el-form-item label="Nilai Akhir" prop="nilai_akhir">
                            <el-input-number
                                v-model="hasilForm.nilai_akhir"
                                :min="0"
                                :max="100"
                                :step="1"
                                :precision="0"
                                placeholder="Nilai 0-100"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Sertifikat">
                            <el-checkbox v-model="hasilForm.sertifikat_diterbitkan">
                                Sertifikat sudah diterbitkan
                            </el-checkbox>
                        </el-form-item>
                    </el-col>
                    <el-col :md="24">
                        <el-form-item label="No. Sertifikat" v-if="hasilForm.sertifikat_diterbitkan">
                            <el-input v-model="hasilForm.nomor_sertifikat" placeholder="Nomor sertifikat" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="Catatan" prop="catatan">
                            <el-input v-model="hasilForm.catatan" type="textarea" :rows="4"/>
                        </el-form-item>
                    </el-col>
                </el-row>
                
                <div class="text-end">
                    <el-button @click="handleTrainingModalClose">Cancel</el-button>
                    <el-button type="primary" native-type="submit">
                    Confirm
                    </el-button>
                </div>
            </el-form>
        </el-dialog>
    </base-layout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Link, router } from '@inertiajs/vue3'
import moment from 'moment'

const props = defineProps({
    data: Object
})

const hasilModal = ref(false)
const hasilLoading = ref(false)
const hasilFormRef = ref()

// Training Form
const hasilForm = reactive({
    nilai_akhir: null,
    sertifikat_diterbitkan: false,
    nomor_sertifikat: '',
    catatan: ''
})

const hasilRules = {
    nilai_akhir: [{ required: true, message: 'Nilai akhir wajib diisi', trigger: 'blur' }],
}


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

const openHasilModal = () => {
    hasilForm.nilai_akhir = props.data.nilai_akhir || null
    hasilForm.sertifikat_diterbitkan = props.data.sertifikat_diterbitkan || false
    hasilForm.nomor_sertifikat = props.data.nomor_sertifikat || ''
    hasilForm.catatan = props.data.catatan || ''
    hasilModal.value = true
}

// Modal handlers
const handleTrainingModalClose = () => {
    hasilFormRef.value?.resetFields();
    hasilModal.value = false;
    hasilLoading.value = false;
}

// Form submission
const onSubmit = async () => {
    if (!hasilFormRef.value) return
    
    await hasilFormRef.value.validate((valid) => {
        if (valid) {
            
            hasilLoading.value = true
            
            try {
                const url = route('admin.training.hasil', {id : props.data.id})
                axios.post(url, hasilForm)
                
                ElMessage.success('Pelatihan berhasil didaftarkan')
                hasilModal.value = false
                
                // Refresh page data
                router.visit(route('admin.training.show', props.data.id), {
                    preserveState: false,
                    preserveScroll: true
                })
            } catch (error) {
                console.error('Error scheduling training:', error)
                ElMessage.error('Gagal mendaftarkan pelatihan')
            } finally {
                hasilLoading.value = false
            }
        }
    })
}

</script>

<style scoped>
.space-x-1 > * + * {
    margin-left: 0.25rem;
}
</style>