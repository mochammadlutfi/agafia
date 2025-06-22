<template>
    <base-layout title="Detail Relawan">
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Detail Peserta Training</span>
                <div class="space-x-1">
                    <el-button type="danger" @click.prevent="hapus(data.id)">
                        <i class="si si-trash me-1"></i>
                        Hapus
                    </el-button>
                </div>
            </div>
            <div class="block block-rounded block-bordered">
                <div class="block-content p-4 fs-sm">
                    
                </div>
            </div>
        </div>
    </base-layout>
</template>
<script>
import DetailOrtu from '@/Components/DetailOrtu.vue';
import moment from 'moment';
export default {
    components : {
        DetailOrtu
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
                this.$inertia.delete(this.route('admin.pengguna.delete', {id : id}), {
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