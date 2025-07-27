<template>
    <base-layout>
        <div class="content">
            <el-form label-position="top" label-width="100%" @submit.prevent="submit">
                <div class="content-heading d-flex justify-content-between align-items-center">
                    <span>{{ title }}</span>
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
                            <el-col :span="12">
                                <el-form-item label="Level" :error="errors.level">
                                    <el-select v-model="form.level" placeholder="Pilih" class="w-100">
                                        <el-option label="Owner" value="owner"/>
                                        <el-option label="Operational Manager" value="operational_manager"/>
                                        <el-option label="Talent Manager" value="talent_manager"/>
                                        <el-option label="Admin" value="admin"/>
                                    </el-select>
                                </el-form-item>
                            </el-col>
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
            title : "Tambah  Baru",
            disableKota : false,
            form : {
                nama: null,
                email: null,
                username : null,
                level : 'Admin',
                password : null,
                password_confirmation : null,
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
            this.form.level = this.value.level;
        },  
        submit() {
            this.loading = true;
            let form = this.$inertia.form(this.form);
            let url = this.editMode === true ? this.route('admin.staff.update', {id : this.value.id}) : this.route('admin.staff.store');
            form.post(url, {
                preserveScroll: true,
                onFinish:() => {
                    this.loading = false;
                },
                onSuccess: () => {
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `Staff Baru Berhasil Ditambahkan!`,
                        showCancelButton: true,
                        confirmButtonText: 'Tambah Lainnya',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$inertia.visit(this.route("admin.staff.create"));
                        }
                    });
                },
            });
        }
    }
}
</script>