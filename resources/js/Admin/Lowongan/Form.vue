<template>
    <base-layout>
        <div class="content">
            <el-form label-position="top" label-width="100%" @submit.prevent="submit">
                <div class="content-heading d-flex justify-content-between align-items-center">
                    <span>{{ title }}</span>
                </div>
                
                <div class="block rounded">
                    <div class="block-content p-4" v-loading="loading">
                        <el-row :gutter="20">
                            <el-col :span="8">
                                <el-form-item label="Perusahaan" :error="errors.nama">
                                    <el-input v-model="form.nama" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Posisi" :error="errors.nama">
                                    <el-input v-model="form.nama" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Kuota" :error="errors.nama">
                                    <el-input v-model="form.nama" />
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :md="16">
                                <el-form-item label="Deskripsi" :error="errors.nama">
                                    <el-input v-model="form.nama" type="textarea" :rows="6"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="8">
                                <el-form-item label="Foto" :error="errors.foto">
                                    <single-file-upload v-model="form.ktp"/>
                                </el-form-item>
                                <el-form-item label="Status" :error="errors.status">
                                    <el-switch
                                        v-model="form.status"
                                        active-text="Aktif"
                                        inactive-text="Tidak Aktif"
                                    />
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-button native-type="submit" type="primary" :loading="loading">
                            <i class="fa fa-check me-2"></i>
                            Simpan
                        </el-button>
                    </div>
                </div>
            </el-form>
        </div>
    </base-layout>
</template>
<script>
import SingleFileUpload from '@/Components/SingleFileUpload.vue';
export default {
    components :{
        SingleFileUpload
    },
    props :{
        value : {
            type : Object,
        },
        editMode : {
            type : Boolean,
            default : false,
        },
        errors : {
            type : Object,
        }
    },
    data(){
        return {
            title : "Tambah Lowongan Baru",
            disableKota : false,
            form : {
                nama: null,
                deskripsi: null,
                lokasi: null,
                durasi: null,
                kapasitas: null,
                instruktur: null,
                aktif: true,
            },
            loading : false,
        }
    },
    created(){
        if(this.editMode){
            this.title = "Ubah Lowongan";
            this.setValue();
        }
    },
    methods : {
        setValue(){
            this.form.nama = this.value.nama;
            this.form.deskripsi = this.value.deskripsi;
            this.form.lokasi = this.value.lokasi;
            this.form.durasi = this.value.durasi;
            this.form.kapasitas = this.value.kapasitas;
            this.form.instruktur = this.value.instruktur;
            this.form.aktif = this.value.aktif;
        },  
        submit() {
            this.loading = true;
            let form = this.$inertia.form(this.form);
            let url = this.editMode === true ? this.route('admin.training.program.update', {id : this.value.id}) : this.route('admin.training.program.store');
            form.post(url, {
                preserveScroll: true,
                onFinish:() => {
                    this.loading = false;
                },
                onSuccess: () => {
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `Pendukung Baru Berhasil Ditambahkan!`,
                        showCancelButton: true,
                        confirmButtonText: 'Tambah Lainnya',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$inertia.visit(this.route("user.pendukung.create"));
                        }
                    });
                },
            });
        }
    }
}
</script>