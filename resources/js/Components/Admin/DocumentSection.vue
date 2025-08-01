<template>
    <div class="px-2">
        <div class="align-middle border-bottom d-flex justify-content-between mb-4 py-2">
            <div class="fs-6 fw-semibold">Data Dokumen</div>
            <div>
                <template v-if="['owner', 'admin'].includes($page.props.user.level)">
                    <el-button 
                        @click="openDocumentModal"
                        type="primary"
                        size="small">
                        <i class="fa fa-plus me-1"></i>
                        Upload Dokumen
                    </el-button>
                    <el-button 
                        @click="showDocumentStatusModalFunc"
                        type="warning" 
                        size="small"
                        v-if="dokumen"
                    >
                        <i class="fa fa-check me-1"></i>
                        Review Dokumen
                    </el-button>
                </template>
            </div>
        </div>
        
        <div v-if="dokumen">
            <!-- Document Status Overview -->
            <el-card class="mb-4">
                <template #header>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Status Review Dokumen</span>
                    </div>
                </template>
                <el-row :gutter="16">
                    <el-col :span="8">
                        <el-statistic title="Status" :value="dokumen.status_label">
                            <template #suffix>
                                <el-tag :type="dokumen.status_color" size="small" class="ms-2">
                                    {{ dokumen.status_label }}
                                </el-tag>
                            </template>
                        </el-statistic>
                    </el-col>
                    <el-col :span="16">
                        <div>
                            <strong>Catatan Review:</strong>
                            <p class="mt-1 mb-0 text-muted">{{ dokumen.catatan || 'Belum ada catatan review' }}</p>
                        </div>
                    </el-col>
                </el-row>
            </el-card>
            
            <!-- Document List -->
            <el-descriptions :column="2" border direction="horizontal">
                <el-descriptions-item :label="doc.label" v-for="(doc, index) in documentTableData" :key="index">
                    <el-button 
                        v-if="doc.file"
                        tag="a" 
                        :href="'/uploads/' + doc.file" 
                        target="_blank" 
                        type="primary" 
                        size="small"
                    >
                        <i class="fa fa-eye me-1"></i>
                        Lihat
                    </el-button>
                    <span v-else class="text-muted">-</span>
                </el-descriptions-item>
            </el-descriptions>
        </div>
        <el-empty v-else>
            <el-button 
                @click="openDocumentModal"
                type="primary">
                Upload Dokumen
            </el-button>
        </el-empty>

        <!-- Document Upload Modal -->
        <el-dialog
            v-model="showDokumenModal"
            title="Upload Dokumen"
            width="800px"
            :before-close="handleDokumenModalClose">
            <el-form label-position="top" :model="dokumenForm" label-width="100%" :rules="dokumenRules" ref="dokumenFormRef">
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="KTP" prop="ktp">
                            <single-file-upload v-model="dokumenForm.ktp" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Kartu Keluarga" prop="kk">
                            <single-file-upload v-model="dokumenForm.kk" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Akte Kelahiran" prop="akte_lahir">
                            <single-file-upload v-model="dokumenForm.akte_lahir" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Buku Nikah (Jika ada)" prop="buku_nikah">
                            <single-file-upload v-model="dokumenForm.buku_nikah" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Surat Keterangan Sehat" prop="surat_keterangan_sehat">
                            <single-file-upload v-model="dokumenForm.surat_keterangan_sehat" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Surat Izin Keluarga" prop="surat_izin_keluarga">
                            <single-file-upload v-model="dokumenForm.surat_izin_keluarga" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Ijazah Terakhir" prop="ijazah">
                            <single-file-upload v-model="dokumenForm.ijazah" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Sertifikat Keahlian/ Kompetensi" prop="sertifikat_keahlian">
                            <single-file-upload v-model="dokumenForm.sertifikat_keahlian" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Paspor" prop="paspor">
                            <single-file-upload v-model="dokumenForm.paspor" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Surat Keterangan Pengalaman Kerja" prop="surat_pengalaman">
                            <single-file-upload v-model="dokumenForm.surat_pengalaman" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="SKCK" prop="skck">
                            <single-file-upload v-model="dokumenForm.skck" />
                        </el-form-item>
                    </el-col>
                </el-row>
                
                <div class="text-end">
                    <el-button @click="showDokumenModal = false">Cancel</el-button>
                    <el-button type="success" @click="submitDokumen" :loading="dokumenLoading">
                        Simpan
                    </el-button>
                </div>
            </el-form>
        </el-dialog>

        <!-- Document Status Update Modal -->
        <el-dialog
            v-model="showDocumentStatusModal"
            title="Review Status Dokumen"
            width="500px"
        >
            <el-form :model="statusForm" :rules="statusRules" ref="statusFormRef" label-position="top">
                <el-alert
                    title="Review Keseluruhan Dokumen"
                    type="info"
                    description="Status dan catatan ini akan berlaku untuk semua dokumen yang telah diupload."
                    show-icon
                    :closable="false"
                    class="mb-3"
                />
                
                <el-form-item label="Status Review" prop="status">
                    <el-select v-model="statusForm.status" placeholder="Pilih status" style="width: 100%">
                        <el-option label="Pending Review" value="pending" />
                        <el-option label="Diterima" value="diterima" />
                        <el-option label="Ditolak" value="ditolak" />
                    </el-select>
                </el-form-item>
                
                <el-form-item label="Catatan Review" prop="catatan">
                    <el-input
                        v-model="statusForm.catatan"
                        type="textarea"
                        :rows="4"
                        placeholder="Berikan catatan review untuk keseluruhan dokumen (wajib diisi)"
                    />
                </el-form-item>
                
                <!-- Document Summary -->
                <el-form-item label="Ringkasan Dokumen">
                    <div class="border rounded p-3 bg-light">
                        <el-row :gutter="16">
                            <el-col :span="12" v-for="doc in documentTableData" :key="doc.field" class="mb-2">
                                <div class="d-flex align-items-center">
                                    <el-tag :type="doc.file ? 'success' : 'danger'" size="small" class="me-2">
                                        <i :class="doc.file ? 'fa fa-check' : 'fa fa-times'"></i>
                                    </el-tag>
                                    <span class="small">{{ doc.label }}</span>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showDocumentStatusModal = false">Batal</el-button>
                    <el-button type="primary" @click="submitStatusUpdate" :loading="statusLoading">
                        Simpan
                    </el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { ElMessage } from 'element-plus'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import SingleFileUpload from '@/Components/SingleFileUpload.vue'

const props = defineProps({
    dokumen: Object,
    lamaranId: [String, Number]
})

defineEmits(['document-updated'])

// Modal and form state
const showDokumenModal = ref(false)
const showDocumentStatusModal = ref(false)
const dokumenLoading = ref(false)
const statusLoading = ref(false)
const dokumenFormRef = ref()
const statusFormRef = ref()

// Document Form
const dokumenForm = reactive({
    lamaran_id: props.lamaranId,
    ktp: null,
    kk: null,
    akte_lahir: null,
    buku_nikah: null,
    surat_keterangan_sehat: null,
    surat_izin_keluarga: null,
    ijazah: null,
    sertifikat_keahlian: null,
    paspor: null,
    surat_pengalaman: null,
    skck: null
})

// Status Form
const statusForm = reactive({
    id: null,
    status: '',
    catatan: ''
})

const dokumenRules = {
    ktp: [{ required: true, message: 'KTP wajib diisi', trigger: 'change' }],
    kk: [{ required: true, message: 'Kartu keluarga wajib diisi', trigger: 'change' }],
    akte_lahir: [{ required: true, message: 'Akte lahir wajib diisi', trigger: 'change' }],
    surat_keterangan_sehat: [{ required: true, message: 'Surat keterangan sehat wajib diisi', trigger: 'change' }],
    surat_izin_keluarga: [{ required: true, message: 'Surat izin keluarga wajib diisi', trigger: 'change' }],
    ijazah: [{ required: true, message: 'Ijazah wajib diisi', trigger: 'change' }],
    sertifikat_keahlian: [{ required: false, message: 'Sertifikat keahlian wajib diisi', trigger: 'change' }],
    paspor: [{ required: true, message: 'Paspor wajib diisi', trigger: 'change' }],
    surat_pengalaman: [{ required: false, message: 'Surat pengalaman kerja wajib diisi', trigger: 'change' }],
    skck: [{ required: true, message: 'SKCK wajib diisi', trigger: 'change' }],
}

const statusRules = {
    status: [{ required: true, message: 'Status wajib dipilih', trigger: 'change' }],
    catatan: [{ required: true, message: 'Catatan review wajib diisi', trigger: 'blur' }]
}

// Modal handlers
const handleDokumenModalClose = (done) => {
    dokumenFormRef.value?.resetFields()
    Object.keys(dokumenForm).forEach(key => {
        if (key !== 'lamaran_id') {
            dokumenForm[key] = null
        }
    })
    done()
}

const openDocumentModal = () => {
    if (props.dokumen) {
        // Pre-fill form with existing data
        Object.keys(dokumenForm).forEach(key => {
            if (props.dokumen[key] && key !== 'lamaran_id') {
                dokumenForm[key] = props.dokumen[key]
            }
        })
    }
    showDokumenModal.value = true
}

const showDocumentStatusModalFunc = () => {
    statusForm.id = props.dokumen.id
    statusForm.status = props.dokumen.status || 'pending'
    statusForm.catatan = props.dokumen.catatan || ''
    showDocumentStatusModal.value = true
}

// Form submissions
const submitDokumen = async () => {
    if (!dokumenFormRef.value) return
    
    await dokumenFormRef.value.validate((valid) => {
        if (valid) {
            uploadDokumen()
        }
    })
}

const uploadDokumen = async () => {
    dokumenLoading.value = true
    
    try {
        const formData = new FormData()
        
        Object.keys(dokumenForm).forEach(key => {
            if (dokumenForm[key] !== null) {
                formData.append(key, dokumenForm[key])
            }
        })
        
        await axios.post(route('admin.dokumen-lamaran.store'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        
        ElMessage.success('Dokumen berhasil diupload')
        showDokumenModal.value = false
        
        // Refresh page data
        router.visit(route('admin.lamaran.show', props.lamaranId), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error uploading documents:', error)
        ElMessage.error('Gagal mengupload dokumen')
    } finally {
        dokumenLoading.value = false
    }
}

const submitStatusUpdate = async () => {
    if (!statusFormRef.value) return
    
    await statusFormRef.value.validate((valid) => {
        if (valid) {
            updateDocumentStatus()
        }
    })
}

const updateDocumentStatus = async () => {
    statusLoading.value = true
    
    try {
        await axios.post(route('admin.dokumen-lamaran.update-status', statusForm.id), {
            status: statusForm.status,
            catatan: statusForm.catatan
        })
        
        ElMessage.success('Status dokumen berhasil diperbarui')
        showDocumentStatusModal.value = false
        
        // Refresh page data
        router.visit(route('admin.lamaran.show', props.lamaranId), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error updating document status:', error)
        ElMessage.error('Gagal memperbarui status dokumen')
    } finally {
        statusLoading.value = false
    }
}

// Computed properties
const documentTableData = computed(() => {
    if (!props.dokumen) return []
    
    return [
        { field: 'ktp', label: 'KTP', file: props.dokumen.ktp },
        { field: 'kk', label: 'Kartu Keluarga', file: props.dokumen.kk },
        { field: 'akte_lahir', label: 'Akte Kelahiran', file: props.dokumen.akte_lahir },
        { field: 'buku_nikah', label: 'Buku Nikah', file: props.dokumen.buku_nikah },
        { field: 'surat_keterangan_sehat', label: 'Surat Keterangan Sehat', file: props.dokumen.surat_keterangan_sehat },
        { field: 'surat_izin_keluarga', label: 'Surat Izin Keluarga', file: props.dokumen.surat_izin_keluarga },
        { field: 'ijazah', label: 'Ijazah Terakhir', file: props.dokumen.ijazah },
        { field: 'sertifikat_keahlian', label: 'Sertifikat Keahlian', file: props.dokumen.sertifikat_keahlian },
        { field: 'paspor', label: 'Paspor', file: props.dokumen.paspor },
        { field: 'surat_pengalaman', label: 'Surat Pengalaman Kerja', file: props.dokumen.surat_pengalaman },
        { field: 'skck', label: 'SKCK', file: props.dokumen.skck },
    ]
})
</script>