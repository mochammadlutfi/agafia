<template>
    <base-layout title="Detail Talent">
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Detail Talent</span>
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
                </div>
            </div>

            <!-- Profile Information -->
            <el-card class="mb-4">
                <div class="border-bottom border-2 mb-4">
                    <h3 class="fs-5 mb-2">1. Informasi Pribadi</h3>
                </div>
                <el-row justify="space-between">
                    <el-col :span="18">
                        <el-descriptions column="2" label-width="180px" :border="true" class="mb-4">
                            <el-descriptions-item label="NIK">
                                {{ talent?.detail?.nik || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Nama Lengkap">
                                {{ talent?.detail?.nama || talent?.nama || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Jenis Kelamin">
                                {{ talent?.detail?.jenis_kelamin || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Agama">
                                {{ talent?.detail?.agama || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Tempat Lahir">
                                {{ talent?.detail?.tempat_lahir || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Tanggal Lahir">
                                {{ format_date(talent?.detail?.tanggal_lahir) || '-' }} 
                                <span v-if="talent?.detail?.tanggal_lahir" class="text-muted">({{ calculateAge(talent.detail.tanggal_lahir) }} tahun)</span>
                            </el-descriptions-item>
                            <el-descriptions-item label="Status Perkawinan">
                                {{ formatStatusPerkawinan(talent?.detail?.status_perkawinan) || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Golongan Darah">
                                {{ talent?.detail?.golongan_darah || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Tinggi / Berat Badan">
                                <span v-if="talent?.detail?.tinggi_badan || talent?.detail?.berat_badan">
                                    {{ talent?.detail?.tinggi_badan || '-' }} cm / {{ talent?.detail?.berat_badan || '-' }} kg
                                </span>
                                <span v-else>-</span>
                            </el-descriptions-item>
                            <el-descriptions-item label="No Handphone">
                                <el-link v-if="talent?.detail?.phone" type="primary" :href="`tel:${talent.detail.phone}`">
                                    {{ talent.detail.phone }}
                                </el-link>
                                <span v-else>-</span>
                            </el-descriptions-item>
                            <el-descriptions-item label="Email">
                                <el-link v-if="talent?.email" type="primary" :href="`mailto:${talent.email}`">
                                    {{ talent.email }}
                                </el-link>
                                <span v-else>-</span>
                            </el-descriptions-item>
                            <el-descriptions-item label="Email Alternatif">
                                {{ talent?.detail?.email_alternatif || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Alamat KTP" span="2">
                                {{ talent?.detail?.alamat || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Alamat Domisili" span="2" v-if="talent?.detail?.alamat_domisili">
                                {{ talent.detail.alamat_domisili }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Kecamatan">
                                {{ talent?.detail?.kecamatan || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Kab/Kota">
                                {{ talent?.detail?.kabupaten_kota || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Provinsi">
                                {{ talent?.detail?.provinsi || '-' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Kode Pos">
                                {{ talent?.detail?.kode_pos || '-' }}
                            </el-descriptions-item>
                        </el-descriptions>
                    </el-col>
                    <el-col :span="5">
                        <div class="text-center">
                            <el-image
                                style="width: 180px; height: 240px;"
                                :src="talent?.detail?.foto ? talent.detail.foto : '/images/placeholder.png'"
                                fit="cover"
                                :preview-src-list="[talent?.detail?.foto ? talent.detail.foto : '/images/placeholder.png']"
                            />
                            <p class="mt-2 text-muted small">Foto Profil</p>
                        </div>
                    </el-col>
                </el-row>

                <div class="border-bottom border-2 mb-4">
                    <h3 class="fs-5 mb-2">2. Kontak Darurat & Keluarga</h3>
                </div>
                <el-descriptions column="1" label-width="180px" :border="true" class="mb-4">
                    <el-descriptions-item label="Nama Kontak Darurat">
                        {{ talent?.detail?.kontak_darurat_nama || '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Nomor Telepon">
                        <el-link v-if="talent?.detail?.kontak_darurat_phone" type="primary" :href="`tel:${talent.detail.kontak_darurat_phone}`">
                            {{ talent.detail.kontak_darurat_phone }}
                        </el-link>
                        <span v-else>-</span>
                    </el-descriptions-item>
                    <el-descriptions-item label="Hubungan">
                        {{ talent?.detail?.kontak_darurat_hubungan || '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Alamat">
                        {{ talent?.detail?.kontak_darurat_alamat || '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Nama Ayah">
                        {{ talent?.detail?.nama_ayah || '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Nama Ibu">
                        {{ talent?.detail?.nama_ibu || '-' }}
                    </el-descriptions-item>
                </el-descriptions>

                <div class="border-bottom border-2 mb-4">
                    <h3 class="fs-5 mb-2">3. Pekerjaan yang Diinginkan</h3>
                </div>
                <el-descriptions column="1" label-width="180px" :border="true" class="mb-4">
                    <el-descriptions-item label="Pekerjaan Diinginkan">
                        {{ talent?.detail?.pekerjaan || '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Negara Tujuan">
                        {{ talent?.detail?.negara_tujuan || '-' }}
                    </el-descriptions-item>
                </el-descriptions>
            </el-card>

            <!-- Applications List -->
            <el-card>
                <div class="border-bottom border-2 mb-4">
                    <h3 class="fs-5 mb-2">4. Riwayat Lamaran</h3>
                </div>
                <div v-if="talent?.lamaran && talent.lamaran.length > 0">
                    <el-table :data="talent.lamaran" border class="w-100" header-cell-class-name="bg-body text-dark">
                        <el-table-column prop="created_at" label="Tanggal Lamar" width="120">
                            <template #default="{ row }">
                                {{ format_date(row.created_at) }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="lowongan.posisi" label="Posisi" />
                        <el-table-column prop="lowongan.perusahaan" label="Perusahaan" />
                        <el-table-column prop="lowongan.lokasi" label="Lokasi"/>
                        <el-table-column prop="status" label="Status" width="150">
                            <template #default="{ row }">
                                <el-tag :type="getStatusType(row.status)" size="small">
                                    {{ getStatusLabel(row.status) }}
                                </el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="Aksi" width="100">
                            <template #default="{ row }">
                                <el-button :tag="Link" :href="route('admin.lamaran.show', row.id)" type="primary" size="small">
                                    Detail
                                </el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>
                <div v-else class="text-center py-4">
                    <el-empty description="Belum ada lamaran yang diajukan" />
                </div>
            </el-card>
        </div>
    </base-layout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import moment from 'moment';

const props = defineProps({
    talent: {
        type: Object,
        default: () => ({})
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

const format_date = (value) => {
    if (value) {
        moment().locale('id');
        return moment(String(value)).format('DD MMMM YYYY')
    }
    return null;
}

const formatStatusPerkawinan = (status) => {
    const statusMap = {
        'lajang': 'Lajang',
        'menikah': 'Menikah',
        'cerai': 'Cerai',
        'janda': 'Janda',
        'duda': 'Duda'
    }
    return statusMap[status] || status
}

const calculateAge = (birthDate) => {
    if (!birthDate) return '-';
    return moment().diff(moment(birthDate), 'years');
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
</script>

<style scoped>
.border-bottom {
    border-bottom: 2px solid #e4e7ed !important;
    padding-bottom: 8px;
    margin-bottom: 20px;
}

.text-primary {
    color: #409eff !important;
    font-weight: 600;
}

.text-muted {
    color: #909399 !important;
}

.small {
    font-size: 0.875rem;
}

:deep(.el-descriptions__label) {
    font-weight: 600;
    color: #606266;
}

:deep(.el-descriptions__content) {
    color: #303133;
}

:deep(.el-card) {
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    :deep(.el-col) {
        margin-bottom: 16px;
    }
    
    :deep(.el-descriptions) {
        font-size: 14px;
    }
}
</style>