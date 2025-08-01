<template>
    <base-layout>
        <div class="content">
            <el-form label-position="top" label-width="100%" @submit.prevent="submit">
                <div class="content-heading d-flex justify-content-between align-items-center">
                    <span>Ubah Password</span>
                </div>
                
                <div class="block rounded">
                    <div class="block-content p-4" v-loading="loading">
                        
                        <el-row :gutter="40">
                            <el-col :span="12">
                                <el-form-item label="Password" :error="errors.password">
                                    <el-input v-model="form.password" type="password" placeholder="Masukan password"
                                        show-password />
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="Konfirmasi Password" :error="errors.password">
                                    <el-input v-model="form.password_confirmation" type="password" placeholder="Masukan konfirmasi password"
                                        show-password />
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
            form : {
                password : null,
                password_confirmation : null,
            },
            loading : false,
        }
    },
    methods : {
        submit() {
            this.loading = true;
            let form = this.$inertia.form(this.form);
            form.post(this.route('admin.password'), {
                preserveScroll: true,
                onFinish:() => {
                    this.loading = false;
                },
                onSuccess: () => {
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `Password Berhasil Diperbaharui!`,
                        showCancelButton: false,
                        showConfirmButton: false,
                    });
                },
            });
        }
    }
}
</script>