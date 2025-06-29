<template>
    <base-layout title="Detail Relawan">
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Detail Medical Chekup</span>
                <div class="space-x-1">
                    <template v-if="data.status == 'pending'">
                    <el-button type="success" @click.prevent="onConfirm">
                        <i class="si si-check me-1"></i>
                        Terima
                    </el-button>
                    <el-button type="danger" @click.prevent="onReject">
                        <i class="si si-close me-1"></i>
                        Tolak
                    </el-button>
                    </template>
                </div>
            </div>
            <div class="block block-rounded block-bordered">
                <div class="block-content p-4 fs-sm">
                    <el-alert
                    class="mb-4"
                    v-if="data.status == 'ditolak'"
                    title="Catatan"
                    type="error"
                    :description="data.detail.catatan"
                    show-icon
                    :closable="false"
                    />
                    
                    <el-descriptions :column="2" label-width="240px" :border="true">
                        <el-descriptions-item label="Talent">
                            {{ data.user.nama }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Nama Fasilitas Kesehatan">
                            {{ data.nama }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Tanggal Pengecekan">
                            {{ format_date(data.tanggal) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Hasil">
                            {{ data.hasil }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Dokumen File">
                            <span v-if="!data.file">-</span>
                            <el-button v-else tag="a" :href="'/uploads/'+data.file" target="_blank" type="primary" size="small">
                                <i class="fa fa-file-pdf-o"></i> Lihat
                            </el-button>                        
                        </el-descriptions-item>
                        <el-descriptions-item label="Status">
                            <el-tag :type="getTypeStatus(data.status)" size="small">
                                {{ data.status_label }}
                            </el-tag>
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
import { ref, onMounted } from 'vue';
import moment from 'moment';
import { ElLoading } from 'element-plus';
import { router } from '@inertiajs/vue3';

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
    case 'pending':
      return 'warning';
    case 'valid':
      return 'success';
    default:
      return 'danger';
  }
};

const format_date = (value) => {
    if (value) {
        moment().locale('id');
        return moment(String(value)).format('DD MMMM YYYY')
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
        axios.post(`/admin/medical/${props.data.id}/state`, {
            state: 'valid'
        })
        .then(response => {
            ElMessage.success('Dokumen medical checkup berhasil di konfirmasi.');
            loading.close();
            router.visit(route('admin.medical.show', props.data.id), {
                only: ['data'],
                preserveState: true,
                preserveScroll: true,
            });
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
        axios.post(`/admin/medical/${props.data.id}/state`, {
            state: 'tidak_valid',
            reason: value
        })
        .then(response => {
            ElMessage.success('Dokumen medical checkup tidak valid.');
            loading.close();
            router.visit(route('admin.medical.show', props.data.id), {
                only: ['data'],
                preserveState: true,
                preserveScroll: true,
            });
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