<template>
    <base-layout title="Detail Relawan">
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Detail Hasil Interview</span>
                <div class="space-x-1">
                    <el-button type="danger" @click.prevent="hapus(data.id)">
                        <i class="si si-trash me-1"></i>
                        Hapus
                    </el-button>
                </div>
            </div>
            <div class="block block-rounded block-bordered">
                <div class="block-content p-3">
                    <el-descriptions :column="2" border :label-width="100" direction="vertical">
                        <el-descriptions-item label="Talent" width="200px" >
                            {{ data.talent.nama }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Tanggal Penilaian" width="200px">
                            {{ format_date(data.tanggal_penilaian) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Nilai Interview">
                            {{ data.skor_interview }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Nilai Psikotes">
                            {{ data.skor_psikotes }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Kemampuan Komunikasi">
                            {{ data.kemampuan_komunikasi }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Kemampuan Teknis">
                            {{ data.kemampuan_teknis }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Penilaian Kepribadian" :span="2" label-position="top">
                            {{ data.penilaian_kepribadian }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Catatan" :span="2" label-position="top">
                            {{ data.catatan }}
                        </el-descriptions-item>
                    </el-descriptions>
                </div>
            </div>
        </div>
    </base-layout>
</template>
<script setup>
import { ref } from 'vue';
import moment from 'moment';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Link } from '@inertiajs/vue3';


const props = defineProps({
    data: Object,
});
const active = ref(0);
const isLoading = ref(false);


const format_date = (value) => {
  if (value) {
    return moment(String(value)).format('DD MMM YYYY HH:mm');
  }
};
</script>
