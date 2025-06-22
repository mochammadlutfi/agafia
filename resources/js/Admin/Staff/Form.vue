<template>
    <base-layout>
        <div class="content">
            <el-form label-position="top" label-width="100%" @submit.prevent="submit">
                <div class="content-heading d-flex justify-content-between align-items-center">
                    <span>{{ title }}</span>
                </div>
                
                <div class="block rounded">
                    <div class="block-content p-4" v-loading="loading">
                        <div class="border-bottom border-2 mb-4">
                            <h3 class="h5 mb-2">1. Informasi Pribadi</h3>
                        </div>
                        <el-row :gutter="40">
                            <el-col :span="12">
                                <el-form-item label="Nama Lengkap" :error="errors.nama">
                                    <el-input v-model="form.nama" />
                                </el-form-item>
                                <el-form-item label="Jenis Kelamin" :error="errors.jk">
                                    <el-radio-group v-model="form.jk">
                                        <el-radio label="Laki-Laki" value="L" />
                                        <el-radio label="Perempuan" value="P"/>
                                    </el-radio-group>
                                </el-form-item>
                                <el-row :gutter="20">
                                    <el-col :span="12">
                                        <el-form-item label="Tempat Lahir" :error="errors.tmpLahir">
                                            <el-input v-model="form.tmpLahir" placeholder="Tempat Lahir" />
                                        </el-form-item>
                                    </el-col>
                                    <el-col :span="12">
                                        <el-form-item label="Tanggal Lahir" :error="errors.tglLahir">
                                            <el-date-picker v-model="form.tglLahir" type="date" placeholder="Tanggal Lahir"
                                                format="DD-MM-YYYY" value-format="YYYY-MM-DD" style="width: 100%" />
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                                <el-form-item label="Agama" :error="errors.agama">
                                    <el-select v-model="form.agama" placeholder="Pilih" class="w-100">
                                        <el-option label="Islam" value="Islam"/>
                                        <el-option label="Katolik" value="Katolik"/>
                                        <el-option label="Protestan" value="Protestan"/>
                                        <el-option label="Hindu" value="Hindu"/>
                                        <el-option label="Budha" value="Budha"/>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="Pendidikan Terakhir" :error="errors.pendidikan_terakhir">
                                    <el-select v-model="form.pendidikan_terakhir" placeholder="Pilih" class="w-100">
                                        <el-option label="SMA/SMK" value="SMA/SMK"/>
                                        <el-option label="D3" value="D3"/>
                                        <el-option label="S1" value="S1"/>
                                        <el-option label="S2" value="S2"/>
                                        <el-option label="S3" value="S3"/>
                                    </el-select>
                                </el-form-item>
                                <el-form-item label="No SK" :error="errors.sk">
                                    <el-input v-model="form.sk" placeholder="No SK"/>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="No Handphone" :error="errors.phone">
                                    <el-input v-model="form.phone" />
                                </el-form-item>
                                <el-form-item label="Alamat Email" :error="errors.email">
                                    <el-input v-model="form.email" />
                                </el-form-item>
                                <el-form-item label="Mulai Bekerja" :error="errors.tglMulai">
                                    <el-date-picker v-model="form.tglMulai" type="year" placeholder="Mulai Bekerja"
                                        format="YYYY" value-format="YYYY" class="w-100"/>
                                </el-form-item>
                                <el-form-item label="Tanggal Masuk Bekerja" :error="errors.tglMasuk">
                                    <el-date-picker v-model="form.tglMasuk" type="date" placeholder="Tanggal Masuk Bekerja"
                                        format="DD-MM-YYYY" value-format="YYYY-MM-DD" style="width: 100%" />
                                </el-form-item>
                                <el-form-item label="Alamat Lengkap" :error="errors.alamat">
                                    <el-input v-model="form.alamat" type="textarea" :rows="5"/>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <template v-if="!editMode">
                            <div class="border-bottom border-2 mb-4">
                                <h3 class="h5 mb-2">2. Informasi Akun</h3>
                            </div>
                            <el-row :gutter="40">
                                <el-col :span="12">
                                    <el-form-item label="Username" :error="errors.username">
                                        <el-input v-model="form.username" placeholder="Masukan Username" />
                                    </el-form-item>
                                    <el-form-item label="Level" :error="errors.level">
                                        <el-select v-model="form.level" placeholder="Pilih" class="w-100">
                                            <el-option label="Admin" value="Admin"/>
                                            <el-option label="Guru" value="Guru"/>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="12">
                                    <el-form-item label="Password" :error="errors.password">
                                        <el-input v-model="form.password" type="password" placeholder="Masukan password"
                                            show-password />
                                    </el-form-item>
                                    <el-form-item label="Konfirmasi Password" :error="errors.password">
                                        <el-input v-model="form.password_confirmation" type="password" placeholder="Masukan konfirmasi password"
                                            show-password />
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </template>
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
                jk: 'Laki-Laki',
                tmpLahir: null,
                tglLahir: null,
                alamat: null,
                agama: null,
                pendidikan_terakhir: null,
                tglMasuk : null,
                tglMulai: null,
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
            this.form.nik = this.value.nik;
            this.form.nama = this.value.nama;
            this.form.jk = (this.value.jk == "P") ? "Perempuan" : "Laki-Laki";
            this.form.tmpLahir = this.value.tmp_lahir;
            this.form.tglLahir = this.value.tgl_lahir;
            this.form.alamat = this.value.alamat;
            this.form.rt = this.value.rt;
            this.form.rw = this.value.rw;
            this.form.tps = this.value.tps;
            this.form.kota_id = this.value.kota_id;
            this.form.kecamatan_id = this.value.kecamatan_id;
            this.form.kelurahan_id = this.value.kelurahan_id;
            this.form.phone = this.value.phone;
            this.form.email = this.value.email;

            this.form.ktp = this.value.ktp;
            this.form.image = this.value.image;

            this.form.tps = this.value.tps;
            this.form.user_id = this.value.user_id
            // if(level)
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