<template>
    <user-layout>
        <div class="border-2 border-bottom border-primary py-2 mb-4">
            <span class="fw-bold fs-lg">Jadwal Interview</span>
        </div>
        <el-card>
            <template v-if="!data">
                <h3 class="fs-3 fw-semibold text-center">
                    Terima kasih telah melengkapi profil Anda.
                </h3>
                <p class="mb-2 text-center">
                    Saat ini Anda belum mendapatkan jadwal interview. <br/>Jadwal akan dikirimkan kepada Anda setelah ditentukan oleh pihak Admin.
                </p>
                <p class="mb-2 text-center">
                    Mohon cek halaman ini secara berkala atau pantau notifikasi email Anda untuk pembaruan selanjutnya.
                </p>
            </template>
            <template v-else>
                <el-descriptions :column="2" border direction="vertical">
                    <el-descriptions-item label="Talent">
                        {{ data.talent.nama }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Tanggal">
                        {{ format_date(data.tanggal) }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Waktu">
                        {{ format_time(data.waktu) }} WIB
                    </el-descriptions-item>
                    <el-descriptions-item label="Lokasi" :span="2">
                        {{ data.lokasi }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Catatan" span="2">
                        {{ data.catatan ?? '-'}}
                    </el-descriptions-item>
                </el-descriptions>
            </template>
        </el-card>
    </user-layout>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import moment from 'moment';
const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    checking: {
        type: Boolean,
        default: false
    },
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

</script>