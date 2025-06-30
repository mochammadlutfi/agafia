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
                    
                    <el-dropdown v-if="activeApplication && activeApplication.status !== 'selesai'" trigger="click">
                        <el-button type="warning" size="small">
                            <i class="fa fa-exchange me-1"></i>
                            Ubah Status Lamaran
                            <i class="fa fa-chevron-down ms-1"></i>
                        </el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item 
                                    v-for="(label, status) in statusOptions" 
                                    :key="status"
                                    @click="changeApplicationStatus(status)"
                                    :disabled="status === activeApplication?.status"
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
                        <div class="d-flex flex-column">
                            <el-tag 
                                v-if="activeApplication"
                                :type="getStatusType(activeApplication.status)" 
                                size="large"
                            >
                                {{ getStatusLabel(activeApplication.status) }}
                            </el-tag>
                            <small class="text-muted mt-1" v-if="activeApplication">
                                {{ activeApplication.lowongan?.posisi }} - {{ activeApplication.lowongan?.perusahaan }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="block-content">
                    <div class="row mb-3">
                        <div class="col-12">
                            <el-progress 
                                :percentage="applicationProgress?.percentage || 0"
                                :stroke-width="20"
                                :text-inside="true"
                                :status="activeApplication?.status === 'ditolak' ? 'exception' : 'success'"
                            >
                            </el-progress>
                        </div>
                    </div>
                    
                    <!-- Status Steps -->
                    <el-steps 
                        :active="applicationProgress?.current?.step || 0" 
                        :process-status="activeApplication?.status === 'ditolak' ? 'error' : 'process'"
                        align-center
                        class="mt-3"
                    >
                        <el-step 
                            v-for="(status, key) in applicationProgress?.all" 
                            :key="key"
                            :title="status.label"
                            v-show="key !== 'ditolak'"
                        />
                    </el-steps>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">
                                <i class="fa fa-file me-2"></i>
                                Dokumen Lamaran
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
                <div class="col-md-4">
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">
                                <i class="fa fa-briefcase me-2"></i>
                                Total Lamaran
                            </h3>
                        </div>
                        <div class="block-content text-center">
                            <div class="fs-2 fw-bold text-primary mb-2">{{ completionStats?.applications?.total || 0 }}</div>
                            <div class="small text-muted">
                                <div>Aktif: {{ completionStats?.applications?.active || 0 }}</div>
                                <div>Selesai: {{ completionStats?.applications?.completed || 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Applications List -->
            <div class="block block-rounded mb-4" v-if="talent?.lamaran && talent.lamaran.length > 0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-list me-2"></i>
                        Daftar Lamaran
                    </h3>
                </div>
                <div class="block-content">
                    <el-table :data="talent?.lamaran || []" border class="w-100" header-cell-class-name="bg-body text-dark">
                        <el-table-column prop="tanggal_lamaran" label="Tanggal" width="120">
                            <template #default="{ row }">
                                {{ formatDate(row.tanggal_lamaran) }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="lowongan.posisi" label="Posisi" />
                        <el-table-column prop="lowongan.perusahaan" label="Perusahaan" />
                        <el-table-column prop="lowongan.negara_tujuan" label="Negara" width="120" />
                        <el-table-column prop="status" label="Status" width="150">
                            <template #default="{ row }">
                                <el-tag :type="getStatusType(row.status)" size="small">
                                    {{ getStatusLabel(row.status) }}
                                </el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="Aksi" width="150">
                            <template #default="{ row }">
                                <el-button :tag="Link" :href="route('admin.lamaran.show', row.id)" type="primary" size="small">
                                    Detail
                                </el-button>
                            </template>
                        </el-table-column>
                    </el-table>
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
                                    v-if="activeApplication?.status === 'ditolak'"
                                    title="Catatan Penolakan Lamaran"
                                    type="error"
                                    :description="activeApplication?.catatan"
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
                                <div class="border-bottom border-2 mb-4 d-flex justify-content-between align-items-center">
                                    <h4 class="h5 mb-2">
                                        <i class="fa fa-files-o me-2"></i>
                                        Dokumen Per Lamaran
                                    </h4>
                                    <el-button :tag="Link" :href="route('admin.dokumen-lamaran.index')" type="primary" size="small">
                                        Kelola Semua Dokumen
                                    </el-button>
                                </div>
                                
                                <div v-if="documentStats">
                                    <el-row :gutter="16" class="mb-4">
                                        <el-col :span="6">
                                            <el-statistic title="Total Dokumen" :value="documentStats.total" />
                                        </el-col>
                                        <el-col :span="6">
                                            <el-statistic title="Menunggu Review" :value="documentStats.pending" />
                                        </el-col>
                                        <el-col :span="6">
                                            <el-statistic title="Disetujui" :value="documentStats.approved" />
                                        </el-col>
                                        <el-col :span="6">
                                            <el-statistic title="Tingkat Persetujuan" :value="documentStats.approval_rate" suffix="%" :precision="1" />
                                        </el-col>
                                    </el-row>
                                </div>
                                
                                <div v-if="activeApplication && activeApplication.dokumen">
                                    <h5 class="mb-3">Dokumen Lamaran Aktif ({{ activeApplication.lowongan?.posisi }})</h5>
                                    <el-table :data="activeApplication.dokumen" border class="w-100" header-cell-class-name="bg-body text-dark" :empty-text="'Belum ada dokumen diupload'">
                                        <el-table-column prop="kategoriDokumen.nama_kategori" label="Kategori Dokumen" />
                                        <el-table-column prop="nama_file" label="Nama File" />
                                        <el-table-column prop="diupload_tanggal" label="Tanggal Upload" width="120">
                                            <template #default="{ row }">
                                                {{ formatDate(row.diupload_tanggal) }}
                                            </template>
                                        </el-table-column>
                                        <el-table-column prop="status" label="Status" width="120">
                                            <template #default="{ row }">
                                                <el-tag :type="getDocumentStatusType(row.status)" size="small">
                                                    {{ getDocumentStatusLabel(row.status) }}
                                                </el-tag>
                                            </template>
                                        </el-table-column>
                                        <el-table-column label="Aksi" width="150">
                                            <template #default="{ row }">
                                                <el-button :tag="Link" :href="route('admin.dokumen-lamaran.show', row.id)" type="primary" size="small">
                                                    Detail
                                                </el-button>
                                            </template>
                                        </el-table-column>
                                    </el-table>
                                </div>
                                
                                <div v-else class="text-center py-4">
                                    <el-empty description="Tidak ada lamaran aktif atau belum ada dokumen diupload" />
                                </div>
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
                                <div v-if="talent?.lamaran && talent.lamaran.length > 0">
                                    <div v-for="lamaran in talent.lamaran" :key="lamaran.id" class="mb-4">
                                        <h6 class="text-primary">{{ lamaran.lowongan?.posisi }} - {{ lamaran.lowongan?.perusahaan }}</h6>
                                        <el-table 
                                            :data="lamaran.jadwalInterview || []" 
                                            border 
                                            class="w-100 mb-3" 
                                            header-cell-class-name="bg-body text-dark"
                                            :empty-text="'Belum ada jadwal interview untuk lamaran ini'"
                                        >
                                            <el-table-column prop="tanggal" label="Tanggal" width="120">
                                                <template #default="{ row }">
                                                    {{ formatDate(row.tanggal) }}
                                                </template>
                                            </el-table-column>
                                            <el-table-column prop="waktu" label="Waktu" width="100" />
                                            <el-table-column prop="pewawancara.nama" label="Pewawancara" />
                                            <el-table-column prop="hasil.skor_interview" label="Skor Interview" width="120" align="center">
                                                <template #default="{ row }">
                                                    <el-tag v-if="row.hasil?.skor_interview" :type="getScoreType(row.hasil.skor_interview)">
                                                        {{ row.hasil.skor_interview }}
                                                    </el-tag>
                                                    <span v-else class="text-muted">-</span>
                                                </template>
                                            </el-table-column>
                                            <el-table-column prop="hasil.rekomendasi" label="Rekomendasi" width="150">
                                                <template #default="{ row }">
                                                    <el-tag v-if="row.hasil?.rekomendasi" :type="getRecommendationType(row.hasil.rekomendasi)" size="small">
                                                        {{ getRecommendationLabel(row.hasil.rekomendasi) }}
                                                    </el-tag>
                                                    <span v-else class="text-muted">-</span>
                                                </template>
                                            </el-table-column>
                                            <el-table-column prop="hasil.catatan" label="Catatan" min-width="200">
                                                <template #default="{ row }">
                                                    {{ row.hasil?.catatan || '-' }}
                                                </template>
                                            </el-table-column>
                                            <el-table-column prop="status" label="Status" width="120">
                                                <template #default="{ row }">
                                                    <el-tag :type="row.hasil ? 'success' : 'warning'" size="small">
                                                        {{ row.hasil ? 'Selesai' : 'Terjadwal' }}
                                                    </el-tag>
                                                </template>
                                            </el-table-column>
                                        </el-table>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4">
                                    <el-empty description="Belum ada lamaran" />
                                </div>
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
                                <div v-if="talent?.lamaran && talent.lamaran.length > 0">
                                    <div v-for="lamaran in talent.lamaran" :key="lamaran.id" class="mb-4">
                                        <h6 class="text-primary">{{ lamaran.lowongan?.posisi }} - {{ lamaran.lowongan?.perusahaan }}</h6>
                                        <el-table 
                                            :data="lamaran.training || []" 
                                            border 
                                            class="w-100 mb-3" 
                                            header-cell-class-name="bg-body text-dark"
                                            :empty-text="'Belum ada data pelatihan untuk lamaran ini'"
                                        >
                                            <el-table-column prop="program.nama" label="Program" min-width="200" />
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
                                            <el-table-column prop="staff.nama" label="Didaftarkan Oleh" width="150" />
                                        </el-table>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4">
                                    <el-empty description="Belum ada lamaran" />
                                </div>
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
                                <div v-if="talent?.lamaran && talent.lamaran.length > 0">
                                    <div v-for="lamaran in talent.lamaran" :key="lamaran.id" class="mb-4">
                                        <h6 class="text-primary">{{ lamaran.lowongan?.posisi }} - {{ lamaran.lowongan?.perusahaan }}</h6>
                                        <div v-if="lamaran.medical && lamaran.medical.length > 0">
                                            <div v-for="(medical, index) in lamaran.medical" :key="index" class="mb-3">
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
                                                            {{ formatDate(medical.tanggal) }}
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
                                        <div v-else class="text-center py-3">
                                            <el-empty description="Belum ada data medical check up untuk lamaran ini" :image-size="60" />
                                        </div>
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
    activeApplication: {
        type: Object,
        default: () => null
    },
    applicationProgress: {
        type: Object,
        default: () => ({})
    },
    completionStats: {
        type: Object,
        default: () => ({})
    },
    documentStats: {
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

// Status mappings for applications
const statusLabels = {
    'pending': 'Menunggu Review',
    'diterima': 'Diterima',
    'ditolak': 'Ditolak',
    'interview': 'Tahap Interview',
    'medical': 'Medical Check Up',
    'pelatihan': 'Pelatihan',
    'siap': 'Siap Berangkat',
    'selesai': 'Selesai'
};

const statusTypes = {
    'pending': 'warning',
    'diterima': 'success',
    'ditolak': 'danger',
    'interview': 'primary',
    'medical': 'info',
    'pelatihan': '',
    'siap': 'success',
    'selesai': 'success'
};

const getStatusLabel = (status) => statusLabels[status] || status;
const getStatusType = (status) => statusTypes[status] || '';

// Document status helpers
const getDocumentStatusLabel = (status) => {
    const labels = {
        'pending': 'Menunggu Review',
        'approved': 'Disetujui',
        'rejected': 'Ditolak'
    };
    return labels[status] || status;
};

const getDocumentStatusType = (status) => {
    const types = {
        'pending': 'warning',
        'approved': 'success', 
        'rejected': 'danger'
    };
    return types[status] || '';
};

const changeApplicationStatus = (newStatus) => {
    if (!props.activeApplication) {
        ElMessage.error('Tidak ada lamaran aktif');
        return;
    }

    if (newStatus === 'ditolak') {
        ElMessageBox.prompt('Alasan Penolakan Lamaran', 'Tolak Lamaran', {
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
            inputType: 'textarea'
        })
        .then(({ value }) => {
            updateApplicationStatus(newStatus, value);
        })
        .catch(() => {
            ElMessage.info('Penolakan dibatalkan.');
        });
    } else {
        ElMessageBox.confirm(
            `Apakah Anda yakin ingin mengubah status lamaran menjadi "${props.statusOptions[newStatus]}"?`, 
            'Ubah Status Lamaran', 
            {
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal',
                type: 'warning'
            }
        )
        .then(() => {
            updateApplicationStatus(newStatus);
        })
        .catch(() => {
            ElMessage.info('Perubahan status dibatalkan.');
        });
    }
}

const updateApplicationStatus = (newStatus, catatan = null) => {
    const loading = ElLoading.service({
        lock: true,
        text: 'Mengubah status lamaran...',
    });
    
    axios.post(`/admin/talent/${props.talent.id}/update-application-status`, {
        lamaran_id: props.activeApplication.id,
        status: newStatus,
        catatan: catatan
    })
    .then(response => {
        ElMessage.success('Status lamaran berhasil diperbarui.');
        loading.close();
        router.visit(route('admin.talent.show', props.talent.id), {
            preserveState: false,
            preserveScroll: true,
        });
    })
    .catch(error => {
        loading.close();
        console.error('Error updating application status:', error);
        ElMessage.error('Gagal mengubah status lamaran. Silakan coba lagi.');
    });
}

// Initialize component
onMounted(() => {
    moment.locale('id');
});
</script>