<template>
    <user-layout>
        <div class="border-2 border-bottom border-primary py-2 mb-4">
            <span class="fw-bold fs-lg">Profil</span>
        </div>
        
        <el-card>
            <template v-if="checking">
                <h3 class="fs-3 fw-semibold text-center">
                    Profil Anda Sedang Diproses</h3>
                <div class="text-center mb-4">
                    <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </div>
                <p class="text-center">
                    Profil Anda telah berhasil dikirim dan sedang dalam proses pengecekan. <br/>Mohon tunggu konfirmasi selanjutnya.
                </p>
            </template>
            <template v-else>
                
            <el-alert
            class="mb-4"
            v-if="data.status == 'ditolak'"
            title="Catatan"
            type="error"
            :description="data.detail.catatan"
            show-icon
            :closable="false"
            />
            <div class="border-bottom border-2 mb-4">
                <h3 class="fs-5 mb-2">1. Informasi Pribadi</h3>
            </div>
            <el-row justify="space-between">
                <el-col :span="18">
                    <el-descriptions column="1" label-width="240px" :border="true" class="mb-4">
                        <el-descriptions-item label="NIK">
                            {{ data.detail.nik }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Nama Lengkap">
                            {{ data.detail.nama }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Jenis Kelamin">
                            {{ data.detail.jenis_kelamin }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Umur">
                            {{ data.detail.age }} Tahun
                        </el-descriptions-item>
                        <el-descriptions-item label="Tempat / Tanggal Lahir">
                            {{ data.detail.tempat_lahir }} / {{ format_date(data.detail.tanggal_lahir) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="No Handphone">
                            {{ data.detail.phone }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Alamat">
                            {{ data.detail.alamat }}
                        </el-descriptions-item>
                    </el-descriptions>
                </el-col>
                <el-col :span="4">
                    <el-image
                        class="w-100"
                        :src="data.detail.foto ? '/uploads/' + data.detail.foto : '/images/placeholder.png'"
                        fit="cover"
                        :preview-src-list="[data.detail.foto ? '/uploads/' + data.detail.foto : '/images/placeholder.png']"
                    />
                </el-col>
            </el-row>
            <div class="border-bottom border-2 mb-4">
                <h3 class="fs-5 mb-2">2. Pemberi Izin & Orang Tua</h3>
            </div>
            <el-row>
                <el-col :span="18">
                    <el-descriptions column="2" label-width="240px" :border="true" class="mb-4">
                        <el-descriptions-item label="Nama Pemberi Izin" span="2">
                            {{ data.detail.nama_izin }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Status Pemberi Izin" span="2">
                            {{ data.detail.status_izin }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Nama Ayah">
                            {{ data.detail.nama_ayah }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Nama Ibu">
                            {{ data.detail.nama_ibu }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Alamat" span="2">
                            {{ data.detail.alamat_ortu }}
                        </el-descriptions-item>
                        <el-descriptions-item label="No Handphone" span="2">
                            {{ data.detail.phone_ortu }}
                        </el-descriptions-item>
                    </el-descriptions>
                </el-col>
            </el-row>
            <div class="border-bottom border-2 mb-4">
                <h3 class="fs-5 mb-2">3. Riwayat Pendidikan</h3>
            </div>
            <el-table :data="data.detail.pendidikan" border class="w-100" header-cell-class-name="bg-body text-dark">
                <el-table-column prop="tingkat" label="Tingkat" />
                <el-table-column prop="nama_sekolah" label="Nama Sekolah" />
                <el-table-column prop="jurusan" label="Jurusan" />
                <el-table-column prop="tahun_lulus" label="Tahun Lulus" />
            </el-table>
            <div class="border-bottom border-2 my-4">
                <h3 class="fs-5 mb-2">4. Riwayat Pekerjaan</h3>
            </div>
            <el-table :data="data.detail.pengalaman" border class="w-100" header-cell-class-name="bg-body text-dark">
                <el-table-column prop="nama" label="Nama Perusahaan" />
                <el-table-column prop="posisi" label="Posisi" />
                <el-table-column prop="tahun_masuk" label="Tahun Masuk" />
                <el-table-column prop="tahun_keluar" label="Tahun Keluar" />
            </el-table>
            <div class="border-bottom border-2 my-4">
                <h3 class="fs-5 mb-2">5. Dokumen Lampiran</h3>
            </div>
            <el-descriptions column="2" label-width="240px" :border="true" class="mb-4">
                <el-descriptions-item label="KTP">
                    <span v-if="!data.detail.ktp">-</span>
                    <el-button v-else tag="a" :href="'/uploads/'+data.ktp" target="_blank" type="primary" size="small">
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>
                </el-descriptions-item>
                <el-descriptions-item label="Kartu Keluarga">
                    <span v-if="!data.kk">-</span>
                    <el-button v-else tag="a" :href="'/uploads/'+data.kk" target="_blank" type="primary" size="small">
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>
                </el-descriptions-item>
                <el-descriptions-item label="Akte Lahir">
                    <span v-if="!data.akte_lahir">-</span>
                    <el-button v-else tag="a" :href="'/uploads/'+data.akte_lahir" target="_blank" type="primary" size="small">
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>
                </el-descriptions-item>
                <el-descriptions-item label="Buku Nikah">
                    <span v-if="!data.buku_nikah">-</span>
                    <el-button v-else tag="a" :href="'/uploads/'+data.buku_nikah" target="_blank" type="primary" size="small">
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>
                </el-descriptions-item>
                <el-descriptions-item label="Surat Keterangan Sehat">
                    <span v-if="!data.surat_keterangan_sehat">-</span>
                    <el-button v-else tag="a" :href="'/uploads/'+data.surat_keterangan_sehat" target="_blank" type="primary" size="small">
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>
                </el-descriptions-item>
                <el-descriptions-item label="Surat Izin Keluarga">
                    <span v-if="!data.surat_izin_keluarga">-</span>
                    <el-button v-else tag="a" :href="'/uploads/'+data.surat_izin_keluarga" target="_blank" type="primary" size="small">
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>
                </el-descriptions-item>
                <el-descriptions-item label="Ijazah Terakhir">
                    <span v-if="!data.ijazah">-</span>
                    <el-button v-else tag="a" :href="'/uploads/'+data.ijazah" target="_blank" type="primary" size="small">
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>
                </el-descriptions-item>
                <el-descriptions-item label="Sertifikat Keahlian/Kompetensi">
                    <span v-if="!data.kompetensi">-</span>
                    <el-button v-else tag="a" :href="'/uploads/'+data.kompetensi" target="_blank" type="primary" size="small">
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>
                </el-descriptions-item>
                <el-descriptions-item label="Paspor">
                    <span v-if="!data.paspor">-</span>
                    <el-button v-else tag="a" :href="'/uploads/'+data.paspor" target="_blank" type="primary" size="small">
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>
                </el-descriptions-item>
                <el-descriptions-item label="Surat Keterangan Pengalaman Kerja">
                    <span v-if="!data.surat_pengalaman_kerja">-</span>
                    <el-button v-else tag="a" :href="'/uploads/'+data.surat_pengalaman_kerja" target="_blank" type="primary" size="small">
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>
                </el-descriptions-item>
                <el-descriptions-item label="SKCK">
                    <span v-if="!data.skck">-</span>
                    <el-button v-else tag="a" :href="'/uploads/'+data.skck" target="_blank" type="primary" size="small">
                        <i class="fa fa-file-pdf-o"></i> Lihat
                    </el-button>
                </el-descriptions-item>
            </el-descriptions>
            </template>
        </el-card>
    </user-layout>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import moment from 'moment';
const props = defineProps({
    data: {
        type: Object,
        default: () => ({})
    },
    checking: {
        type: Boolean,
        default: false
    },
});


const format_date = (value) => {
    if (value) {
        moment().locale('id');
        return moment(String(value)).format('DD MMMM YYYY')
    }
}

</script>