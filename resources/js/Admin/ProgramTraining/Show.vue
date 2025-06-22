<template>
    <base-layout title="Detail Relawan">
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Detail Program Training</span>
                <div class="space-x-1">
                    <a :href="route('admin.staff.edit', {id : data.id})" class="ep-button">
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
                    <el-descriptions :column="1">
                        <el-descriptions-item label="Nama Program">{{ data.nama }}</el-descriptions-item>
                        <el-descriptions-item label="Jenis Kelamin">{{ data.jk }}</el-descriptions-item>
                        <el-descriptions-item label="Tempat / Tanggal Lahir">{{ data.tmp_lahir }} / {{  format_date(data.tgl_lahir) }}</el-descriptions-item>
                        <el-descriptions-item label="No Handphone">{{ data.phone }}</el-descriptions-item>
                        <el-descriptions-item label="Email">{{ data.email }}</el-descriptions-item>
                        <el-descriptions-item label="Alamat Lengkap">
                            {{ data.alamat }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Agama">{{ data.agama }}</el-descriptions-item>
                        <el-descriptions-item label="Pendidikan Terakhir">{{ data.pendidikan_terakhir }}</el-descriptions-item>
                        <el-descriptions-item label="Mulai Bekerja">{{ data.tgl_mulai }}</el-descriptions-item>
                        <el-descriptions-item label="Tanggal Masuk">{{ format_date(data.tgl_masuk) }}</el-descriptions-item>
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
    data(){
        return {
            active : 0,
            isLoading : false,
            listRekrutan: [],
        }
    },
    props : {
        data : Object,
    },
    computed : {
        getImage(){
            if(this.data.image){
                return this.data.image;
            }
            return "/media/placeholder/avatar.jpg";
        },
        getKTP(){
            if(this.data.ktp){
                return this.data.ktp;
            }
            return "/media/placeholder/ktp.jpg";
        }
    },
    methods : {
        setMenu(index){
            this.active = index;
        },
        zeroPad(num) {
            return num.toString().padStart(3, "0");
        },
        percentage(number, total){
            var percent = (parseInt(number)/total)* 100
            return Math.round(percent);
        },
        format_date(value) {
            if (value) {
                moment().locale('id');
                return moment(String(value)).format('DD MMMM YYYY')
            }
        },
        hapus(id){
            ElMessageBox.alert('Data yang dihapus tidak bisa dikembalikan!', 'Peringatan', {
                showCancelButton: true,
                confirmButtonText: 'Ya!',
                cancelButtonText: 'Tidak!',
                type: 'warning',
            })
            .then(() => {
                this.$inertia.delete(this.route('admin.staff.delete', {id : id}), {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.fetchData();
                        ElMessage({
                            type: 'success',
                            message: 'Data Berhasil Dihapus!',
                        });
                    },
                });
            });
        },
    }
}
</script>