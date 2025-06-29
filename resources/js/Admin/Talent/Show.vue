<template>
    <base-layout title="Detail Talent">
        <div class="content">
            <!-- Header with Status and Actions -->
            <div class="content-heading d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-0">Detail Talent</h2>
                    <small class="text-muted">{{ talent?.detail?.nama || talent?.nama }}</small>
                </div>
                <div class="space-x-1">
                    <el-button 
                        type="primary" 
                        :tag="Link" 
                        :href="route('admin.talent.edit', talent?.id)"
                        size="small"
                    >
                        <i class="fa fa-edit me-1"></i>
                        Edit Profile
                    </el-button>
                    
                    <el-dropdown v-if="talent?.status !== 'selesai'" trigger="click">
                        <el-button type="warning" size="small">
                            <i class="fa fa-exchange me-1"></i>
                            Ubah Status
                            <i class="fa fa-chevron-down ms-1"></i>
                        </el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item 
                                    v-for="(label, status) in statusOptions" 
                                    :key="status"
                                    @click="changeStatus(status)"
                                    :disabled="status === talent?.status"
                                >
                                    {{ label }}
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
            </div>

            <!-- Status Progress -->
            <div class="block block-rounded mb-4">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-tasks me-2"></i>
                        Progress Status
                    </h3>
                    <div class="block-options">
                        <el-tag 
                            :type="statusProgress?.current?.color" 
                            size="large"
                        >
                            {{ statusProgress?.current?.label }}
                        </el-tag>
                    </div>
                </div>
                <div class="block-content">
                    <div class="row mb-3">
                        <div class="col-12">
                            <el-progress 
                                :percentage="statusProgress?.percentage || 0"
                                :stroke-width="20"
                                :text-inside="true"
                                :status="talent?.status === 'ditolak' ? 'exception' : 'success'"
                            >
                            </el-progress>
                        </div>
                    </div>
                    
                    <!-- Status Steps -->
                    <el-steps 
                        :active="statusProgress?.current?.step || 0" 
                        :process-status="talent?.status === 'ditolak' ? 'error' : 'process'"
                        align-center
                        class="mt-3"
                    >
                        <el-step 
                            v-for="(status, key) in statusProgress?.all" 
                            :key="key"
                            :title="status.label"
                            v-show="key !== 'ditolak'"
                        />
                    </el-steps>
                </div>
            </div>

            <!-- Completion Statistics -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">
                                <i class="fa fa-user me-2"></i>
                                Profil Lengkap
                            </h3>
                        </div>
                        <div class="block-content text-center">
                            <el-progress 
                                type="circle" 
                                :percentage="completionStats?.profile?.percentage || 0"
                                :width="120"
                            >
                                <template #default="{ percentage }">
                                    <span class="percentage-value">{{ percentage }}%</span>
                                </template>
                            </el-progress>
                            <p class="mt-2 mb-0">
                                {{ completionStats?.profile?.completed || 0 }} dari {{ completionStats?.profile?.total || 0 }} field terisi
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">
                                <i class="fa fa-file me-2"></i>
                                Dokumen Lengkap
                            </h3>
                        </div>
                        <div class="block-content text-center">
                            <el-progress 
                                type="circle" 
                                :percentage="completionStats?.documents?.percentage || 0"
                                :width="120"
                                color="#67C23A"
                            >
                                <template #default="{ percentage }">
                                    <span class="percentage-value">{{ percentage }}%</span>
                                </template>
                            </el-progress>
                            <p class="mt-2 mb-0">
                                {{ completionStats?.documents?.completed || 0 }} dari {{ completionStats?.documents?.total || 0 }} dokumen tersedia
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tab Navigation -->
            <div class="block block-rounded">
                <div class="block-content p-0">
                    <el-tabs v-model="activeTab" class="demo-tabs">
                        <!-- Personal Information Tab -->
                        <el-tab-pane label="Informasi Pribadi" name="personal">
                            <div class="p-4">
                                <!-- Alert for rejected status -->
                                <el-alert
                                    class="mb-4"
                                    v-if="talent?.status === 'ditolak'"
                                    title="Catatan Penolakan"
                                    type="error"
                                    :description="talent?.detail?.catatan"
                                    show-icon
                                    :closable="false"
                                />
                                <el-row justify="space-between">
                                    <el-col :span="17">
                                        <el-descriptions column="1" label-width="200px" :border="true" class="mb-4">
                                            <el-descriptions-item label="NIK">
                                                {{ talent?.detail?.nik || '-' }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Nama Lengkap">
                                                {{ talent?.detail?.nama || talent?.nama || '-' }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Jenis Kelamin">
                                                {{ talent?.detail?.jenis_kelamin || '-' }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Umur">
                                                {{ calculateAge(talent?.detail?.tanggal_lahir) }} Tahun
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Tempat / Tanggal Lahir">
                                                {{ talent?.detail?.tempat_lahir || '-' }} / {{ formatDate(talent?.detail?.tanggal_lahir) }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="No Handphone">
                                                <el-link type="primary" :href="`tel:${talent?.detail?.phone}`">
                                                    {{ talent?.detail?.phone || '-' }}
                                                </el-link>
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Email">
                                                <el-link type="primary" :href="`mailto:${talent?.email}`">
                                                    {{ talent?.email || '-' }}
                                                </el-link>
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Alamat">
                                                {{ talent?.detail?.alamat || '-' }}
                                            </el-descriptions-item>
                                        </el-descriptions>
                                    </el-col>
                                    <el-col :span="6">
                                        <div class="text-center">
                                            <el-image
                                                class="w-100"
                                                style="max-width: 200px; height: 250px;"
                                                :src="talent?.detail?.foto ? '/uploads/' + talent?.detail?.foto : '/images/placeholder.png'"
                                                fit="cover"
                                                :preview-src-list="[talent?.detail?.foto ? '/uploads/' + talent?.detail?.foto : '/images/placeholder.png']"
                                            />
                                            <p class="mt-2 text-muted small">Foto Profile</p>
                                        </div>
                                    </el-col>
                                </el-row>

                                <div class="border-bottom border-2 my-4">
                                    <h4 class="h5 mb-2">
                                        <i class="fa fa-users me-2"></i>
                                        Pemberi Izin & Orang Tua
                                    </h4>
                                </div>
                                <el-descriptions column="2" label-width="200px" :border="true" class="mb-4">
                                    <el-descriptions-item label="Nama Pemberi Izin" span="2">
                                        {{ talent?.detail?.nama_izin || '-' }}
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Status Pemberi Izin" span="2">
                                        {{ talent?.detail?.status_izin || '-' }}
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Nama Ayah">
                                        {{ talent?.detail?.nama_ayah || '-' }}
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Nama Ibu">
                                        {{ talent?.detail?.nama_ibu || '-' }}
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Alamat Orang Tua" span="2">
                                        {{ talent?.detail?.alamat_ortu || '-' }}
                                    </el-descriptions-item>
                                    <el-descriptions-item label="No HP Orang Tua" span="2">
                                        <el-link v-if="talent?.detail?.phone_ortu" type="primary" :href="`tel:${talent?.detail?.phone_ortu}`">
                                            {{ talent?.detail?.phone_ortu }}
                                        </el-link>
                                        <span v-else>-</span>
                                    </el-descriptions-item>
                                </el-descriptions>
                            </div>
                        </el-tab-pane>

                        <!-- Education & Experience Tab -->
                        <el-tab-pane label="Pendidikan & Pengalaman" name="education">
                            <div class="p-4">
                                <div class="border-bottom border-2 mb-4">
                                    <h4 class="h5 mb-2">
                                        <i class="fa fa-graduation-cap me-2"></i>
                                        Riwayat Pendidikan
                                    </h4>
                                </div>
                                <el-table 
                                    :data="talent?.detail?.pendidikan || []" 
                                    border 
                                    class="w-100 mb-4" 
                                    header-cell-class-name="bg-body text-dark"
                                    :empty-text="'Tidak ada data pendidikan'"
                                >
                                    <el-table-column prop="tingkat" label="Tingkat" />
                                    <el-table-column prop="nama_sekolah" label="Nama Sekolah" />
                                    <el-table-column prop="jurusan" label="Jurusan" />
                                    <el-table-column prop="tahun_lulus" label="Tahun Lulus" />
                                </el-table>

                                <div class="border-bottom border-2 my-4">
                                    <h4 class="h5 mb-2">
                                        <i class="fa fa-briefcase me-2"></i>
                                        Riwayat Pekerjaan
                                    </h4>
                                </div>
                                <el-table 
                                    :data="talent?.detail?.pengalaman || []" 
                                    border 
                                    class="w-100" 
                                    header-cell-class-name="bg-body text-dark"
                                    :empty-text="'Tidak ada data pengalaman kerja'"
                                >
                                    <el-table-column prop="nama" label="Nama Perusahaan" />
                                    <el-table-column prop="posisi" label="Posisi" />
                                    <el-table-column prop="tahun_masuk" label="Tahun Masuk" />
                                    <el-table-column prop="tahun_keluar" label="Tahun Keluar" />
                                </el-table>

                                <div class="border-bottom border-2 my-4">
                                    <h4 class="h5 mb-2">
                                        <i class="fa fa-target me-2"></i>
                                        Tujuan Kerja
                                    </h4>
                                </div>
                                <el-descriptions column="2" label-width="200px" :border="true">
                                    <el-descriptions-item label="Pekerjaan yang Diminati">
                                        {{ talent?.detail?.pekerjaan || '-' }}
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Negara Tujuan">
                                        {{ talent?.detail?.negara_tujuan || '-' }}
                                    </el-descriptions-item>
                                </el-descriptions>
                            </div>
                        </el-tab-pane>

                        <!-- Documents Tab -->
                        <el-tab-pane label="Dokumen" name="documents">
                            <div class="p-4">
                                <div class="border-bottom border-2 mb-4">
                                    <h4 class="h5 mb-2">
                                        <i class="fa fa-files-o me-2"></i>
                                        Dokumen Lampiran
                                    </h4>
                                </div>
                                <el-descriptions column="2" label-width="240px" :border="true" class="mb-4">
                                    <el-descriptions-item label="KTP">
                                        <span v-if="!talent?.detail?.ktp">-</span>
                                        <el-button v-else tag="a" :href="'/uploads/'+talent?.detail?.ktp" target="_blank" type="primary" size="small">
                                            <i class="fa fa-file-pdf-o"></i> Lihat
                                        </el-button>
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Kartu Keluarga">
                                        <span v-if="!talent?.detail?.kk">-</span>
                                        <el-button v-else tag="a" :href="'/uploads/'+talent?.detail?.kk" target="_blank" type="primary" size="small">
                                            <i class="fa fa-file-pdf-o"></i> Lihat
                                        </el-button>
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Akte Lahir">
                                        <span v-if="!talent?.detail?.akte_lahir">-</span>
                                        <el-button v-else tag="a" :href="'/uploads/'+talent?.detail?.akte_lahir" target="_blank" type="primary" size="small">
                                            <i class="fa fa-file-pdf-o"></i> Lihat
                                        </el-button>
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Buku Nikah">
                                        <span v-if="!talent?.detail?.buku_nikah">-</span>
                                        <el-button v-else tag="a" :href="'/uploads/'+talent?.detail?.buku_nikah" target="_blank" type="primary" size="small">
                                            <i class="fa fa-file-pdf-o"></i> Lihat
                                        </el-button>
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Surat Keterangan Sehat">
                                        <span v-if="!talent?.detail?.surat_keterangan_sehat">-</span>
                                        <el-button v-else tag="a" :href="'/uploads/'+talent?.detail?.surat_keterangan_sehat" target="_blank" type="primary" size="small">
                                            <i class="fa fa-file-pdf-o"></i> Lihat
                                        </el-button>
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Surat Izin Keluarga">
                                        <span v-if="!talent?.detail?.surat_izin_keluarga">-</span>
                                        <el-button v-else tag="a" :href="'/uploads/'+talent?.detail?.surat_izin_keluarga" target="_blank" type="primary" size="small">
                                            <i class="fa fa-file-pdf-o"></i> Lihat
                                        </el-button>
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Ijazah Terakhir">
                                        <span v-if="!talent?.detail?.ijazah">-</span>
                                        <el-button v-else tag="a" :href="'/uploads/'+talent?.detail?.ijazah" target="_blank" type="primary" size="small">
                                            <i class="fa fa-file-pdf-o"></i> Lihat
                                        </el-button>
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Sertifikat Keahlian/Kompetensi">
                                        <span v-if="!talent?.detail?.kompetensi">-</span>
                                        <el-button v-else tag="a" :href="'/uploads/'+talent?.detail?.kompetensi" target="_blank" type="primary" size="small">
                                            <i class="fa fa-file-pdf-o"></i> Lihat
                                        </el-button>
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Paspor">
                                        <span v-if="!talent?.detail?.paspor">-</span>
                                        <el-button v-else tag="a" :href="'/uploads/'+talent?.detail?.paspor" target="_blank" type="primary" size="small">
                                            <i class="fa fa-file-pdf-o"></i> Lihat
                                        </el-button>
                                    </el-descriptions-item>
                                    <el-descriptions-item label="Surat Keterangan Pengalaman Kerja">
                                        <span v-if="!talent?.detail?.surat_pengalaman_kerja">-</span>
                                        <el-button v-else tag="a" :href="'/uploads/'+talent?.detail?.surat_pengalaman_kerja" target="_blank" type="primary" size="small">
                                            <i class="fa fa-file-pdf-o"></i> Lihat
                                        </el-button>
                                    </el-descriptions-item>
                                    <el-descriptions-item label="SKCK">
                                        <span v-if="!talent?.detail?.skck">-</span>
                                        <el-button v-else tag="a" :href="'/uploads/'+talent?.detail?.skck" target="_blank" type="primary" size="small">
                                            <i class="fa fa-file-pdf-o"></i> Lihat
                                        </el-button>
                                    </el-descriptions-item>
                                </el-descriptions>
                            </div>
                        </el-tab-pane>

                        <!-- Interview History Tab -->
                        <el-tab-pane label="Riwayat Interview" name="interview">
                            <div class="p-4">
                                <div class="border-bottom border-2 mb-4">
                                    <h4 class="h5 mb-2">
                                        <i class="fa fa-comments me-2"></i>
                                        Riwayat Interview & Penilaian
                                    </h4>
                                </div>
                                <el-table 
                                    :data="jadwalInterview" 
                                    border 
                                    class="w-100" 
                                    header-cell-class-name="bg-body text-dark"
                                    :empty-text="'Belum ada jadwal interview'"
                                >
                                    <el-table-column prop="tanggal" label="Tanggal" width="120">
                                        <template #default="{ row }">
                                            {{ formatDate(row.tanggal) }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="waktu" label="Waktu" width="100" />
                                    <el-table-column prop="pewawancara_nama" label="Pewawancara" />
                                    <el-table-column prop="skor_interview" label="Skor Interview" width="120" align="center">
                                        <template #default="{ row }">
                                            <el-tag v-if="row.skor_interview" :type="getScoreType(row.skor_interview)">
                                                {{ row.skor_interview }}
                                            </el-tag>
                                            <span v-else class="text-muted">-</span>
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="skor_psikotes" label="Skor Psikotes" width="120" align="center">
                                        <template #default="{ row }">
                                            <el-tag v-if="row.skor_psikotes" :type="getScoreType(row.skor_psikotes)">
                                                {{ row.skor_psikotes }}
                                            </el-tag>
                                            <span v-else class="text-muted">-</span>
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="rekomendasi" label="Rekomendasi" width="150">
                                        <template #default="{ row }">
                                            <el-tag v-if="row.rekomendasi" :type="getRecommendationType(row.rekomendasi)" size="small">
                                                {{ getRecommendationLabel(row.rekomendasi) }}
                                            </el-tag>
                                            <span v-else class="text-muted">-</span>
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="hasil_catatan" label="Catatan" min-width="200">
                                        <template #default="{ row }">
                                            {{ row.hasil_catatan || '-' }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="status" label="Status" width="120">
                                        <template #default="{ row }">
                                            <el-tag :type="row.tanggal_penilaian ? 'success' : 'warning'" size="small">
                                                {{ row.tanggal_penilaian ? 'Selesai' : 'Terjadwal' }}
                                            </el-tag>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>
                        </el-tab-pane>

                        <!-- Training History Tab -->
                        <el-tab-pane label="Riwayat Pelatihan" name="training">
                            <div class="p-4">
                                <div class="border-bottom border-2 mb-4">
                                    <h4 class="h5 mb-2">
                                        <i class="fa fa-graduation-cap me-2"></i>
                                        Riwayat Pelatihan
                                    </h4>
                                </div>
                                <el-table 
                                    :data="training" 
                                    border 
                                    class="w-100" 
                                    header-cell-class-name="bg-body text-dark"
                                    :empty-text="'Belum ada data pelatihan'"
                                >
                                    <el-table-column prop="program_nama" label="Program" min-width="200" />
                                    <el-table-column prop="tanggal_daftar" label="Tanggal Daftar" width="120">
                                        <template #default="{ row }">
                                            {{ formatDate(row.tanggal_daftar) }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="tanggal_mulai" label="Mulai" width="120">
                                        <template #default="{ row }">
                                            {{ formatDate(row.tanggal_mulai) }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="tanggal_selesai" label="Selesai" width="120">
                                        <template #default="{ row }">
                                            {{ formatDate(row.tanggal_selesai) }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="status" label="Status" width="150">
                                        <template #default="{ row }">
                                            <el-tag :type="getTrainingStatusType(row.status)" size="small">
                                                {{ getTrainingStatusLabel(row.status) }}
                                            </el-tag>
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="nilai_akhir" label="Nilai" width="100" align="center">
                                        <template #default="{ row }">
                                            <el-tag v-if="row.nilai_akhir" :type="getScoreType(row.nilai_akhir)">
                                                {{ row.nilai_akhir }}
                                            </el-tag>
                                            <span v-else class="text-muted">-</span>
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="nomor_sertifikat" label="No. Sertifikat" width="150">
                                        <template #default="{ row }">
                                            {{ row.nomor_sertifikat || '-' }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="pendaftar_nama" label="Didaftarkan Oleh" width="150" />
                                </el-table>
                            </div>
                        </el-tab-pane>

                        <!-- Medical Records Tab -->
                        <el-tab-pane label="Medical Records" name="medical">
                            <div class="p-4">
                                <div class="border-bottom border-2 mb-4">
                                    <h4 class="h5 mb-2">
                                        <i class="fa fa-heartbeat me-2"></i>
                                        Medical Check Up Records
                                    </h4>
                                </div>
                                <div v-if="talent?.medical && talent.medical.length > 0">
                                    <div v-for="(medical, index) in talent.medical" :key="index" class="mb-4">
                                        <el-card>
                                            <template #header>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">Medical Check #{{ index + 1 }}</h6>
                                                    <el-tag :type="medical.status === 'lulus' ? 'success' : 'danger'" size="small">
                                                        {{ medical.status === 'lulus' ? 'Lulus' : 'Tidak Lulus' }}
                                                    </el-tag>
                                                </div>
                                            </template>
                                            <el-descriptions column="2" label-width="200px" :border="true">
                                                <el-descriptions-item label="Tanggal Medical">
                                                    {{ formatDate(medical.tanggal_medical) }}
                                                </el-descriptions-item>
                                                <el-descriptions-item label="Rumah Sakit">
                                                    {{ medical.rumah_sakit || '-' }}
                                                </el-descriptions-item>
                                                <el-descriptions-item label="Dokter Pemeriksa">
                                                    {{ medical.dokter || '-' }}
                                                </el-descriptions-item>
                                                <el-descriptions-item label="Tinggi Badan">
                                                    {{ medical.tinggi_badan ? medical.tinggi_badan + ' cm' : '-' }}
                                                </el-descriptions-item>
                                                <el-descriptions-item label="Berat Badan">
                                                    {{ medical.berat_badan ? medical.berat_badan + ' kg' : '-' }}
                                                </el-descriptions-item>
                                                <el-descriptions-item label="Tekanan Darah">
                                                    {{ medical.tekanan_darah || '-' }}
                                                </el-descriptions-item>
                                                <el-descriptions-item label="Golongan Darah">
                                                    {{ medical.golongan_darah || '-' }}
                                                </el-descriptions-item>
                                                <el-descriptions-item label="Mata">
                                                    {{ medical.mata || '-' }}
                                                </el-descriptions-item>
                                                <el-descriptions-item label="Catatan" span="2">
                                                    {{ medical.catatan || '-' }}
                                                </el-descriptions-item>
                                            </el-descriptions>
                                        </el-card>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4">
                                    <el-empty description="Belum ada data medical check up" />
                                </div>
                            </div>
                        </el-tab-pane>
                    </el-tabs>
                </div>
            </div>
        </div>
    </base-layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import moment from 'moment';
import { ElMessage, ElMessageBox, ElLoading } from 'element-plus';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

// Reactive state
const activeTab = ref('personal');

const props = defineProps({
    talent: {
        type: Object,
        default: () => ({})
    },
    jadwalInterview: {
        type: Array,
        default: () => []
    },
    training: {
        type: Array,
        default: () => []
    },
    statusProgress: {
        type: Object,
        default: () => ({})
    },
    completionStats: {
        type: Object,
        default: () => ({})
    },
    statusOptions: {
        type: Object,
        default: () => ({})
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});


// Helper methods
const formatDate = (value) => {
    if (value) {
        moment.locale('id');
        return moment(String(value)).format('DD MMMM YYYY')
    }
    return '-';
}

const calculateAge = (birthDate) => {
    if (!birthDate) return '-';
    return moment().diff(moment(birthDate), 'years');
}

const getScoreType = (score) => {
    if (score >= 80) return 'success';
    if (score >= 70) return 'warning';
    if (score >= 60) return 'info';
    return 'danger';
}

const getRecommendationType = (recommendation) => {
    const types = {
        'sangat_direkomendasikan': 'success',
        'direkomendasikan': 'success',
        'bersyarat': 'warning',
        'tidak_direkomendasikan': 'danger'
    };
    return types[recommendation] || 'info';
}

const getRecommendationLabel = (recommendation) => {
    const labels = {
        'sangat_direkomendasikan': 'Sangat Direkomendasikan',
        'direkomendasikan': 'Direkomendasikan',
        'bersyarat': 'Bersyarat',
        'tidak_direkomendasikan': 'Tidak Direkomendasikan'
    };
    return labels[recommendation] || recommendation;
}

const getTrainingStatusType = (status) => {
    const types = {
        'terdaftar': 'info',
        'sedang_pelatihan': 'warning',
        'selesai': 'success',
        'mengundurkan_diri': 'danger'
    };
    return types[status] || 'info';
}

const getTrainingStatusLabel = (status) => {
    const labels = {
        'terdaftar': 'Terdaftar',
        'sedang_pelatihan': 'Sedang Pelatihan',
        'selesai': 'Selesai',
        'mengundurkan_diri': 'Mengundurkan Diri'
    };
    return labels[status] || status;
}

const changeStatus = (newStatus) => {
    if (newStatus === 'ditolak') {
        ElMessageBox.prompt('Alasan Penolakan', 'Tolak Talent', {
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
            inputType: 'textarea'
        })
        .then(({ value }) => {
            updateStatus(newStatus, value);
        })
        .catch(() => {
            ElMessage.info('Penolakan dibatalkan.');
        });
    } else {
        ElMessageBox.confirm(
            `Apakah Anda yakin ingin mengubah status menjadi "${props.statusOptions[newStatus]}"?`, 
            'Ubah Status', 
            {
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal',
                type: 'warning'
            }
        )
        .then(() => {
            updateStatus(newStatus);
        })
        .catch(() => {
            ElMessage.info('Perubahan status dibatalkan.');
        });
    }
}

const updateStatus = (newStatus, reason = null) => {
    const loading = ElLoading.service({
        lock: true,
        text: 'Mengubah status...',
    });
    
    axios.post(`/admin/talent/${props.talent.id}/state`, {
        state: newStatus,
        reason: reason
    })
    .then(response => {
        ElMessage.success('Status berhasil diperbarui.');
        loading.close();
        router.visit(route('admin.talent.show', props.talent.id), {
            preserveState: false,
            preserveScroll: true,
        });
    })
    .catch(error => {
        loading.close();
        console.error('Error updating status:', error);
        ElMessage.error('Gagal mengubah status. Silakan coba lagi.');
    });
}

// Initialize component
onMounted(() => {
    moment.locale('id');
});
</script>