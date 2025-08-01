<template>
    <base-layout>
        <div class="content">
            <el-form label-position="top" label-width="100%" @submit.prevent="submit">
                <div class="content-heading d-flex justify-content-between align-items-center">
                    <span>Ubah Profile</span>
                </div>
                
                <div class="block rounded">
                    <div class="block-content p-4" v-loading="loading">
                        
                        <el-row :gutter="40">
                            <el-col :span="12">
                                <el-form-item label="Nama" :error="errors.nama">
                                    <el-input v-model="form.nama" placeholder="Masukan Nama" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="Email" :error="errors.email">
                                    <el-input v-model="form.email" placeholder="Masukan Email" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="Username" :error="errors.username">
                                    <el-input v-model="form.username" placeholder="Masukan Username" />
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
export default {
    components :{
    },
    props :{
        data : {
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
            title : "Tambah  Baru",
            disableKota : false,
            form : {
                nama: this.data.nama,
                email: this.data.email,
                username : this.data.username,
            },
            loading : false,
        }
    },
    created(){
        if(this.editMode){
            this.setValue();
        }
    },
    methods : {
        setValue(){
            this.form.nama = this.value.nama;
            this.form.username = this.value.username;
            this.form.email = this.value.email;
        },  
        submit() {
            this.loading = true;
            let form = this.$inertia.form(this.form);
            form.post(this.route('admin.profile'), {
                preserveScroll: true,
                onFinish:() => {
                    this.loading = false;
                },
                onSuccess: () => {
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `Profile Berhasil Diperbaharui!`,
                        showCancelButton: false,
                        showConfirmButton: false,
                    });
                },
            });
        }
    }
}
</script>