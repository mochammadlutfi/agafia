<template>
    <div class="px-2">
        <div class="align-middle border-bottom d-flex justify-content-between mb-4 py-2">
            <div class="fs-6 fw-semibold">Data Medical Checkup</div>
            
            <div v-if="medical.status == 'pending'">
                <el-button type="success" @click.prevent="onConfirm">
                    <i class="si si-check me-1"></i>
                    Terima
                </el-button>
                <el-button type="danger" @click.prevent="onReject">
                    <i class="si si-close me-1"></i>
                    Tolak
                </el-button>
            </div>
        </div>
        <div v-if="medical">
            <el-descriptions :column="2" border direction="horizontal">
                <el-descriptions-item label="Nama Fasilitas Kesehatan">
                    {{ medical.nama }}
                </el-descriptions-item>
                <el-descriptions-item label="Tanggal Pengecekan">
                    {{ formatDate(medical.tanggal) }}
                </el-descriptions-item>
                <el-descriptions-item label="Hasil">
                    {{ medical.hasil }}
                </el-descriptions-item>
                <el-descriptions-item label="Dokumen File">
                    <span v-if="!medical.file">-</span>
                    <el-button 
                        v-else 
                        tag="a" 
                        :href="'/uploads/'+medical.file" 
                        target="_blank" 
                        type="primary" 
                        size="small"
                    >
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>                        
                </el-descriptions-item>
                <el-descriptions-item label="Status">
                    <el-tag :type="getStatusType(medical.status)" size="small">
                        {{ medical.status_label }}
                    </el-tag>
                </el-descriptions-item>
                <el-descriptions-item label="Catatan" :span="2" label-position="top">
                    {{ medical.catatan }}
                </el-descriptions-item>
            </el-descriptions>
        </div>
        <el-empty v-else>
            <el-button 
                @click="showMedicalModal = true"
                type="primary"
                v-if="canSchedule"
            >
                Upload Medical Checkup
            </el-button>
        </el-empty>

        <!-- Medical Check Modal -->
        <el-dialog
            v-model="showMedicalModal"
            title="Jadwalkan Medical Check Up"
            width="600px"
            :before-close="handleMedicalModalClose"
        >
            <el-form :model="medicalForm" :rules="medicalRules" ref="medicalFormRef" label-position="top" label-width="150px">
                <el-form-item label="Tanggal Medical" prop="tanggal">
                    <el-date-picker
                        v-model="medicalForm.tanggal"
                        type="date"
                        placeholder="Pilih tanggal"
                        format="DD/MM/YYYY"
                        value-format="YYYY-MM-DD"
                        style="width: 100%"
                    />
                </el-form-item>
                <el-form-item label="Nama Fasilitas Kesehatan" prop="nama">
                    <el-input v-model="medicalForm.nama" placeholder="Nama Fasilitas Kesehatan (misal:Rumah Sakit Bunga, dll)" />
                </el-form-item>
                <el-form-item label="Upload File" prop="file">
                    <single-file-upload v-model="medicalForm.file"/>
                </el-form-item>
                <el-form-item label="Hasil" prop="hasil">
                    <el-input v-model="medicalForm.hasil" placeholder="Hasil medical check" />
                </el-form-item>
                <el-form-item label="Status" prop="status">
                    <el-select v-model="medicalForm.status" placeholder="Pilih status" style="width: 100%">
                        <el-option label="Pending" value="pending" />
                        <el-option label="Lulus" value="lulus" />
                        <el-option label="Tidak Lulus" value="tidak_lulus" />
                    </el-select>
                </el-form-item>
                <el-form-item label="Catatan">
                    <el-input
                        v-model="medicalForm.catatan"
                        type="textarea"
                        :rows="3"
                        placeholder="Catatan tambahan (opsional)"
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showMedicalModal = false">Batal</el-button>
                    <el-button type="info" @click="submitMedicalForm" :loading="medicalLoading">
                        Simpan
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
import SingleFileUpload from '@/Components/SingleFileUpload.vue'

const props = defineProps({
    medical: Object,
    canSchedule: Boolean,
    lamaranId: [String, Number],
    userId: [String, Number]
})

defineEmits(['medical-updated'])

// Modal and form state
const showMedicalModal = ref(false)
const medicalLoading = ref(false)
const medicalFormRef = ref()

// Medical Form
const medicalForm = reactive({
    lamaran_id: props.lamaranId,
    user_id: props.userId,
    tanggal: '',
    nama: '',
    hasil: '',
    file: null,
    catatan: '',
    status: 'pending'
})

const medicalRules = {
    tanggal: [{ required: true, message: 'Tanggal medical wajib diisi', trigger: 'blur' }],
    nama: [{ required: true, message: 'Nama fasilitas kesehatan wajib diisi', trigger: 'blur' }],
    hasil: [{ required: true, message: 'Hasil medical wajib diisi', trigger: 'blur' }],
    status: [{ required: true, message: 'Status wajib dipilih', trigger: 'change' }]
}

// Modal handlers
const handleMedicalModalClose = (done) => {
    medicalFormRef.value?.resetFields()
    medicalForm.file = null
    done()
}

// Form submission
const submitMedicalForm = async () => {
    if (!medicalFormRef.value) return
    
    await medicalFormRef.value.validate((valid) => {
        if (valid) {
            scheduleMedical()
        }
    })
}

const scheduleMedical = async () => {
    medicalLoading.value = true
    
    try {
        await axios.post(route('admin.medical.store'), medicalForm)
        
        ElMessage.success('Medical check up berhasil dijadwalkan')
        showMedicalModal.value = false
        
        // Refresh page data
        router.visit(route('admin.lamaran.show', props.lamaranId), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error scheduling medical:', error)
        ElMessage.error('Gagal menjadwalkan medical check up')
    } finally {
        medicalLoading.value = false
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
        case 'pending':
            return 'warning'
        case 'lulus':
            return 'success'
        case 'tidak_lulus':
            return 'danger'
        default:
            return 'info'
    }
}


const onConfirm = () => {
    ElMessageBox.confirm('Apakah Anda yakin ingin menyatakan dokumen medical checkup ini **valid**?', 'Validasi Dokumen Medical Checkup', {
        confirmButtonText: 'Ya, Terima',
        cancelButtonText: 'Tidak, Batalkan',
        type: 'warning'
    })
    .then(() => {
        const loading = ElLoading.service({
            lock: true,
            text: 'Loading',
        });
        axios.post(route('admin.medical.state', {id : props.medical.id}), {
            state: 'valid'
        })
        .then(response => {
            ElMessage.success('Dokumen medical checkup berhasil di konfirmasi.');
            loading.close();
            router.visit(route('admin.lamaran.show', props.lamaranId), {
                preserveState: false,
                preserveScroll: true
            })
        })
        .catch(error => {
            // Handle error response
            console.error('Error confirming user:', error);
            ElMessage.info('Konfirmasi dibatalkan.');
        });
    })
    .catch(() => {
        ElMessage.info('Konfirmasi dibatalkan.');
    });
};

const onReject = () => {
  ElMessageBox.prompt('Alasan Penolakan', 'Validasi Dokumen Medical Checkup', {
    confirmButtonText: 'Kirim',
    cancelButtonText: 'Batal',
    inputType : 'textarea'
  })
    .then(({ value }) => {
        const loading = ElLoading.service({
            lock: true,
            text: 'Loading',
        });
        axios.post(route('admin.medical.state', {id : props.medical.id}), {
            state: 'tidak_valid',
            reason: value
        })
        .then(response => {
            ElMessage.success('Dokumen medical checkup tidak valid.');
            loading.close();
            router.visit(route('admin.lamaran.show', props.lamaranId), {
                preserveState: false,
                preserveScroll: true
            })
        })
    })
    .catch(() => {
      ElMessage({
        type: 'info',
        message: 'Input canceled',
      })
    })
}
</script>