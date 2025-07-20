<template>
    <div class="px-2">
        <div class="align-middle border-bottom d-flex justify-content-between mb-4 py-2">
            <div class="fs-6 fw-semibold">Data Training</div>
            <el-button 
                @click="showTrainingModal = true"
                type="primary"
                v-if="canSchedule"
            >
                Daftarkan Training
            </el-button>
        </div>

        <el-table 
            :data="training || []" 
            border 
            style="width: 100%"
            :empty-text="'Belum ada data pelatihan'"
        >
            <el-table-column prop="program.nama" label="Program" />
            <el-table-column prop="tanggal_daftar" label="Tanggal Daftar" width="120">
                <template #default="{ row }">
                    {{ formatDate(row.tanggal_daftar) }}
                </template>
            </el-table-column>
            <el-table-column prop="status" label="Status" width="150">
                <template #default="{ row }">
                    <el-tag :type="getStatusType(row.status)" size="small">
                        {{ getStatusLabel(row.status) }}
                    </el-tag>
                </template>
            </el-table-column>
            <el-table-column prop="nilai_akhir" label="Nilai" width="80" align="center" />
            <el-table-column label="Aksi" width="120">
                <template #default="{ row }">
                    <el-button 
                        @click="$emit('view-training', row.id)"
                        type="primary" 
                        size="small"
                    >
                        Detail
                    </el-button>
                </template>
            </el-table-column>
        </el-table>

        <!-- Training Modal -->
        <el-dialog
            v-model="showTrainingModal"
            title="Daftarkan Pelatihan"
            width="600px"
            :before-close="handleTrainingModalClose"
        >
            <el-form :model="trainingForm" label-position="top" :rules="trainingRules" ref="trainingFormRef" label-width="150px">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <el-form-item label="Program Pelatihan" prop="program_id">
                            <select-program v-model="trainingForm.program_id"/>
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Tanggal Daftar" prop="tanggal_daftar">
                            <el-date-picker
                                v-model="trainingForm.tanggal_daftar"
                                type="date"
                                placeholder="Pilih tanggal daftar"
                                format="DD/MM/YYYY"
                                value-format="YYYY-MM-DD"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Tanggal Mulai" prop="tanggal_mulai">
                            <el-date-picker
                                v-model="trainingForm.tanggal_mulai"
                                type="date"
                                placeholder="Pilih tanggal mulai"
                                format="DD/MM/YYYY"
                                value-format="YYYY-MM-DD"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Tanggal Selesai" prop="tanggal_selesai">
                            <el-date-picker
                                v-model="trainingForm.tanggal_selesai"
                                type="date"
                                placeholder="Pilih tanggal selesai"
                                format="DD/MM/YYYY"
                                value-format="YYYY-MM-DD"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="Catatan">
                    <el-input
                        v-model="trainingForm.catatan"
                        type="textarea"
                        :rows="3"
                        placeholder="Catatan tambahan (opsional)"
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showTrainingModal = false">Batal</el-button>
                    <el-button type="success" @click="submitTrainingForm" :loading="trainingLoading">
                        Daftarkan
                    </el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { ElMessage } from 'element-plus'
import { router } from '@inertiajs/vue3'
import moment from 'moment'
import axios from 'axios'
import SelectProgram from '@/Components/SelectProgram.vue'

const props = defineProps({
    training: Array,
    canSchedule: Boolean,
    lamaranId: [String, Number]
})

defineEmits(['view-training', 'training-updated'])

// Modal and form state
const showTrainingModal = ref(false)
const trainingLoading = ref(false)
const trainingFormRef = ref()

// Training Form
const trainingForm = reactive({
    lamaran_id: props.lamaranId,
    program_id: '',
    tanggal_daftar: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    status: 'terdaftar',
    nilai_akhir: null,
    sertifikat_diterbitkan: false,
    nomor_sertifikat: '',
    catatan: ''
})

const trainingRules = {
    program_id: [{ required: true, message: 'Program pelatihan wajib dipilih', trigger: 'change' }],
    tanggal_daftar: [{ required: true, message: 'Tanggal daftar wajib diisi', trigger: 'blur' }],
    tanggal_mulai: [{ required: true, message: 'Tanggal mulai wajib diisi', trigger: 'blur' }],
    tanggal_selesai: [{ required: true, message: 'Tanggal selesai wajib diisi', trigger: 'blur' }]
}




// Modal handlers
const handleTrainingModalClose = (done) => {
    trainingFormRef.value?.resetFields()
    done()
}

// Form submission
const submitTrainingForm = async () => {
    if (!trainingFormRef.value) return
    
    await trainingFormRef.value.validate((valid) => {
        if (valid) {
            scheduleTraining()
        }
    })
}

const scheduleTraining = async () => {
    trainingLoading.value = true
    
    try {
        const url = (trainingForm.id) ? route('admin.training.update', {id : trainingForm.id}) : route('admin.training.store')
        await axios.post(url, trainingForm)
        
        ElMessage.success('Pelatihan berhasil didaftarkan')
        showTrainingModal.value = false
        
        // Refresh page data
        router.visit(route('admin.lamaran.show', props.lamaranId), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error scheduling training:', error)
        ElMessage.error('Gagal mendaftarkan pelatihan')
    } finally {
        trainingLoading.value = false
    }
}

// Helper functions
const formatDate = (value) => {
    if (value) {
        return moment(String(value)).format('DD MMM YYYY')
    }
}

const getStatusType = (status) => {
    switch (status) {
        case 'terdaftar':
            return 'info'
        case 'proses':
            return 'warning'
        case 'lulus':
            return 'success'
        case 'tidak_lulus':
            return 'danger'
        default:
            return 'info'
    }
}

const getStatusLabel = (status) => {
    const labels = {
        'terdaftar': 'Terdaftar',
        'proses': 'Sedang Proses',
        'lulus': 'Lulus',
        'tidak_lulus': 'Tidak Lulus'
    }
    return labels[status] || status
}
</script>