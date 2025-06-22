<template>
    <user-layout>
        <div class="border-2 border-bottom border-primary py-2 mb-4">
            <span class="fw-bold fs-lg">Medical Checkup</span>
        </div>
        <el-card>
            <template v-if="data.medical">
                <h3 class="fs-3 fw-semibold text-center">
                  Dokumen Medical Checkup Sedang Diproses
                </h3>
                <div class="text-center mb-4">
                    <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </div>
                <p class="mb-2 text-center">
                  Dokumen medical checkup Anda telah berhasil diunggah dan saat ini sedang dalam proses validasi oleh tim kami.
                </p>
                <p class="mb-2 text-center">
                  Mohon tunggu beberapa saat. Anda akan menerima notifikasi setelah dokumen ini diverifikasi.
                </p>
            </template>
            <template v-else>
              <el-form label-position="top" label-width="100%" @submit.prevent="submit">
                <el-row :gutter="20">
                    <el-col :md="12">
                      <el-form-item label="Nama Faskes" :error="errors.nama">
                            <el-input v-model="form.nama" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Tanggal" :error="errors.tanggal">
                          <el-date-picker v-model="form.tanggal" class="w-100" value-format="YYYY-MM-DD" format="DD-MM-YYYY" type="date" placeholder="Pilih tanggal"></el-date-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Hasil" :error="errors.hasil">
                            <el-input v-model="form.hasil" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Dokumen" :error="errors.file">
                          <single-file-upload v-model="form.file"/>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-button native-type="submit" type="primary">
                    <i class="fa fa-check me-2"></i>
                    Simpan
                </el-button>
              </el-form>
            </template>
        </el-card>
    </user-layout>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import moment from 'moment';
import { useForm, router } from '@inertiajs/vue3';
import SingleFileUpload from '@/Components/SingleFileUpload.vue';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    editMode: {
        type: Boolean,
        default: false
    },
    errors: {
        type: Object,
        default: () => ({})
    }
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

const form = useForm({
    nama: null,
    tanggal: null,
    hasil: null,
    file: null,
});


const submit = () => {
    form.post(route('user.medical.store'), {
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};
</script>