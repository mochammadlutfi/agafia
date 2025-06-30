<template>
    <base-layout title="Detail Lamaran">
        <div class="content">
            <!-- Header -->
            <div class="content-heading d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-0">Detail Lamaran #{{ lamaran?.id }}</h2>
                    <small class="text-muted">{{ lamaran?.user?.detail?.nama || lamaran?.user?.nama }}</small>
                </div>
                <div class="space-x-1">
                    <el-button :tag="Link" :href="route('admin.lamaran.index')" type="default">
                        <i class="fa fa-arrow-left me-1"></i>
                        Kembali
                    </el-button>
                    <el-dropdown trigger="click" v-if="lamaran?.status !== 'selesai'">
                        <el-button type="warning">
                            <i class="fa fa-exchange me-1"></i>
                            Ubah Status
                            <i class="fa fa-chevron-down ms-1"></i>
                        </el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item
                                    v-for="(label, status) in statusOptions"
                                    :key="status"
                                    @click="updateStatus(status)"
                                    :disabled="status === lamaran?.status"
                                >
                                    {{ label }}
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
            </div>

            <!-- Application Status Card -->
            <div class="block block-rounded mb-4">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-info-circle me-2"></i>
                        Status Lamaran
                    </h3>
                    <div class="block-options">
                        <el-tag :type="getStatusType(lamaran?.status)" size="large">
                            {{ getStatusLabel(lamaran?.status) }}
                        </el-tag>
                    </div>
                </div>
                <div class="block-content  p-4">
                    <!-- Progress Steps -->
                    <el-steps 
                        :active="getProgressStep(lamaran?.status)" 
                        :process-status="lamaran?.status === 'ditolak' ? 'error' : 'process'"
                        align-center
                        class="mb-4"
                    >
                        <el-step title="Lamaran Dikirim" />
                        <el-step title="Review" />
                        <el-step title="Interview" />
                        <el-step title="Medical Check" />
                        <el-step title="Pelatihan" />
                        <el-step title="Siap Berangkat" />
                        <el-step title="Selesai" />
                    </el-steps>

                    <!-- Rejection Alert -->
                    <el-alert
                        v-if="lamaran?.status === 'ditolak'"
                        title="Lamaran Ditolak"
                        type="error"
                        :description="lamaran?.catatan"
                        show-icon
                        :closable="false"
                        class="mb-4"
                    />

                    <!-- Application Details -->
                    <el-row :gutter="16">
                        <el-col :span="12">
                            <el-descriptions title="Detail Lamaran" column="1" border>
                                <el-descriptions-item label="Tanggal Lamaran">
                                    {{ formatDate(lamaran?.tanggal_lamaran) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Status">
                                    <el-tag :type="getStatusType(lamaran?.status)">
                                        {{ getStatusLabel(lamaran?.status) }}
                                    </el-tag>
                                </el-descriptions-item>
                                <el-descriptions-item label="CV File" v-if="lamaran?.cv_file">
                                    <el-button 
                                        tag="a" 
                                        :href="`/storage/${lamaran.cv_file}`" 
                                        target="_blank" 
                                        type="primary" 
                                        size="small"
                                    >
                                        <i class="fa fa-download me-1"></i>
                                        Download CV
                                    </el-button>
                                </el-descriptions-item>
                                <el-descriptions-item label="Catatan" v-if="lamaran?.catatan">
                                    {{ lamaran.catatan }}
                                </el-descriptions-item>
                            </el-descriptions>
                        </el-col>
                        <el-col :span="12">
                            <el-descriptions title="Informasi Lowongan" column="1" border>
                                <el-descriptions-item label="Posisi">
                                    {{ lamaran?.lowongan?.posisi }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Perusahaan">
                                    {{ lamaran?.lowongan?.perusahaan }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Negara Tujuan">
                                    {{ lamaran?.lowongan?.negara_tujuan }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Gaji">
                                    {{ formatCurrency(lamaran?.lowongan?.gaji) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Status Lowongan">
                                    <el-tag :type="lamaran?.lowongan?.status === 'buka' ? 'success' : 'danger'" size="small">
                                        {{ lamaran?.lowongan?.status === 'buka' ? 'Buka' : 'Tutup' }}
                                    </el-tag>
                                </el-descriptions-item>
                            </el-descriptions>
                        </el-col>
                    </el-row>
                </div>
            </div>

            <!-- Completion Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">
                                <i class="fa fa-file-text me-2"></i>
                                Kelengkapan Dokumen
                            </h3>
                        </div>
                        <div class="block-content text-center p-4">
                            <el-progress 
                                type="circle" 
                                :percentage="getDocumentCompletionPercentage()"
                                :width="120"
                                color="#67C23A"
                            >
                                <template #default="{ percentage }">
                                    <span class="percentage-value">{{ percentage }}%</span>
                                </template>
                            </el-progress>
                            <p class="mt-2 mb-0">
                                {{ completionStats?.documents?.completed || 0 }} dari {{ completionStats?.documents?.total || 0 }} dokumen
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">
                                <i class="fa fa-comments me-2"></i>
                                Interview
                            </h3>
                        </div>
                        <div class="block-content text-center p-4">
                            <div class="fs-2 fw-bold text-primary mb-2">
                                {{ lamaran?.interview?.length || 0 }}
                            </div>
                            <p class="mb-2">Jadwal Interview</p>
                            <el-button 
                                @click="showInterviewModal = true"
                                type="primary" 
                                size="small"
                                v-if="['diterima', 'interview'].includes(lamaran?.status)"
                            >
                                Jadwalkan Interview
                            </el-button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">
                                <i class="fa fa-stethoscope me-2"></i>
                                Medical Check
                            </h3>
                        </div>
                        <div class="block-content text-center p-4">
                            <div class="fs-2 fw-bold text-info mb-2">
                                {{ lamaran?.medical?.length || 0 }}
                            </div>
                            <p class="mb-2">Medical Check Up</p>
                            <el-button 
                                @click="showMedicalModal = true"
                                type="info" 
                                size="small"
                                v-if="['interview', 'medical'].includes(lamaran?.status)"
                            >
                                Jadwalkan MCU
                            </el-button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">
                                <i class="fa fa-graduation-cap me-2"></i>
                                Pelatihan
                            </h3>
                        </div>
                        <div class="block-content text-center p-4">
                            <div class="fs-2 fw-bold text-success mb-2">
                                {{ lamaran?.training?.length || 0 }}
                            </div>
                            <p class="mb-2">Program Pelatihan</p>
                            <el-button 
                                @click="showTrainingModal = true"
                                type="success" 
                                size="small"
                                v-if="['medical', 'pelatihan'].includes(lamaran?.status)"
                            >
                                Daftarkan Pelatihan
                            </el-button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Content -->
            <div class="block block-rounded">
                <div class="block-content p-4">
                    <el-tabs v-model="activeTab" class="demo-tabs">
                        <!-- Applicant Information -->
                        <el-tab-pane label="Informasi Pelamar" name="applicant">
                            <div class="p-4">
                                <el-row :gutter="24">
                                    <el-col :span="16">
                                        <el-descriptions title="Data Pribadi" column="1" border>
                                            <el-descriptions-item label="Nama Lengkap">
                                                {{ lamaran?.user?.detail?.nama || lamaran?.user?.nama }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Email">
                                                <el-link type="primary" :href="`mailto:${lamaran?.user?.email}`">
                                                    {{ lamaran?.user?.email }}
                                                </el-link>
                                            </el-descriptions-item>
                                            <el-descriptions-item label="NIK">
                                                {{ lamaran?.user?.detail?.nik || '-' }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="No. Handphone">
                                                <el-link type="primary" :href="`tel:${lamaran?.user?.detail?.phone}`">
                                                    {{ lamaran?.user?.detail?.phone || '-' }}
                                                </el-link>
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Tempat, Tanggal Lahir">
                                                {{ lamaran?.user?.detail?.tempat_lahir || '-' }}, {{ formatDate(lamaran?.user?.detail?.tanggal_lahir) }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Jenis Kelamin">
                                                {{ lamaran?.user?.detail?.jenis_kelamin || '-' }}
                                            </el-descriptions-item>
                                            <el-descriptions-item label="Alamat">
                                                {{ lamaran?.user?.detail?.alamat || '-' }}
                                            </el-descriptions-item>
                                        </el-descriptions>
                                    </el-col>
                                    <el-col :span="8">
                                        <div class="text-center">
                                            <el-image
                                                style="width: 200px; height: 250px"
                                                :src="lamaran?.user?.detail?.foto ? lamaran.user.detail.foto : '/images/placeholder.png'"
                                                fit="cover"
                                                :preview-src-list="[lamaran?.user?.detail?.foto ? `/storage/${lamaran.user.detail.foto}` : '/images/placeholder.png']"
                                            />
                                            <p class="mt-2 text-muted">Foto Pelamar</p>
                                        </div>
                                    </el-col>
                                </el-row>
                                
                                <el-button 
                                    :tag="Link" 
                                    :href="route('admin.talent.show', lamaran?.user?.id)" 
                                    type="primary"
                                    class="mt-3"
                                >
                                    <i class="fa fa-user me-1"></i>
                                    Lihat Profil Lengkap
                                </el-button>
                            </div>
                        </el-tab-pane>

                        <!-- Documents -->
                        <el-tab-pane label="Dokumen" name="documents">
                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5>Dokumen Lamaran</h5>
                                    <el-button 
                                        :tag="Link" 
                                        :href="route('admin.dokumen-lamaran.by-lamaran', lamaran?.id)" 
                                        type="primary"
                                    >
                                        Kelola Dokumen
                                    </el-button>
                                </div>

                                <el-table 
                                    :data="lamaran?.dokumen || []" 
                                    border 
                                    style="width: 100%"
                                    :empty-text="'Belum ada dokumen diupload'"
                                >
                                    <el-table-column prop="kategori_dokumen.nama_kategori" label="Kategori" />
                                    <el-table-column prop="nama_file" label="Nama File" />
                                    <el-table-column prop="diupload_tanggal" label="Tanggal Upload" width="140">
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
                                    <el-table-column label="Aksi" width="180">
                                        <template #default="{ row }">
                                            <div class="d-flex gap-1">
                                                <el-button 
                                                    :tag="Link" 
                                                    :href="route('admin.dokumen-lamaran.show', row.id)" 
                                                    type="primary" 
                                                    size="small"
                                                >
                                                    Detail
                                                </el-button>
                                                <el-button 
                                                    tag="a" 
                                                    :href="route('admin.dokumen-lamaran.view', row.id)" 
                                                    target="_blank" 
                                                    type="info" 
                                                    size="small"
                                                >
                                                    Lihat
                                                </el-button>
                                            </div>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>
                        </el-tab-pane>

                        <!-- Interview History -->
                        <el-tab-pane label="Riwayat Interview" name="interview">
                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5>Riwayat Interview</h5>
                                    <el-button 
                                        @click="showInterviewModal = true"
                                        type="primary"
                                        v-if="['diterima', 'interview'].includes(lamaran?.status)"
                                    >
                                        Jadwalkan Interview
                                    </el-button>
                                </div>

                                <el-table 
                                    :data="lamaran?.interview || []" 
                                    border 
                                    style="width: 100%"
                                    :empty-text="'Belum ada jadwal interview'"
                                >
                                    <el-table-column prop="tanggal" label="Tanggal" width="120">
                                        <template #default="{ row }">
                                            {{ formatDate(row.tanggal) }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="waktu" label="Waktu" width="100" />
                                    <el-table-column prop="pewawancara.nama" label="Pewawancara" />
                                    <el-table-column prop="lokasi" label="Lokasi" />
                                    <el-table-column prop="status" label="Status" width="120">
                                        <template #default="{ row }">
                                            <el-tag :type="row.hasil ? 'success' : 'warning'" size="small">
                                                {{ row.hasil ? 'Selesai' : 'Terjadwal' }}
                                            </el-tag>
                                        </template>
                                    </el-table-column>
                                    <el-table-column label="Aksi" width="120">
                                        <template #default="{ row }">
                                            <el-button 
                                                :tag="Link" 
                                                :href="route('admin.interview.show', row.id)" 
                                                type="primary" 
                                                size="small"
                                            >
                                                Detail
                                            </el-button>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>
                        </el-tab-pane>

                        <!-- Training History -->
                        <el-tab-pane label="Pelatihan" name="training">
                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5>Riwayat Pelatihan</h5>
                                    <el-button 
                                        @click="showTrainingModal = true"
                                        type="primary"
                                        v-if="['medical', 'pelatihan'].includes(lamaran?.status)"
                                    >
                                        Daftarkan Pelatihan
                                    </el-button>
                                </div>

                                <el-table 
                                    :data="lamaran?.training || []" 
                                    border 
                                    style="width: 100%"
                                    :empty-text="'Belum ada data pelatihan'"
                                >
                                    <el-table-column prop="program.nama" label="Program" />
                                    <el-table-column prop="tanggal_daftar" label="Tanggal Daftar" width="120">
                                        <template #default="{ row }">
                                            {{ formatDate(row.tanggal_daftar) }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="status" label="Status" width="150">
                                        <template #default="{ row }">
                                            <el-tag :type="getTrainingStatusType(row.status)" size="small">
                                                {{ getTrainingStatusLabel(row.status) }}
                                            </el-tag>
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="nilai_akhir" label="Nilai" width="80" align="center" />
                                    <el-table-column label="Aksi" width="120">
                                        <template #default="{ row }">
                                            <el-button 
                                                :tag="Link" 
                                                :href="route('admin.training.show', row.id)" 
                                                type="primary" 
                                                size="small"
                                            >
                                                Detail
                                            </el-button>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>
                        </el-tab-pane>
                    </el-tabs>
                </div>
            </div>
        </div>

        <!-- Interview Schedule Modal -->
        <el-dialog
            v-model="showInterviewModal"
            title="Jadwalkan Interview"
            width="700px"
            :before-close="handleInterviewModalClose"
        >
            <el-form :model="interviewForm" :rules="interviewRules" ref="interviewFormRef" label-width="150px">
                <el-row :gutter="16">
                    <el-col :span="12">
                        <el-form-item label="Tanggal" prop="tanggal">
                            <el-date-picker
                                v-model="interviewForm.tanggal"
                                type="date"
                                placeholder="Pilih tanggal"
                                format="DD/MM/YYYY "
                                value-format="YYYY-MM-DD"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Waktu" prop="waktu">
                            <el-time-picker
                                v-model="interviewForm.waktu"
                                placeholder="Pilih waktu"
                                format="HH:mm"
                                value-format="HH:mm:ss"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="Lokasi" prop="lokasi">
                    <el-input v-model="interviewForm.lokasi" placeholder="Masukkan lokasi interview (misal: Kantor Pusat, Online via Zoom, dll)" />
                </el-form-item>
                <el-form-item label="Pewawancara" prop="pewawancara_id">
                    <select-staff v-model="interviewForm.pewawancara_id" />
                </el-form-item>
                <el-form-item label="Status" prop="status">
                    <el-select v-model="interviewForm.status" placeholder="Pilih status" style="width: 100%">
                        <el-option label="Dijadwalkan" value="dijadwalkan" />
                        <el-option label="Selesai" value="selesai" />
                        <el-option label="Dibatalkan" value="dibatalkan" />
                        <el-option label="Dijadwal Ulang" value="dijadwal_ulang" />
                    </el-select>
                </el-form-item>
                <el-form-item label="Catatan">
                    <el-input
                        v-model="interviewForm.catatan"
                        type="textarea"
                        :rows="3"
                        placeholder="Catatan tambahan (opsional)"
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showInterviewModal = false">Batal</el-button>
                    <el-button type="primary" @click="submitInterviewForm" :loading="interviewLoading">
                        Jadwalkan Interview
                    </el-button>
                </span>
            </template>
        </el-dialog>

        <!-- Medical Check Modal -->
        <el-dialog
            v-model="showMedicalModal"
            title="Jadwalkan Medical Check Up"
            width="600px"
            :before-close="handleMedicalModalClose"
        >
            <el-form :model="medicalForm" :rules="medicalRules" ref="medicalFormRef" label-width="150px">
                <el-form-item label="Tanggal Medical" prop="tanggal">
                    <el-date-picker
                        v-model="medicalForm.tanggal"
                        type="date"
                        placeholder="Pilih tanggal"
                        format="DD/MM/YYYY"
                        value-format="YYYY-MM-DD"
                        style="width: 100%"
                    />
                </el-form-item>
                <el-form-item label="Nama Medical" prop="nama">
                    <el-input v-model="medicalForm.nama" placeholder="Nama medical check (misal: MCU Lengkap, Rontgen Dada, dll)" />
                </el-form-item>
                <el-form-item label="Upload File" prop="file">
                    <el-upload
                        class="upload-demo"
                        drag
                        action="#"
                        :before-upload="handleMedicalFileUpload"
                        :show-file-list="false"
                        accept=".pdf,.jpg,.jpeg,.png"
                    >
                        <el-icon class="el-icon--upload"><upload-filled /></el-icon>
                        <div class="el-upload__text">
                            Drop file here atau <em>click to upload</em>
                        </div>
                        <template #tip>
                            <div class="el-upload__tip">
                                PDF, JPG, PNG files dengan ukuran kurang dari 2MB
                            </div>
                        </template>
                    </el-upload>
                    <div v-if="medicalForm.file" class="mt-2">
                        <el-tag type="success">{{ medicalForm.fileName }}</el-tag>
                    </div>
                </el-form-item>
                <el-form-item label="Hasil" prop="hasil">
                    <el-input v-model="medicalForm.hasil" placeholder="Hasil medical check" />
                </el-form-item>
                <el-form-item label="Status" prop="status">
                    <el-select v-model="medicalForm.status" placeholder="Pilih status" style="width: 100%">
                        <el-option label="Pending" value="pending" />
                        <el-option label="Valid" value="valid" />
                        <el-option label="Tidak Valid" value="tidak valid" />
                    </el-select>
                </el-form-item>
                <el-form-item label="Catatan">
                    <el-input
                        v-model="medicalForm.catatan"
                        type="textarea"
                        :rows="3"
                        placeholder="Catatan tambahan (opsional)"
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showMedicalModal = false">Batal</el-button>
                    <el-button type="info" @click="submitMedicalForm" :loading="medicalLoading">
                        Jadwalkan MCU
                    </el-button>
                </span>
            </template>
        </el-dialog>

        <!-- Training Modal -->
        <el-dialog
            v-model="showTrainingModal"
            title="Daftarkan Pelatihan"
            width="600px"
            :before-close="handleTrainingModalClose"
        >
            <el-form :model="trainingForm" :rules="trainingRules" ref="trainingFormRef" label-width="150px">
                <el-form-item label="Program Pelatihan" prop="program_id">
                    <el-select v-model="trainingForm.program_id" placeholder="Pilih program pelatihan" style="width: 100%">
                        <el-option 
                            v-for="program in trainingPrograms" 
                            :key="program.id" 
                            :label="program.nama" 
                            :value="program.id" 
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="Tanggal Daftar" prop="tanggal_daftar">
                    <el-date-picker
                        v-model="trainingForm.tanggal_daftar"
                        type="date"
                        placeholder="Pilih tanggal daftar"
                        format="DD/MM/YYYY"
                        value-format="YYYY-MM-DD"
                        style="width: 100%"
                    />
                </el-form-item>
                <el-form-item label="Tanggal Mulai" prop="tanggal_mulai">
                    <el-date-picker
                        v-model="trainingForm.tanggal_mulai"
                        type="date"
                        placeholder="Pilih tanggal mulai"
                        format="DD/MM/YYYY"
                        value-format="YYYY-MM-DD"
                        style="width: 100%"
                    />
                </el-form-item>
                <el-form-item label="Tanggal Selesai" prop="tanggal_selesai">
                    <el-date-picker
                        v-model="trainingForm.tanggal_selesai"
                        type="date"
                        placeholder="Pilih tanggal selesai"
                        format="DD/MM/YYYY"
                        value-format="YYYY-MM-DD"
                        style="width: 100%"
                    />
                </el-form-item>
                <el-form-item label="Status" prop="status">
                    <el-select v-model="trainingForm.status" placeholder="Pilih status" style="width: 100%">
                        <el-option label="Terdaftar" value="terdaftar" />
                        <el-option label="Sedang Pelatihan" value="sedang_pelatihan" />
                        <el-option label="Selesai" value="selesai" />
                        <el-option label="Mengundurkan Diri" value="mengundurkan_diri" />
                    </el-select>
                </el-form-item>
                <el-form-item label="Nilai Akhir" prop="nilai_akhir">
                    <el-input-number
                        v-model="trainingForm.nilai_akhir"
                        :min="0"
                        :max="100"
                        :step="1"
                        :precision="0"
                        placeholder="Nilai 0-100"
                        style="width: 100%"
                    />
                </el-form-item>
                <el-form-item label="Sertifikat">
                    <el-checkbox v-model="trainingForm.sertifikat_diterbitkan">
                        Sertifikat sudah diterbitkan
                    </el-checkbox>
                </el-form-item>
                <el-form-item label="No. Sertifikat" v-if="trainingForm.sertifikat_diterbitkan">
                    <el-input v-model="trainingForm.nomor_sertifikat" placeholder="Nomor sertifikat" />
                </el-form-item>
                <el-form-item label="Catatan">
                    <el-input
                        v-model="trainingForm.catatan"
                        type="textarea"
                        :rows="3"
                        placeholder="Catatan tambahan (opsional)"
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showTrainingModal = false">Batal</el-button>
                    <el-button type="success" @click="submitTrainingForm" :loading="trainingLoading">
                        Daftarkan
                    </el-button>
                </span>
            </template>
        </el-dialog>
        
        <!-- Hasil Interview -->
        <el-dialog
            v-model="modalHasil"
            title="Konfirmasi Selesai"
            width="800">
            <el-form label-position="top" :model="formHasilInterview" label-width="100%"  :rules="hasilInterviewRules" ref="hasilInterviewFormRef">
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="Skor Interview" prop="skor_interview">
                            <el-input v-model="formHasilInterview.skor_interview" type="number" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Skor Psikotes" prop="skor_psikotes">
                            <el-input v-model="formHasilInterview.skor_psikotes" type="number" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Kemampuan Komunikasi" prop="kemampuan_komunikasi">
                            <el-select v-model="formHasilInterview.kemampuan_komunikasi" placeholder="Pilih">
                                <el-option value="kurang" label="Kurang" />
                                <el-option value="cukup" label="Cukup" />
                                <el-option value="baik" label="Baik" />
                                <el-option value="sangat_baik" label="Sangat Baik" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Kemampuan Teknis" prop="kemampuan_teknis">
                            <el-select v-model="formHasilInterview.kemampuan_teknis" placeholder="Pilih">
                                <el-option value="kurang" label="Kurang" />
                                <el-option value="cukup" label="Cukup" />
                                <el-option value="baik" label="Baik" />
                                <el-option value="sangat_baik" label="Sangat Baik" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Rekomendasi" prop="rekomendasi">
                            <el-select v-model="formHasilInterview.rekomendasi" placeholder="Pilih">
                                <el-option value="tidak_direkomendasikan" label="Tidak Direkomendasikan" />
                                <el-option value="bersyarat" label="Bersyarat" />
                                <el-option value="direkomendasikan" label="Direkomendasikan" />
                                <el-option value="sangat_direkomendasikan" label="Sangat Direkomendasikan" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Penilaian Kepribadian" prop="kepribadian">
                            <el-input v-model="formHasilInterview.kepribadian" type="textarea" :rows="4"/>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Catatan" prop="catatan">
                            <el-input v-model="formHasilInterview.catatan" type="textarea" :rows="4"/>
                        </el-form-item>
                    </el-col>
                </el-row>
                
                <div class="text-end">
                    <el-button @click="dialogVisible = false">Cancel</el-button>
                    <el-button type="success" @click="submitHasilInterview" :loading="hasilInterivewLoading">
                        Simpan
                    </el-button>
                </div>
            </el-form>
        </el-dialog>
    </base-layout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { ElMessage, ElMessageBox, ElLoading } from 'element-plus'
import { UploadFilled } from '@element-plus/icons-vue'
import SelectStaff from '@/Components/SelectStaff.vue';
import axios from 'axios'

const activeTab = ref('applicant')

// Modal visibility
const showInterviewModal = ref(false)
const showMedicalModal = ref(false)
const showTrainingModal = ref(false)

// Loading states
const interviewLoading = ref(false)
const medicalLoading = ref(false)
const trainingLoading = ref(false)

// Form refs
const interviewFormRef = ref()
const medicalFormRef = ref()
const trainingFormRef = ref()

// Interview Form
const interviewForm = reactive({
    lamaran_id: null,
    tanggal: '',
    lokasi: '',
    pewawancara_id: '',
    status: 'dijadwalkan',
    catatan: ''
})

const interviewRules = {
    tanggal: [{ required: true, message: 'Tanggal interview wajib diisi', trigger: 'blur' }],
    lokasi: [{ required: true, message: 'Lokasi interview wajib diisi', trigger: 'blur' }],
    pewawancara_id: [{ required: true, message: 'Pewawancara wajib dipilih', trigger: 'change' }],
    status: [{ required: true, message: 'Status wajib dipilih', trigger: 'change' }]
}

// Medical Form
const medicalForm = reactive({
    user_id: null,
    tanggal: '',
    nama: '',
    hasil: 'belum dikonfirmasi',
    file: null,
    fileName: '',
    catatan: '',
    status: 'pending'
})

const medicalRules = {
    tanggal: [{ required: true, message: 'Tanggal medical wajib diisi', trigger: 'blur' }],
    nama: [{ required: true, message: 'Nama medical wajib diisi', trigger: 'blur' }],
    file: [{ required: true, message: 'File wajib diupload', trigger: 'change' }],
    hasil: [{ required: true, message: 'Hasil wajib diisi', trigger: 'blur' }],
    status: [{ required: true, message: 'Status wajib dipilih', trigger: 'change' }]
}

// Training Form
const trainingForm = reactive({
    user_id: null,
    lamaran_id: null,
    program_id: '',
    tanggal_daftar: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    status: 'terdaftar',
    nilai_akhir: null,
    sertifikat_diterbitkan: false,
    nomor_sertifikat: '',
    catatan: '',
    didaftarkan_oleh: null
});


const hasilInterviewRules = {
    skor_interview: [{ required: true, message: 'Penilaian Interview harus diisi', trigger: 'blur' }],
    skor_psikotes: [{ required: true, message: 'Penilaian Psikotes harus diisi', trigger: 'blur' }],
    kemampuan_komunikasi: [{ required: true, message: 'Kemampuan Komunikasi harus diisi', trigger: 'change' }],
    kemampuan_teknis: [{ required: true, message: 'Kemampuan Teknis harus diisi', trigger: 'change' }],
    kepribadian: [{ required: true, message: 'Kepribadian harus diisi', trigger: 'blur' }]
}

const formHasilInterview = reactive({
    user_id: null,
    lamaran_id: null,
    jadwal_id: null,
    skor_interview: null,
    skor_psikotes: null,
    kemampuan_komunikasi: null,
    kemampuan_teknis: null,
    kepribadian: null,
    rekomendasi: null,
    catatan: null,
});

const trainingRules = {
    program_id: [{ required: true, message: 'Program pelatihan wajib dipilih', trigger: 'change' }],
    tanggal_daftar: [{ required: true, message: 'Tanggal daftar wajib diisi', trigger: 'blur' }],
    tanggal_mulai: [{ required: true, message: 'Tanggal mulai wajib diisi', trigger: 'blur' }],
    tanggal_selesai: [{ required: true, message: 'Tanggal selesai wajib diisi', trigger: 'blur' }],
    status: [{ required: true, message: 'Status wajib dipilih', trigger: 'change' }]
}

const props = defineProps({
    lamaran: Object,
    completionStats: Object,
    statusOptions: Object,
    requiredDocuments: Array,
    adminList: Array,
    trainingPrograms: Array
})

// Status helpers
const statusLabels = {
    'pending': 'Menunggu Review',
    'diterima': 'Diterima', 
    'ditolak': 'Ditolak',
    'interview': 'Tahap Interview',
    'medical': 'Medical Check Up',
    'pelatihan': 'Pelatihan',
    'siap': 'Siap Berangkat',
    'selesai': 'Selesai'
}

const statusTypes = {
    'pending': 'warning',
    'diterima': 'success',
    'ditolak': 'danger',
    'interview': 'primary',
    'medical': 'info',
    'pelatihan': '',
    'siap': 'success',
    'selesai': 'success'
}

const getStatusLabel = (status) => statusLabels[status] || status
const getStatusType = (status) => statusTypes[status] || ''

const getProgressStep = (status) => {
    const steps = {
        'pending': 1,
        'diterima': 2,
        'interview': 3,
        'medical': 4,
        'pelatihan': 5,
        'siap': 6,
        'selesai': 7,
        'ditolak': 0
    }
    return steps[status] || 1
}

// Document helpers
const getDocumentStatusLabel = (status) => {
    const labels = {
        'pending': 'Menunggu Review',
        'approved': 'Disetujui',
        'rejected': 'Ditolak'
    }
    return labels[status] || status
}

const getDocumentStatusType = (status) => {
    const types = {
        'pending': 'warning',
        'approved': 'success',
        'rejected': 'danger'
    }
    return types[status] || ''
}

const getDocumentCompletionPercentage = () => {
    if (!props.completionStats?.documents) return 0
    const { completed, total } = props.completionStats.documents
    return total > 0 ? Math.round((completed / total) * 100) : 0
}

// Training helpers
const getTrainingStatusType = (status) => {
    const types = {
        'terdaftar': 'info',
        'sedang_pelatihan': 'warning',
        'selesai': 'success',
        'mengundurkan_diri': 'danger'
    }
    return types[status] || 'info'
}

const getTrainingStatusLabel = (status) => {
    const labels = {
        'terdaftar': 'Terdaftar',
        'sedang_pelatihan': 'Sedang Pelatihan',
        'selesai': 'Selesai',
        'mengundurkan_diri': 'Mengundurkan Diri'
    }
    return labels[status] || status
}

// Format helpers
const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })
}

const formatCurrency = (amount) => {
    if (!amount) return '-'
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount)
}

// File upload handler
const handleMedicalFileUpload = (file) => {
    // Validate file size (2MB = 2 * 1024 * 1024 bytes)
    if (file.size > 2 * 1024 * 1024) {
        ElMessage.error('File size cannot exceed 2MB!')
        return false
    }

    // Validate file type
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']
    if (!allowedTypes.includes(file.type)) {
        ElMessage.error('Only PDF, JPG, and PNG files are allowed!')
        return false
    }

    medicalForm.file = file
    medicalForm.fileName = file.name
    
    return false // Prevent auto upload
}

// Modal handlers
const handleInterviewModalClose = (done) => {
    interviewFormRef.value?.resetFields()
    done()
}

const handleMedicalModalClose = (done) => {
    medicalFormRef.value?.resetFields()
    medicalForm.file = null
    medicalForm.fileName = ''
    done()
}

const handleTrainingModalClose = (done) => {
    trainingFormRef.value?.resetFields()
    done()
}

// Form submissions
const submitInterviewForm = async () => {
    if (!interviewFormRef.value) return
    
    await interviewFormRef.value.validate((valid) => {
        if (valid) {
            scheduleInterview()
        }
    })
}

const submitMedicalForm = async () => {
    if (!medicalFormRef.value) return
    
    await medicalFormRef.value.validate((valid) => {
        if (valid) {
            scheduleMedical()
        }
    })
}

const submitTrainingForm = async () => {
    if (!trainingFormRef.value) return
    
    await trainingFormRef.value.validate((valid) => {
        if (valid) {
            scheduleTraining()
        }
    })
}

// API calls
const scheduleInterview = async () => {
    interviewLoading.value = true
    
    try {
        interviewForm.lamaran_id = props.lamaran.id
        
        await axios.post(route('admin.interview.jadwal.store'), interviewForm)
        
        ElMessage.success('Interview berhasil dijadwalkan')
        showInterviewModal.value = false
        
        // Refresh page data
        router.visit(route('admin.lamaran.show', props.lamaran.id), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error scheduling interview:', error)
        ElMessage.error('Gagal menjadwalkan interview')
    } finally {
        interviewLoading.value = false
    }
}

const scheduleMedical = async () => {
    medicalLoading.value = true
    
    try {
        medicalForm.lamaran_id = props.lamaran.id
        
        await axios.post(route('admin.medical.store'), medicalForm)
        
        ElMessage.success('Medical check up berhasil dijadwalkan')
        showMedicalModal.value = false
        
        // Refresh page data
        router.visit(route('admin.lamaran.show', props.lamaran.id), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error scheduling medical:', error)
        ElMessage.error('Gagal menjadwalkan medical check up')
    } finally {
        medicalLoading.value = false
    }
}

const scheduleTraining = async () => {
    trainingLoading.value = true
    
    try {
        trainingForm.lamaran_id = props.lamaran.id
        
        await axios.post(route('admin.training.store'), trainingForm)
        
        ElMessage.success('Pelatihan berhasil didaftarkan')
        showTrainingModal.value = false
        
        // Refresh page data
        router.visit(route('admin.lamaran.show', props.lamaran.id), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error scheduling training:', error)
        ElMessage.error('Gagal mendaftarkan pelatihan')
    } finally {
        trainingLoading.value = false
    }
}

// Status update
const updateStatus = async (status) => {
    let catatan = null
    
    if (status === 'ditolak') {
        try {
            const { value } = await ElMessageBox.prompt('Alasan penolakan:', 'Tolak Lamaran', {
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal',
                inputType: 'textarea'
            })
            catatan = value
        } catch {
            return
        }
    } else {
        try {
            await ElMessageBox.confirm(
                `Ubah status lamaran menjadi "${statusLabels[status]}"?`,
                'Konfirmasi',
                {
                    confirmButtonText: 'Ya, Ubah',
                    cancelButtonText: 'Batal',
                    type: 'warning'
                }
            )
        } catch {
            return
        }
    }

    const loading = ElLoading.service({ lock: true, text: 'Memperbarui status...' })
    
    try {
        await axios.post(route('admin.lamaran.update-status', props.lamaran.id), {
            status,
            catatan
        })
        
        ElMessage.success('Status berhasil diperbarui')
        router.visit(route('admin.lamaran.show', props.lamaran.id), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error updating status:', error)
        ElMessage.error('Gagal memperbarui status')
    } finally {
        loading.close()
    }
}
</script>

<style scoped>
.space-x-1 > * + * {
    margin-left: 0.25rem;
}

.percentage-value {
    font-size: 16px;
    font-weight: bold;
}

.gap-1 {
    gap: 0.25rem;
}
</style>