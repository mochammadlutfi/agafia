<template>
    <base-layout>
        <div class="content">
            <el-row justify="center" class="mb-4">
                <el-col :md="20">
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
                                    <el-row :gutter="20">
                                        <el-col :span="12">
                                            <el-form-item label="Jenis Kelamin" :error="errors.jenis_kelamin">
                                                <el-radio-group v-model="form.jenis_kelamin">
                                                    <el-radio label="Laki-Laki" value="Laki-Laki" />
                                                    <el-radio label="Perempuan" value="Perempuan"/>
                                                </el-radio-group>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="Agama" :error="errors.agama">
                                                <el-select v-model="form.agama" placeholder="Pilih agama" class="w-100">
                                                    <el-option label="Islam" value="Islam" />
                                                    <el-option label="Kristen" value="Kristen" />
                                                    <el-option label="Katolik" value="Katolik" />
                                                    <el-option label="Hindu" value="Hindu" />
                                                    <el-option label="Buddha" value="Buddha" />
                                                    <el-option label="Konghucu" value="Konghucu" />
                                                </el-select>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-form-item label="Tempat / Tanggal Lahir" :error="errors.tempat_lahir || errors.tanggal_lahir">
                                        <el-col :span="12" class="ps-0">
                                            <el-input v-model="form.tempat_lahir"  placeholder="Masukkan Tempat Lahir" class="w-100"/>
                                        </el-col>
                                        <el-col :span="12" class="pe-0">
                                            <el-date-picker v-model="form.tanggal_lahir" value-format="YYYY-MM-DD" format="DD-MM-YYYY" type="date" placeholder="Pilih tanggal lahir"></el-date-picker>
                                        </el-col>
                                    </el-form-item>
                                    <el-row :gutter="20">
                                        <el-col :span="12">
                                            <el-form-item label="Status Perkawinan" :error="errors.status_perkawinan">
                                                <el-select v-model="form.status_perkawinan" placeholder="Pilih status" class="w-100">
                                                    <el-option label="Lajang" value="lajang" />
                                                    <el-option label="Menikah" value="menikah" />
                                                    <el-option label="Cerai" value="cerai" />
                                                    <el-option label="Janda" value="janda" />
                                                    <el-option label="Duda" value="duda" />
                                                </el-select>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="Golongan Darah" :error="errors.golongan_darah">
                                                <el-select v-model="form.golongan_darah" placeholder="Pilih golongan darah" class="w-100">
                                                    <el-option label="A" value="A" />
                                                    <el-option label="B" value="B" />
                                                    <el-option label="AB" value="AB" />
                                                    <el-option label="O" value="O" />
                                                </el-select>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-row :gutter="20">
                                        <el-col :span="12">
                                            <el-form-item label="Tinggi Badan (cm)" :error="errors.tinggi_badan">
                                                <el-input-number v-model="form.tinggi_badan" placeholder="170" class="w-100" :min="100" :max="250"/>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="Berat Badan (kg)" :error="errors.berat_badan">
                                                <el-input-number v-model="form.berat_badan" placeholder="60" class="w-100" :min="30" :max="200"/>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                    <el-form-item label="Nomor Telepon / HP" :error="errors.phone">
                                        <el-input v-model="form.phone" placeholder="Masukkan nomor telepon" class="w-100"/>
                                    </el-form-item>
                                    <el-form-item label="Email Alternatif" :error="errors.email_alternatif">
                                        <el-input v-model="form.email_alternatif" placeholder="email@example.com" class="w-100"/>
                                    </el-form-item>
                                    <el-form-item label="Alamat KTP" :error="errors.alamat">
                                        <el-input type="textarea" v-model="form.alamat" placeholder="Masukan alamat sesuai KTP"/>
                                    </el-form-item>
                                    <el-form-item label="Alamat Domisili" :error="errors.alamat_domisili">
                                        <el-input type="textarea" v-model="form.alamat_domisili" placeholder="Alamat tempat tinggal saat ini (jika berbeda dari KTP)"/>
                                    </el-form-item>
                                    <el-row :gutter="20">
                                        <el-col :span="12">
                                            <el-form-item label="Kecamatan" :error="errors.kecamatan">
                                                <el-input v-model="form.kecamatan" placeholder="Kecamatan" class="w-100"/>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="Kab/Kota" :error="errors.kabupaten_kota">
                                                <el-input v-model="form.kabupaten_kota" placeholder="Kabupaten/Kota" class="w-100"/>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="Provinsi" :error="errors.provinsi">
                                                <el-input v-model="form.provinsi" placeholder="Provinsi" class="w-100"/>
                                            </el-form-item>
                                        </el-col>
                                        <el-col :span="12">
                                            <el-form-item label="Kode Pos" :error="errors.kode_pos">
                                                <el-input v-model="form.kode_pos" placeholder="40123" class="w-100" style="max-width: 150px;"/>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                </el-col>
                                <el-col :span="6">
                                    <el-form-item label="Foto" :error="errors.foto" label-position="top">
                                        <single-file-upload v-model="form.foto"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>

                            <div class="border-bottom border-2 mb-4">
                                <h3 class="fs-5 mb-2">2. Kontak Darurat & Keluarga</h3>
                            </div>
                            <el-row>
                                <el-col :span="18">
                                    <div class="mb-4">
                                        <h4 class="fs-6 mb-3 text-primary">Kontak Darurat</h4>
                                        <el-form-item label="Nama Kontak Darurat" :error="errors.kontak_darurat_nama">
                                            <el-input v-model="form.kontak_darurat_nama" placeholder="Nama kontak darurat" class="w-100"/>
                                        </el-form-item>
                                        <el-row :gutter="20">
                                            <el-col :span="12">
                                                <el-form-item label="Nomor Telepon" :error="errors.kontak_darurat_phone">
                                                    <el-input v-model="form.kontak_darurat_phone" placeholder="Nomor telepon" class="w-100"/>
                                                </el-form-item>
                                            </el-col>
                                            <el-col :span="12">
                                                <el-form-item label="Hubungan" :error="errors.kontak_darurat_hubungan">
                                                    <el-input v-model="form.kontak_darurat_hubungan" placeholder="Orang tua/Saudara/dll" class="w-100"/>
                                                </el-form-item>
                                            </el-col>
                                        </el-row>
                                        <el-form-item label="Alamat Kontak Darurat" :error="errors.kontak_darurat_alamat">
                                            <el-input type="textarea" v-model="form.kontak_darurat_alamat" placeholder="Alamat kontak darurat"/>
                                        </el-form-item>
                                    </div>
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
                                <h3 class="fs-5 mb-2">4. Pengalaman Kerja & Profesional</h3>
                            </div>
                            <div class="mb-4">
                                <h4 class="fs-6 mb-3 text-primary">Pengalaman Kerja Dalam Negeri</h4>
                                <el-table :data="form.pengalaman" border class="w-100" header-cell-class-name="bg-body text-dark">
                                    <el-table-column label="Nama Perusahaan" width="200">
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
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="fs-6 mb-3 text-primary">Tujuan Karir</h4>
                                <el-form-item label="Pekerjaan Yang Diinginkan">
                                    <el-input v-model="form.pekerjaan" placeholder="Masukkan pekerjaan yang diinginkan" class="w-100"/>
                                </el-form-item>
                                <el-form-item label="Negara Tujuan Kerja">
                                    <el-input v-model="form.negara_tujuan" placeholder="Masukkan tujuan negara" class="w-100"/>
                                </el-form-item>
                                <el-form-item label="Objektif Karir" :error="errors.objektif_karir">
                                    <el-input type="textarea" v-model="form.objektif_karir" placeholder="Jelaskan tujuan karir dan motivasi Anda" :rows="3"/>
                                </el-form-item>
                                <el-form-item label="Ringkasan Profil" :error="errors.ringkasan_profil">
                                    <el-input type="textarea" v-model="form.ringkasan_profil" placeholder="Ringkasan pengalaman dan keahlian Anda" :rows="3"/>
                                </el-form-item>
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="fs-6 mb-3 text-primary">Keahlian & Kemampuan</h4>
                                <el-form-item label="Keahlian Khusus" :error="errors.keahlian_khusus">
                                    <el-input type="textarea" v-model="form.keahlian_khusus" placeholder="Keahlian khusus untuk pekerjaan overseas" :rows="2"/>
                                </el-form-item>
                                <el-form-item label="Hobi & Minat" :error="errors.hobi">
                                    <el-input v-model="form.hobi" placeholder="Hobi dan minat Anda" class="w-100"/>
                                </el-form-item>
                                <el-form-item label="Motto Hidup" :error="errors.motto_hidup">
                                    <el-input v-model="form.motto_hidup" placeholder="Motto atau prinsip hidup Anda" class="w-100"/>
                                </el-form-item>
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="fs-6 mb-3 text-primary">Kondisi Kesehatan</h4>
                                <el-form-item label="Kondisi Kesehatan" :error="errors.kondisi_kesehatan">
                                    <el-input type="textarea" v-model="form.kondisi_kesehatan" placeholder="Kondisi kesehatan khusus, alergi, atau catatan medis" :rows="2"/>
                                </el-form-item>
                                <el-form-item label="Medical Check Up Terakhir" :error="errors.medical_checkup_terakhir">
                                    <el-date-picker v-model="form.medical_checkup_terakhir" value-format="YYYY-MM-DD" format="DD-MM-YYYY" type="date" placeholder="Tanggal medical check up terakhir" class="w-100"/>
                                </el-form-item>
                            </div>
                            
                            <!-- <div class="mt-4 border-bottom border-2 mb-4">
                                <h3 class="fs-5 mb-2">5. Dokumen</h3>
                            </div> -->
                            <!-- <el-row :gutter="20">
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
                            </el-row> -->

                            <el-button type="primary" class="w-100" @click="submit" :loading="form.processing">
                                <i class="fa fa-check me-2"></i>
                                {{ props.editMode ? 'Update Profil' : 'Simpan Profil' }}
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
    // Informasi Pribadi
    nik : '',
    nama: '',
    jenis_kelamin: 'Laki-Laki',
    agama: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    status_perkawinan: '',
    tinggi_badan: null,
    berat_badan: null,
    golongan_darah: '',
    phone: '',
    email_alternatif: '',
    alamat: '',
    alamat_domisili: '',
    kecamatan: '',
    kabupaten_kota: '',
    provinsi: '',
    kode_pos: '',
    
    // Kontak Darurat
    kontak_darurat_nama: '',
    kontak_darurat_phone: '',
    kontak_darurat_hubungan: '',
    kontak_darurat_alamat: '',

    // Pendidikan
    pendidikan: [
        {
            nama_sekolah: '',
            tingkat: '',
            tahun_lulus: '',
            jurusan: '',
        }
    ],
    
    // Pengalaman Kerja
    pengalaman: [
        {
            nama_perusahaan: '',
            posisi: '',
            tahun_masuk: '',
            tahun_keluar: '',
        }
    ],
    
    // Tujuan Karir
    negara_tujuan: '',
    pekerjaan: '',
    objektif_karir: '',
    ringkasan_profil: '',
    
    // Keahlian
    keahlian_khusus: '',
    hobi: '',
    motto_hidup: '',
    
    // Kesehatan
    kondisi_kesehatan: '',
    medical_checkup_terakhir: '',
    
    // Files
    foto : null,
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
    const routeName = props.editMode ? 'user.biodata.update' : 'user.biodata.store';
    const method = 'post';
    
    form[method](route(routeName), {
        onSuccess: () => {
            if (!props.editMode) {
                form.reset();
            }
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};
const setValue = () => {
    const val = props.data.detail;

    // Informasi Pribadi
    form.nik = val.nik || '';
    form.nama = val.nama || '';
    form.jenis_kelamin = val.jenis_kelamin || 'Laki-Laki';
    form.agama = val.agama || '';
    form.tempat_lahir = val.tempat_lahir || '';
    form.tanggal_lahir = val.tanggal_lahir || '';
    form.status_perkawinan = val.status_perkawinan || '';
    form.tinggi_badan = val.tinggi_badan || null;
    form.berat_badan = val.berat_badan || null;
    form.golongan_darah = val.golongan_darah || '';
    form.phone = val.phone || '';
    form.email_alternatif = val.email_alternatif || '';
    form.alamat = val.alamat || '';
    form.alamat_domisili = val.alamat_domisili || '';
    form.kecamatan = val.kecamatan || '';
    form.kabupaten_kota = val.kabupaten_kota || '';
    form.provinsi = val.provinsi || '';
    form.kode_pos = val.kode_pos || '';
    
    // Kontak Darurat
    form.kontak_darurat_nama = val.kontak_darurat_nama || '';
    form.kontak_darurat_phone = val.kontak_darurat_phone || '';
    form.kontak_darurat_hubungan = val.kontak_darurat_hubungan || '';
    form.kontak_darurat_alamat = val.kontak_darurat_alamat || '';
    
    // Keluarga
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

    // Tujuan Karir
    form.negara_tujuan = val.negara_tujuan || '';
    form.pekerjaan = val.pekerjaan || '';
    form.objektif_karir = val.objektif_karir || '';
    form.ringkasan_profil = val.ringkasan_profil || '';
    
    // Keahlian
    form.keahlian_khusus = val.keahlian_khusus || '';
    form.hobi = val.hobi || '';
    form.motto_hidup = val.motto_hidup || '';
    
    // Kesehatan
    form.kondisi_kesehatan = val.kondisi_kesehatan || '';
    form.medical_checkup_terakhir = val.medical_checkup_terakhir || '';

    // File uploads
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

<style scoped>
.border-bottom {
    border-bottom: 2px solid #e4e7ed !important;
    padding-bottom: 8px;
    margin-bottom: 24px;
}

.text-primary {
    color: #409eff !important;
    font-weight: 600;
}

:deep(.el-card) {
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

:deep(.el-form-item__label) {
    font-weight: 600;
    color: #606266;
}

:deep(.el-input__wrapper) {
    border-radius: 6px;
}

:deep(.el-select .el-input__wrapper) {
    border-radius: 6px;
}

:deep(.el-button) {
    border-radius: 6px;
}

:deep(.el-table) {
    border-radius: 8px;
    overflow: hidden;
}

:deep(.el-table th) {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
}

:deep(.el-alert) {
    border-radius: 8px;
    margin-bottom: 20px;
}

.section-divider {
    margin: 32px 0;
}

h3.fs-5 {
    color: #303133;
    font-weight: 700;
    margin-bottom: 16px;
}

h4.fs-6 {
    color: #409eff;
    font-weight: 600;
    margin-bottom: 12px;
    border-left: 3px solid #409eff;
    padding-left: 12px;
}

:deep(.el-input-number) {
    width: 100%;
}

:deep(.el-date-picker) {
    width: 100%;
}

:deep(.el-radio-group) {
    display: flex;
    gap: 16px;
}

:deep(.el-radio) {
    margin-right: 0;
}

@media (max-width: 768px) {
    :deep(.el-col) {
        margin-bottom: 16px;
    }
    
    :deep(.el-form-item) {
        margin-bottom: 16px;
    }
    
    h4.fs-6 {
        font-size: 14px;
    }
}
</style>