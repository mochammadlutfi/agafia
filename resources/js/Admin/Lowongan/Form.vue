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
                                <el-form-item label="Perusahaan" :error="errors.perusahaan">
                                    <el-input v-model="form.perusahaan" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Posisi" :error="errors.posisi">
                                    <el-input v-model="form.posisi" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="8">
                                <el-form-item label="Kuota" :error="errors.kuota">
                                    <el-input-number v-model="form.kuota" :min="1" />
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :span="12">
                                <el-form-item label="Lokasi" :error="errors.lokasi">
                                    <el-input v-model="form.lokasi" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="Skill" :error="errors.skill">
                                    <el-input v-model="form.skill" />
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :md="16">
                                <el-form-item label="Deskripsi" :error="errors.deskripsi">
                                    <el-input v-model="form.deskripsi" type="textarea" :rows="6"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="8">
                                <el-form-item label="Status" :error="errors.status">
                                    <el-select v-model="form.status" placeholder="Pilih Status">
                                        <el-option label="Buka" value="buka"/>
                                        <el-option label="Tutup" value="tutup"/>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="URL Foto (Opsional)" :error="errors.foto">
                                    <SingleFileUpload v-model="form.foto" />
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
                perusahaan: null,
                posisi: null, 
                skill: null,
                deskripsi: null,
                kuota: null,
                lokasi: null,
                status: 'buka',
                foto: null,
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
            this.form.perusahaan = this.value.perusahaan;
            this.form.posisi = this.value.posisi;
            this.form.skill = this.value.skill;
            this.form.deskripsi = this.value.deskripsi;
            this.form.kuota = this.value.kuota;
            this.form.lokasi = this.value.lokasi;
            this.form.status = this.value.status;
            this.form.foto = this.value.foto;
        },  
        submit() {
            this.loading = true;
            let form = this.$inertia.form(this.form);
            let url = this.editMode === true ? this.route('admin.lowongan.update', {id : this.value.id}) : this.route('admin.lowongan.store');
            form.post(url, {
                preserveScroll: true,
                onFinish:() => {
                    this.loading = false;
                },
                onSuccess: () => {
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `Lowongan Berhasil Disimpan!`,
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