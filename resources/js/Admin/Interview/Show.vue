<template>
    <base-layout title="Detail Relawan">
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Detail Interview</span>
                <div class="space-x-1">
                    <template v-if="['operational_manager', 'owner'].includes($page.props.user.level)">
                        <el-button type="success" @click.prevent="openModalSelesai" v-if="data.status === 'dijadwalkan'">
                            <i class="si si-check me-2"></i>
                            Selesai
                        </el-button>
                        <el-button type="danger" @click.prevent="cancel(data.id)" v-if="data.status === 'dijadwalkan'">
                            <i class="si si-close me-2"></i>
                            Batalkan
                        </el-button>
                    </template>
                    <el-button v-if="data.status === 'selesai'">
                        <i class="si si-eye me-2"></i>
                        Lihat Hasil
                    </el-button>
                </div>
            </div>
            <div class="block block-rounded block-bordered">
                <div class="block-content p-3">
                    <el-descriptions :column="2" border direction="horizontal">
                        <el-descriptions-item label="Lowongan">
                            {{ data.lamaran.lowongan.posisi }},
                            {{ data.lamaran.lowongan.perusahaan }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Pelamar">
                            {{ data.lamaran.user.nama }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Tanggal">
                            {{ format_date(data.tanggal) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Waktu">
                            {{ format_time(data.waktu) }} WIB
                        </el-descriptions-item>
                        <el-descriptions-item label="Pewawancara">
                            {{ data.pewawancara.nama }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Lokasi" :span="2">
                            {{ data.lokasi }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Status">
                            <el-tag :type="getTypeStatus(data.status)" size="small">
                                {{ data.status_label }}
                            </el-tag>
                        </el-descriptions-item>
                        <el-descriptions-item label="Catatan" span="2">
                            {{ data.catatan ?? '-'}}
                        </el-descriptions-item>
                    </el-descriptions>
                </div>
            </div>
        </div>
        <el-dialog
            v-model="modalHasil"
            title="Konfirmasi Selesai"
            width="800">
            <el-form label-position="top" label-width="100%" @submit.prevent="submit" v-loading="loadingForm">
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="Skor Interview" :error="errors.skor_interview">
                            <el-input v-model="form.skor_interview" type="number" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Skor Psikotes" :error="errors.skor_psikotes">
                            <el-input v-model="form.skor_psikotes" type="number" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Kemampuan Komunikasi" :error="errors.kemampuan_komunikasi">
                            <el-select v-model="form.kemampuan_komunikasi" placeholder="Pilih">
                                <el-option value="kurang" label="Kurang" />
                                <el-option value="cukup" label="Cukup" />
                                <el-option value="baik" label="Baik" />
                                <el-option value="sangat_baik" label="Sangat Baik" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Kemampuan Teknis" :error="errors.kemampuan_teknis">
                            <el-select v-model="form.kemampuan_teknis" placeholder="Pilih">
                                <el-option value="kurang" label="Kurang" />
                                <el-option value="cukup" label="Cukup" />
                                <el-option value="baik" label="Baik" />
                                <el-option value="sangat_baik" label="Sangat Baik" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Rekomendasi" :error="errors.rekomendasi">
                            <el-select v-model="form.rekomendasi" placeholder="Pilih">
                                <el-option value="tidak_direkomendasikan" label="Tidak Direkomendasikan" />
                                <el-option value="bersyarat" label="Bersyarat" />
                                <el-option value="direkomendasikan" label="Direkomendasikan" />
                                <el-option value="sangat_direkomendasikan" label="Sangat Direkomendasikan" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Penilaian Kepribadian" :error="errors.kepribadian">
                            <el-input v-model="form.kepribadian" type="textarea" :rows="4"/>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Catatan" :error="errors.catatan">
                            <el-input v-model="form.catatan" type="textarea" :rows="4"/>
                        </el-form-item>
                    </el-col>
                </el-row>
                
                <div class="text-end">
                    <el-button @click="dialogVisible = false">Cancel</el-button>
                    <el-button type="primary" native-type="submit">
                    Confirm
                    </el-button>
                </div>
            </el-form>
        </el-dialog>
    </base-layout>
</template>
<script setup>
import { ref } from 'vue';
import moment from 'moment';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Link, useForm } from '@inertiajs/vue3';


const props = defineProps({
    data: Object,
    errors: {
        type: Object,
        default: () => ({})
    }
});
const active = ref(0);
const isLoading = ref(false);
const modalHasil = ref(false);
const loadingForm = ref(false);
const form = useForm({
    lamaran_id: props.data.lamaran.id,
    user_id: props.data.lamaran.user_id,
    skor_interview: null,
    skor_psikotes: null,
    kemampuan_komunikasi: null,
    kemampuan_teknis: null,
    kepribadian: null,
    rekomendasi: null,
    catatan: null,
});


const getTypeStatus = (status) => {
  switch (status) {
    case 'dijadwalkan':
      return 'info';
    case 'selesai':
      return 'success';
    case 'dijadwal_ulang':
      return 'warning';
    default:
      return 'danger';
  }
};

const openModalSelesai = (id) => {
    modalHasil.value = true;
}


const submit = () => {
    loadingForm.value = true;
    let url = route('admin.interview.hasil', { id : props.data.id});
    
    form.post(url, {
        preserveScroll: true,
        onFinish: () => {
            loadingForm.value = false;
            modalHasil.value = false;
        },
        onSuccess: () => {
            return  ElMessage.success('Jadwal berhasil Diselesaikan');
        },
    });
};


const cancel = (id) => {
  ElMessageBox.confirm('Apakah Anda yakin ingin membatalkan jadwal ini?', 'Konfirmasi', {
    confirmButtonText: 'Ya',
    cancelButtonText: 'Tidak',
    type: 'warning'
  }).then(() => {
    ElMessage.success('Jadwal berhasil dibatalkan.');
  }).catch(() => {
    ElMessage.info('Aksi dibatalkan.');
  });
};


const format_date = (value) => {
  if (value) {
    return moment(String(value)).format('DD MMM YYYY');
  }
}

const format_time = (value) => {
  if (value) {
    return moment(String(value)).format('HH:mm');
  }
}
</script>
