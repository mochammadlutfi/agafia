<template>
    <base-layout title="Detail Program Training">
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Detail Program Training</span>
                <div class="space-x-1">
                    <a :href="route('admin.training.program.edit', {id : data.id})" class="ep-button">
                        <i class="si si-note me-1"></i>
                        Ubah
                    </a>
                    <el-button type="danger" @click.prevent="hapus(data.id)">
                        <i class="si si-trash me-1"></i>
                        Hapus
                    </el-button>
                </div>
            </div>
            <div class="block block-rounded block-bordered">
                <div class="block-content p-3">
                    <el-descriptions :column="1" border>
                        <el-descriptions-item label="Nama Program">{{ data.nama }}</el-descriptions-item>
                        <el-descriptions-item label="Deskripsi">{{ data.deskripsi }}</el-descriptions-item>
                        <el-descriptions-item label="Lokasi">{{ data.lokasi }}</el-descriptions-item>
                        <el-descriptions-item label="Durasi">{{ data.durasi }}</el-descriptions-item>
                        <el-descriptions-item label="Kapasitas">{{ data.kapasitas }} peserta</el-descriptions-item>
                        <el-descriptions-item label="Instruktur">{{ data.instruktur }}</el-descriptions-item>
                        <el-descriptions-item label="Jumlah Peserta Aktif">{{ data.jumlah_peserta_aktif || 0 }} peserta</el-descriptions-item>
                        <el-descriptions-item label="Sisa Kapasitas">{{ data.sisa_kapasitas || data.kapasitas }} peserta</el-descriptions-item>
                        <el-descriptions-item label="Status">
                            <el-tag :type="data.aktif ? 'success' : 'danger'">
                                {{ data.aktif ? 'Aktif' : 'Tidak Aktif' }}
                            </el-tag>
                        </el-descriptions-item>
                        <el-descriptions-item label="Status Kapasitas">
                            <el-tag :type="data.penuh ? 'danger' : 'success'">
                                {{ data.penuh ? 'Penuh' : 'Tersedia' }}
                            </el-tag>
                        </el-descriptions-item>
                    </el-descriptions>
                </div>
            </div>
        </div>
    </base-layout>
</template>
<script>
import moment from 'moment';

export default {
    components : {
    },
    props : {
        data : Object,
    },
    methods : {
        hapus(id){
            ElMessageBox.alert('Data yang dihapus tidak bisa dikembalikan!', 'Peringatan', {
                showCancelButton: true,
                confirmButtonText: 'Ya!',
                cancelButtonText: 'Tidak!',
                type: 'warning',
            })
            .then(() => {
                this.$inertia.delete(this.route('admin.training.program.destroy', {id : id}), {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.$inertia.visit(this.route('admin.training.program.index'));
                        ElMessage({
                            type: 'success',
                            message: 'Program Training Berhasil Dihapus!',
                        });
                    },
                });
            });
        },
    }
}
</script>