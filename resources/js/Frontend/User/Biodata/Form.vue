<template>
    <base-layout>
        <div class="content">
            <el-row justify="center" class="mb-4">
                <el-col :md="18">
                    <el-card>
                        <el-alert
                        class="mb-4"
                        v-if="data.status == 'ditolak'"
                        title="Catatan"
                        type="error"
                        :description="data.detail.catatan"
                        show-icon
                        :closable="false"
                        />
                        <el-form :model="form" label-width="auto" @submit.prevent="submit">
                            <div class="border-bottom border-2 mb-4">
                                <h3 class="fs-5 mb-2">1. Informasi Pribadi</h3>
                            </div>
                            <el-row :gutter="20">
                                <el-col :span="18">
                                    <el-form-item label="NIK" :error="errors.nik">
                                        <el-input v-model="form.nik" placeholder="Masukkan NIK" class="w-100"/>
                                    </el-form-item>
                                    <el-form-item label="Nama Lengkap" :error="errors.nama">
                                        <el-input v-model="form.nama" placeholder="Masukkan nama lengkap" class="w-100"/>
                                    </el-form-item>
                                    <el-form-item label="Jenis Kelamin" :error="errors.jenis_kelamin">
                                        <el-radio-group v-model="form.jenis_kelamin">
                                            <el-radio label="Laki-Laki" value="Laki-Laki" />
                                            <el-radio label="Perempuan" value="Perempuan"/>
                                        </el-radio-group>
                                    </el-form-item>
                                    <el-form-item label="Tempat / Tanggal Lahir" :error="errors.tempat_lahir || errors.tanggal_lahir">
                                        <el-col :span="12" class="ps-0">
                                            <el-input v-model="form.tempat_lahir"  placeholder="Masukkan Tempat Lahir" class="w-100"/>
                                        </el-col>
                                        <el-col :span="12" class="pe-0">
                                            <el-date-picker v-model="form.tanggal_lahir" value-format="YYYY-MM-DD" format="DD-MM-YYYY" type="date" placeholder="Pilih tanggal lahir"></el-date-picker>
                                        </el-col>
                                    </el-form-item>
                                    <el-form-item label="Nomor Telepon / HP" :error="errors.phone">
                                        <el-input v-model="form.phone" placeholder="Masukkan nomor telepon" class="w-100"/>
                                    </el-form-item>
                                    <el-form-item label="Alamat" :error="errors.alamat">
                                        <el-input type="textarea" v-model="form.alamat" placeholder="Masukan alamat"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="6">
                                    <el-form-item label="Foto" :error="errors.foto" label-position="top">
                                        <single-file-upload v-model="form.foto"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>

                            <div class="border-bottom border-2 mb-4">
                                <h3 class="fs-5 mb-2">2. Pemberi Izin</h3>
                            </div>
                            <el-row>
                                <el-col :span="18">
                                    <el-form-item label="Nama Pemberi Izin" :error="errors.nama_izin">
                                        <el-input v-model="form.nama_izin" placeholder="Masukkan nama lengkap" class="w-100"/>
                                    </el-form-item>
                                    <el-form-item label="Status Pemberi Izin" :error="errors.status_izin">
                                        <el-input v-model="form.status_izin" placeholder="Masukkan status" class="w-100"/>
                                    </el-form-item>
                                    <el-form-item label="Nama Ayah" :error="errors.nama_ayah">
                                        <el-input v-model="form.nama_ayah" placeholder="Masukkan nama ayah" class="w-100"/>
                                    </el-form-item>
                                    <el-form-item label="Nama Ibu" :error="errors.nama_ibu">
                                        <el-input v-model="form.nama_ibu" placeholder="Masukkan nama ibu" class="w-100"/>
                                    </el-form-item>
                                    <el-form-item label="Alamat" :error="errors.alamat_ortu">
                                        <el-input type="textarea" v-model="form.alamat_ortu" placeholder="Masukan alamat"/>
                                    </el-form-item>
                                    <el-form-item label="Nomor Telepon / HP" :error="errors.phone_ortu">
                                        <el-input v-model="form.phone_ortu" placeholder="Masukkan nomor telepon" class="w-100"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <div class="border-bottom border-2 mb-4">
                                <h3 class="fs-5 mb-2">3. Riwayat Pendidikan</h3>
                            </div>
                            <el-table :data="form.pendidikan" border class="w-100" header-cell-class-name="bg-body text-dark">
                                <el-table-column label="Tingkat" width="150">
                                    <template #default="scope">
                                        <el-select v-model="scope.row.tingkat" placeholder="Pilih tingkat">
                                            <el-option label="SD" value="SD" />
                                            <el-option label="SMP" value="SMP" />
                                            <el-option label="SMA" value="SMA" />
                                            <el-option label="D3" value="D3" />
                                            <el-option label="S1" value="S1" />
                                            <el-option label="S2" value="S2" />
                                            <el-option label="S3" value="S3" />
                                        </el-select>
                                    </template>
                                </el-table-column>
                                <el-table-column label="Nama Sekolah">
                                    <template #default="scope">
                                        <el-input v-model="scope.row.nama_sekolah" placeholder="Masukkan nama sekolah" />
                                    </template>
                                </el-table-column>
                                <el-table-column label="Tahun Lulus" width="150">
                                    <template #default="scope">
                                        <el-date-picker type="year" class="w-100" value-format="YYYY" v-model="scope.row.tahun_lulus" placeholder="Tahun lulus" />
                                    </template>
                                </el-table-column>
                                <el-table-column label="Jurusan" width="200">
                                    <template #default="scope">
                                        <el-input v-model="scope.row.jurusan" placeholder="Masukkan jurusan" />
                                    </template>
                                </el-table-column>
                                <el-table-column label="Aksi" width="100">
                                    <template #default="scope">
                                        <el-button :disabled="scope.$index == 0"  type="danger" size="small" @click="form.pendidikan.splice(scope.$index, 1)">
                                            <i class="fa fa-trash"></i>
                                        </el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <el-button class="mt-2 w-100" @click="onAddPendidikan">
                                Tambah Pendidikan
                            </el-button>

                            
                            <div class="mt-4 border-bottom border-2 mb-4">
                                <h3 class="fs-5 mb-2">4. Pekerjaan</h3>
                            </div>
                            <el-table :data="form.pengalaman" border class="w-100" header-cell-class-name="bg-body text-dark">
                                <el-table-column label="Nama Perusaahan" width="200">
                                    <template #default="scope">
                                        <el-input v-model="scope.row.nama" placeholder="Masukkan nama perusahaan" />
                                    </template>
                                </el-table-column>
                                <el-table-column label="Posisi">
                                    <template #default="scope">
                                        <el-input v-model="scope.row.posisi" placeholder="Masukkan posisi" />
                                    </template>
                                </el-table-column>
                                <el-table-column label="Tahun Masuk" width="150">
                                    <template #default="scope">
                                        <el-date-picker type="year" class="w-100" value-format="YYYY" v-model="scope.row.tahun_masuk" placeholder="Tahun masuk" />
                                    </template>
                                </el-table-column>
                                <el-table-column label="Tahun Keluar" width="150">
                                    <template #default="scope">
                                        <el-date-picker type="year" class="w-100" value-format="YYYY" v-model="scope.row.tahun_keluar" placeholder="Tahun keluar" />
                                    </template>
                                </el-table-column>
                                <el-table-column label="Aksi" width="60" align="center">
                                    <template #default="scope">
                                        <el-button :disabled="scope.$index == 0"  type="danger" size="small" @click="form.pengalaman.splice(scope.$index, 1)">
                                            <i class="fa fa-trash"></i>
                                        </el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <el-button class="my-2 w-100" @click="onAddPengalaman">
                                Tambah Pengalaman Kerja
                            </el-button>
                            <el-form-item label="Pekerjaan Yang Diinginkan">
                                <el-input v-model="form.pekerjaan" placeholder="Masukkan pekerjaan yang diinginkan" class="w-100"/>
                            </el-form-item>
                            <el-form-item label="Negara Tujuan Kerja">
                                <el-input v-model="form.negara_tujuan" placeholder="Masukkan tujuan negara" class="w-100"/>
                            </el-form-item>
                            
                            <div class="mt-4 border-bottom border-2 mb-4">
                                <h3 class="fs-5 mb-2">5. Dokumen</h3>
                            </div>
                            <el-row :gutter="20">
                                <el-col :span="12">
                                    <el-form-item label="KTP" :error="errors.ktp" label-position="top">
                                        <single-file-upload v-model="form.ktp"/>
                                    </el-form-item>
                                    <el-form-item label="Kartu Keluarga" :error="errors.kk" label-position="top">
                                        <single-file-upload v-model="form.kk"/>
                                    </el-form-item>
                                    <el-form-item label="Akte Lahir" :error="errors.akte_lahir" label-position="top">
                                        <single-file-upload v-model="form.akte_lahir"/>
                                    </el-form-item>
                                    <el-form-item label="Buku Nikah" :error="errors.buku_nikah" label-position="top">
                                        <single-file-upload v-model="form.buku_nikah"/>
                                    </el-form-item>
                                    <el-form-item label="Surat Keterangan Sehat" :error="errors.keterangan_sehat" label-position="top">
                                        <single-file-upload v-model="form.keterangan_sehat"/>
                                    </el-form-item>
                                    <el-form-item label="Surat Izin Keluarga" :error="errors.izin_keluarga" label-position="top">
                                        <single-file-upload v-model="form.izin_keluarga"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="12">
                                    <el-form-item label="Ijazah Terakhir" :error="errors.ijazah" label-position="top">
                                        <single-file-upload v-model="form.ijazah"/>
                                    </el-form-item>
                                    <el-form-item label="Sertifikat Keahlian/Kompetensi" :error="errors.komptensi" label-position="top">
                                        <single-file-upload v-model="form.komptensi"/>
                                    </el-form-item>
                                    <el-form-item label="Paspor" :error="errors.paspor" label-position="top">
                                        <single-file-upload v-model="form.paspor"/>
                                    </el-form-item>
                                    <el-form-item label="Surat Keterangan Pengalaman Kerja" :error="errors.pengalaman_kerja" label-position="top">
                                        <single-file-upload v-model="form.pengalaman_kerja"/>
                                    </el-form-item>
                                    <el-form-item label="SKCK" :error="errors.skck" label-position="top">
                                        <single-file-upload v-model="form.skck"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>

                            <el-button type="primary" class="w-100" @click="submit">
                                <i class="fa fa-check me-2"></i>
                                Simpan
                            </el-button>
                        </el-form>
                    </el-card>
                </el-col>
            </el-row>
        </div>
    </base-layout>
</template>

<script setup>

import { ref, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import SingleFileUpload from '@/Components/SingleFileUpload.vue';

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

const form = useForm({
    nik : '',
    nama: '',
    jenis_kelamin: 'Laki-Laki',
    tempat_lahir: '',
    tanggal_lahir: '',
    phone: '',
    alamat: '',
    nama_izin: '',
    status_izin: '',
    nama_ayah: '',
    nama_ibu: '',
    phone_ortu: '',
    alamat_ortu: '',
    pendidikan: [
        {
            nama_sekolah: '',
            tingkat: '',
            tahun_lulus: '',
            jurusan: '',
        }
    ],
    pengalaman: [
        {
            nama_perusahaan: '',
            posisi: '',
            tahun_masuk: '',
            tahun_keluar: '',
        }
    ],
    negara_tujuan: '',
    pekerjaan: '',
    foto : null,
    ktp: null,
    kk: null,
    akte_lahir: null,
    buku_nikah: null,
    keterangan_sehat: null,
    izin_keluarga: null,
    ijazah: null,
    keahlian: null,
    paspor: null,
    pengalaman_kerja: null,
    skck: null,
    komptensi: null,
});


const onAddPendidikan = () => {
    form.pendidikan.push({
        nama: '',
        tingkat: '',
        tahun_lulus: '',
        jurusan: '',
    });
};

const onAddPengalaman = () => {
    form.pengalaman.push({
        nama : '',
        posisi: '',
        tahun_masuk: '',
        tahun_keluar: '',
    });
};

const submit = () => {
    form.post(route('user.biodata.store'), {
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};
const setValue = () => {
    const val = props.data.detail;

    form.nik = val.nik || '';
    form.nama = val.nama || '';
    form.jenis_kelamin = val.jenis_kelamin || 'Laki-Laki';
    form.tempat_lahir = val.tempat_lahir || '';
    form.tanggal_lahir = val.tanggal_lahir || '';
    form.phone = val.phone || '';
    form.alamat = val.alamat || '';
    form.nama_izin = val.nama_izin || '';
    form.status_izin = val.status_izin || '';
    form.nama_ayah = val.nama_ayah || '';
    form.nama_ibu = val.nama_ibu || '';
    form.phone_ortu = val.phone_ortu || '';
    form.alamat_ortu = val.alamat_ortu || '';
    
    form.pendidikan = Array.isArray(val.pendidikan) ? val.pendidikan.map(item => ({
        nama_sekolah: item.nama_sekolah || '',
        tingkat: item.tingkat || '',
        tahun_lulus: item.tahun_lulus || '',
        jurusan: item.jurusan || '',
    })) : [{
        nama_sekolah: '',
        tingkat: '',
        tahun_lulus: '',
        jurusan: '',
    }];

    form.pengalaman = Array.isArray(val.pengalaman) ? val.pengalaman.map(item => ({
        nama: item.nama || '',
        posisi: item.posisi || '',
        tahun_masuk: item.tahun_masuk || '',
        tahun_keluar: item.tahun_keluar || '',
    })) : [{
        nama: '',
        posisi: '',
        tahun_masuk: '',
        tahun_keluar: '',
    }];

    form.negara_tujuan = val.negara_tujuan || '';
    form.pekerjaan = val.pekerjaan || '';

    // File uploads (assume null if not present)
    form.ktp = val.ktp || null;
    form.kk = val.kk || null;
    form.akte_lahir = val.akte_lahir || null;
    form.buku_nikah = val.buku_nikah || null;
    form.keterangan_sehat = val.surat_keterangan_sehat || null;
    form.izin_keluarga = val.surat_izin_keluarga || null;
    form.ijazah = val.ijazah || null;
    form.keahlian = val.keahlian || null;
    form.paspor = val.paspor || null;
    form.pengalaman_kerja = val.surat_pengalaman_kerja || null;
    form.skck = val.skck || null;
    form.komptensi = val.komptensi || null;
    form.foto = val.foto || null;
};


onMounted(() => {
    if(props.data.detail){
        setValue();
    }
});
</script>