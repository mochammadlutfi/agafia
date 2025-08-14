<template>
    <div class="px-2">
        <div class="align-middle border-bottom d-flex justify-content-between mb-4 py-2">
            <div class="fs-6 fw-semibold">Data Interview</div>
            <div>
                <el-button v-if="interview && ['owner', 'admin'].includes($page.props.user.level)"
                    @click="openInterviewModal"
                    type="primary">
                    Ubah Jadwal Interview
                </el-button>
                <el-button v-if="interview && ['owner', 'operational_manager'].includes($page.props.user.level)"
                    @click="openHasilModal"
                    type="primary">
                    Hasil Interview
                </el-button>
            </div>
        </div>
        <div v-if="interview">
            <el-descriptions class="mb-4" :column="2" border direction="horizontal">
                <el-descriptions-item label="Tanggal">
                    {{ formatDate(interview.tanggal) }}
                </el-descriptions-item>
                <el-descriptions-item label="Waktu">
                    {{ formatTime(interview.waktu) }} WIB
                </el-descriptions-item>
                <el-descriptions-item label="Pewawancara">
                    {{ interview?.pewawancara?.nama || '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Lokasi" :span="2">
                    {{ interview.lokasi }}
                </el-descriptions-item>
                <el-descriptions-item label="Status">
                    <el-tag :type="getStatusType(interview.status)" size="small">
                        {{ interview.status_label }}
                    </el-tag>
                </el-descriptions-item>
                <el-descriptions-item label="Catatan" :span="2">
                    {{ interview.catatan ?? '-'}}
                </el-descriptions-item>
            </el-descriptions>
            <el-descriptions title="Hasil Interview" :column="2" border direction="horizontal" v-if="interview.status == 'selesai'">
                <el-descriptions-item label="Skor Interview">
                    {{ interview.skor_interview ?? '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Skor Psikotes">
                    {{ interview.skor_psikotes ?? '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Kemampuan Komunikasi">
                    {{ interview.kemampuan_komunikasi ?? '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Kemampuan Teknis">
                    {{ interview.kemampuan_teknis ?? '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Rekomendasi">
                    {{ interview.rekomendasi ?? '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Penilaian Kepribadian">
                    {{ interview.kepribadian ?? '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Catatan">
                    {{ interview.catatan_hasil ?? '-' }}
                </el-descriptions-item>
            </el-descriptions>
        </div>
        <el-empty v-else>
            <el-button 
                @click="showInterviewModal = true"
                type="primary"
                v-if="canSchedule && ['owner', 'admin'].includes($page.props.user.level)"
            >
                Jadwalkan Interview
            </el-button>
        </el-empty>

        <!-- Interview Schedule Modal -->
        <el-dialog
            v-model="showInterviewModal"
            title="Jadwalkan Interview"
            width="700px"
            :before-close="handleInterviewModalClose"
        >
            <el-form :model="interviewForm" :rules="interviewRules" ref="interviewFormRef" label-width="150px">
                <el-row :gutter="16">
                    <el-col :span="12">
                        <el-form-item label="Tanggal" prop="tanggal">
                            <el-date-picker
                                v-model="interviewForm.tanggal"
                                type="date"
                                placeholder="Pilih tanggal"
                                format="DD/MM/YYYY "
                                value-format="YYYY-MM-DD"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Waktu" prop="waktu">
                            <el-time-picker
                                v-model="interviewForm.waktu"
                                placeholder="Pilih waktu"
                                format="HH:mm"
                                value-format="HH:mm:ss"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="Lokasi" prop="lokasi">
                    <el-input v-model="interviewForm.lokasi" placeholder="Masukkan lokasi interview (misal: Kantor Pusat, Online via Zoom, dll)" />
                </el-form-item>
                <el-form-item label="Status" prop="status">
                    <el-select v-model="interviewForm.status" placeholder="Pilih status" style="width: 100%">
                        <el-option label="Dijadwalkan" value="dijadwalkan" />
                        <el-option label="Dibatalkan" value="dibatalkan" />
                        <el-option label="Dijadwal Ulang" value="dijadwal_ulang" />
                    </el-select>
                </el-form-item>
                <el-form-item label="Catatan">
                    <el-input
                        v-model="interviewForm.catatan"
                        type="textarea"
                        :rows="3"
                        placeholder="Catatan tambahan (opsional)"
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showInterviewModal = false">Batal</el-button>
                    <el-button type="primary" @click="submitInterviewForm" :loading="interviewLoading">
                        Jadwalkan Interview
                    </el-button>
                </span>
            </template>
        </el-dialog>

        
        <el-dialog
            v-model="showHasilModal"
            title="Hasil Interview"
            width="800">
            <el-form label-position="top" ref="hasilviewFormRef" :model="formHasil" :rules="hasilRules" label-width="100%" @submit.prevent="submitHasil" v-loading="hasilLoading">
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="Skor Interview" prop="skor_interview">
                            <el-input v-model="formHasil.skor_interview" type="number" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Skor Psikotes" prop="skor_psikotes">
                            <el-input v-model="formHasil.skor_psikotes" type="number" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Kemampuan Komunikasi" prop="kemampuan_komunikasi">
                            <el-select v-model="formHasil.kemampuan_komunikasi" placeholder="Pilih">
                                <el-option value="kurang" label="Kurang" />
                                <el-option value="cukup" label="Cukup" />
                                <el-option value="baik" label="Baik" />
                                <el-option value="sangat_baik" label="Sangat Baik" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Kemampuan Teknis" prop="kemampuan_teknis">
                            <el-select v-model="formHasil.kemampuan_teknis" placeholder="Pilih">
                                <el-option value="kurang" label="Kurang" />
                                <el-option value="cukup" label="Cukup" />
                                <el-option value="baik" label="Baik" />
                                <el-option value="sangat_baik" label="Sangat Baik" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Rekomendasi" prop="rekomendasi">
                            <el-select v-model="formHasil.rekomendasi" placeholder="Pilih">
                                <el-option value="tidak_direkomendasikan" label="Tidak Direkomendasikan" />
                                <el-option value="bersyarat" label="Bersyarat" />
                                <el-option value="direkomendasikan" label="Direkomendasikan" />
                                <el-option value="sangat_direkomendasikan" label="Sangat Direkomendasikan" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Penilaian Kepribadian" prop="kepribadian">
                            <el-input v-model="formHasil.kepribadian" type="textarea" :rows="4"/>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Catatan" prop="catatan">
                            <el-input v-model="formHasil.catatan" type="textarea" :rows="4"/>
                        </el-form-item>
                    </el-col>
                </el-row>
                
                <div class="text-end">
                    <el-button @click="dialogVisible = false">Cancel</el-button>
                    <el-button type="primary" native-type="submit">
                    Simpan Hasil Interview
                    </el-button>
                </div>
            </el-form>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { ElMessage } from 'element-plus'
import { router } from '@inertiajs/vue3'
import moment from 'moment'
import axios from 'axios'
import SelectStaff from '@/Components/SelectStaff.vue'

const props = defineProps({
    interview: Object,
    canSchedule: Boolean,
    lamaranId: [String, Number]
})

defineEmits(['interview-updated'])

// Modal and form state
const showInterviewModal = ref(false)
const showHasilModal = ref(false)
const interviewLoading = ref(false)
const hasilLoading = ref(false)
const interviewFormRef = ref()
const hasilviewFormRef = ref()

// Interview Form
const interviewForm = reactive({
    lamaran_id: props.lamaranId,
    tanggal: '',
    waktu: '',
    lokasi: '',
    pewawancara_id: '',
    status: 'dijadwalkan',
    catatan: ''
})

const formHasil = reactive({
    lamaran_id: props.lamaranId,
    nilai_interview: null,
    nilai_psikotes: null,
    kemampuan_komunikasi: null,
    kemampuan_teknis: null,
    kepribadian: null,
    rekomendasi: null,
    catatan: null
})

const interviewRules = {
    tanggal: [{ required: true, message: 'Tanggal interview wajib diisi', trigger: 'blur' }],
    waktu: [{ required: true, message: 'Waktu interview wajib diisi', trigger: 'blur' }],
    lokasi: [{ required: true, message: 'Lokasi interview wajib diisi', trigger: 'blur' }],
    status: [{ required: true, message: 'Status wajib dipilih', trigger: 'change' }]
}

const hasilRules = {
    skor_interview: [{ required: true, message: 'Skor interview wajib diisi', trigger: 'blur' }],
    skor_psikotes: [{ required: true, message: 'Skor psikotes wajib diisi', trigger: 'blur' }],
    kemampuan_komunikasi: [{ required: true, message: 'Kemampuan komunikasi wajib dipilih', trigger: 'change' }],
    kemampuan_teknis: [{ required: true, message: 'Kemampuan teknis wajib dipilih', trigger: 'change' }],
    kepribadian: [{ required: true, message: 'Penilaian kepribadian wajib diisi', trigger: 'blur' }],
    rekomendasi: [{ required: true, message: 'Rekomendasi wajib dipilih', trigger: 'change' }],
    catatan: [{ required: false }]
}

const openInterviewModal = () => {
    // console.log('Moment object:', moment(props.interview.waktu).format('HH:mm:ss'));
    showInterviewModal.value = true
    interviewForm.tanggal = props.interview?.tanggal ? moment(props.interview.tanggal).format('YYYY-MM-DD') : ''
    interviewForm.waktu = props.interview?.waktu ? moment(props.interview.waktu).format('HH:mm:ss') : moment().format('HH:mm:ss')
    interviewForm.lokasi = props.interview?.lokasi || ''
    interviewForm.status = props.interview?.status || 'dijadwalkan'
    interviewForm.catatan = props.interview?.catatan || ''
    interviewLoading.value = false
}


const openHasilModal = () => {
    // console.log('Moment object:', moment(props.interview.waktu).format('HH:mm:ss'));
    showHasilModal.value = true
    formHasil.skor_interview = props.interview?.skor_interview || null
    formHasil.skor_psikotes = props.interview?.skor_psikotes || null
    formHasil.kemampuan_komunikasi = props.interview?.kemampuan_komunikasi || null
    formHasil.kemampuan_teknis = props.interview?.kemampuan_teknis || null
    formHasil.kepribadian = props.interview?.kepribadian || null
    formHasil.rekomendasi = props.interview?.rekomendasi || null
    formHasil.catatan = props.interview?.catatan || null
    hasilLoading.value = false
}
// Modal handlers
const handleInterviewModalClose = (done) => {
    interviewFormRef.value?.resetFields()
    done()
}

// Form submission
const submitInterviewForm = async () => {
    if (!interviewFormRef.value) return
    
    await interviewFormRef.value.validate((valid) => {
        if (valid) {
            scheduleInterview()
        }
    })
}

const scheduleInterview = async () => {
    interviewLoading.value = true
    
    try {
        const url  = props.interview ? route('admin.interview.update', props.interview.id) : route('admin.interview.store')

        await axios.post(url, interviewForm)
        
        ElMessage.success('Interview berhasil dijadwalkan')
        showInterviewModal.value = false
        
        // Refresh page data
        router.visit(route('admin.lamaran.show', props.lamaranId), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error scheduling interview:', error)
        ElMessage.error('Gagal menjadwalkan interview')
    } finally {
        interviewLoading.value = false
    }
}

const submitHasil = async () => {
    hasilLoading.value = true
    if (!hasilviewFormRef.value) return
    
    await hasilviewFormRef.value.validate((valid) => {
        if (valid) {
            saveInterviewResults()
        }
    })
}

const saveInterviewResults = async () => {
    hasilLoading.value = true
    
    try {
        const url = route('admin.interview.hasil', { id: props.interview.id })
        
        await axios.post(url, formHasil)
        
        ElMessage.success('Hasil interview berhasil disimpan')
        showHasilModal.value = false
        
        // Refresh page data
        router.visit(route('admin.lamaran.show', props.lamaranId), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error saving interview results:', error)
        ElMessage.error('Gagal menyimpan hasil interview')
    } finally {
        hasilLoading.value = false
    }
}
// Helper functions
const formatDate = (value) => {
    if (value) {
        return moment(String(value)).format('DD MMM YYYY')
    }
}

const formatTime = (value) => {
    if (value) {
        return moment(value, "HH:mm:ss").format('HH:mm')
    }
}

const getStatusType = (status) => {
    switch (status) {
        case 'dijadwalkan':
            return 'info'
        case 'selesai':
            return 'success'
        case 'dijadwal_ulang':
            return 'warning'
        default:
            return 'danger'
    }
}
</script>