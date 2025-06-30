<template>
    <user-layout>
        <div class="align-middle border-2 border-bottom border-primary d-flex justify-content-between mb-4 py-2">
            <span class="fw-bold fs-lg">Profil</span>
            <div>
                <el-button :tag="Link" :href="route('user.biodata.edit')" type="primary">
                    <i class="fa fa-edit me-2"></i>
                    Edit Profil
                </el-button>
                <el-button v-if="isProfileComplete" type="success" tag="a" target="_blank" :href="route('user.biodata.pdf')">
                    <i class="fa fa-download me-2"></i>
                    Download CV
                </el-button>
            </div>
        </div>
        
        <el-card>
            <div class="mb-3" v-if="isProfileComplete">
                <el-alert
                    title="Profil Lengkap"
                    type="success"
                    :description="`Data profil Anda sudah ${completionPercentage}% lengkap`"
                    show-icon
                    :closable="false"
                />
            </div>
            <div class="mb-3" v-else>
                <el-alert
                    title="Profil Belum Lengkap"
                    type="warning"
                    :description="`Data profil Anda baru ${completionPercentage}% lengkap. Lengkapi data untuk meningkatkan peluang diterima kerja.`"
                    show-icon
                    :closable="false"
                />
            </div>
                
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
                    <el-descriptions column="2" label-width="180px" :border="true" class="mb-4">
                        <el-descriptions-item label="NIK">
                            {{ data.detail.nik || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Nama Lengkap">
                            {{ data.detail.nama || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Jenis Kelamin">
                            {{ data.detail.jenis_kelamin || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Agama">
                            {{ data.detail.agama || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Tempat Lahir">
                            {{ data.detail.tempat_lahir || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Tanggal Lahir">
                            {{ format_date(data.detail.tanggal_lahir) || '-' }} 
                            <span v-if="data.detail.age" class="text-muted">({{ data.detail.age }} tahun)</span>
                        </el-descriptions-item>
                        <el-descriptions-item label="Status Perkawinan">
                            {{ formatStatusPerkawinan(data.detail.status_perkawinan) || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Golongan Darah">
                            {{ data.detail.golongan_darah || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Tinggi / Berat Badan">
                            <span v-if="data.detail.tinggi_badan || data.detail.berat_badan">
                                {{ data.detail.tinggi_badan || '-' }} cm / {{ data.detail.berat_badan || '-' }} kg
                            </span>
                            <span v-else>-</span>
                        </el-descriptions-item>
                        <el-descriptions-item label="No Handphone">
                            {{ data.detail.phone || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Email Alternatif">
                            {{ data.detail.email_alternatif || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Alamat KTP" span="2">
                            {{ data.detail.alamat || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Alamat Domisili" span="2" v-if="data.detail.alamat_domisili">
                            {{ data.detail.alamat_domisili }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Kecamatan">
                            {{ data.detail.kecamatan || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Kab/Kota">
                            {{ data.detail.kabupaten_kota || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Provinsi">
                            {{ data.detail.provinsi || '-' }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Kode Pos">
                            {{ data.detail.kode_pos || '-' }}
                        </el-descriptions-item>
                    </el-descriptions>
                </el-col>
                <el-col :span="5">
                    <div class="text-center">
                        <el-image
                            style="width: 180px; height: 240px;"
                            :src="data.detail.foto ? data.detail.foto : '/images/placeholder.png'"
                            fit="cover"
                            :preview-src-list="[data.detail.foto ? data.detail.foto : '/images/placeholder.png']"
                        />
                        <p class="mt-2 text-muted small">Foto Profil</p>
                    </div>
                </el-col>
            </el-row>
            <div class="border-bottom border-2 mb-4">
                <h3 class="fs-5 mb-2">2. Kontak Darurat & Keluarga</h3>
            </div>
            <el-descriptions column="1" label-width="180px" :border="true" class="mb-4">
                <el-descriptions-item label="Nama">
                    {{ data.detail.kontak_darurat_nama || '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Nomor Telepon">
                    {{ data.detail.kontak_darurat_phone || '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Hubungan">
                    {{ data.detail.kontak_darurat_hubungan || '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="Alamat">
                    {{ data.detail.kontak_darurat_alamat || '-' }}
                </el-descriptions-item>
            </el-descriptions>
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
                <h3 class="fs-5 mb-2">4. Pengalaman Kerja & Profesional</h3>
            </div>
            <div class="mb-4">
                <h4 class="fs-6 mb-3 text-primary">Riwayat Pekerjaan</h4>
                <el-table :data="data.detail.pengalaman" border class="w-100" header-cell-class-name="bg-body text-dark">
                    <el-table-column prop="nama" label="Nama Perusahaan" />
                    <el-table-column prop="posisi" label="Posisi" />
                    <el-table-column prop="tahun_masuk" label="Tahun Masuk" width="150" />
                    <el-table-column prop="tahun_keluar" label="Tahun Keluar" width="150" />
                </el-table>
            </div>
            
            <div class="mb-4" v-if="data.detail.objektif_karir || data.detail.ringkasan_profil">
                <h4 class="fs-6 mb-3 text-primary">Profil Profesional</h4>
                <el-descriptions column="1" label-width="180px" :border="true" class="mb-4">
                    <el-descriptions-item label="Objektif Karir" v-if="data.detail.objektif_karir">
                        {{ data.detail.objektif_karir }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Ringkasan Profil" v-if="data.detail.ringkasan_profil">
                        {{ data.detail.ringkasan_profil }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Pekerjaan Diinginkan">
                        {{ data.detail.pekerjaan || '-' }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Negara Tujuan">
                        {{ data.detail.negara_tujuan || '-' }}
                    </el-descriptions-item>
                </el-descriptions>
            </div>
            
            <div class="mb-4" v-if="data.detail.keahlian_khusus || data.detail.hobi || data.detail.motto_hidup">
                <h4 class="fs-6 mb-3 text-primary">Keahlian & Kepribadian</h4>
                <el-descriptions column="1" label-width="180px" :border="true" class="mb-4">
                    <el-descriptions-item label="Keahlian Khusus" v-if="data.detail.keahlian_khusus">
                        {{ data.detail.keahlian_khusus }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Hobi & Minat" v-if="data.detail.hobi">
                        {{ data.detail.hobi }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Motto Hidup" v-if="data.detail.motto_hidup">
                        {{ data.detail.motto_hidup }}
                    </el-descriptions-item>
                </el-descriptions>
            </div>
            
            <div class="mb-4" v-if="data.detail.kondisi_kesehatan || data.detail.medical_checkup_terakhir">
                <h4 class="fs-6 mb-3 text-primary">Informasi Kesehatan</h4>
                <el-descriptions column="1" label-width="180px" :border="true" class="mb-4">
                    <el-descriptions-item label="Kondisi Kesehatan" v-if="data.detail.kondisi_kesehatan">
                        {{ data.detail.kondisi_kesehatan }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Medical Check Up Terakhir" v-if="data.detail.medical_checkup_terakhir">
                        {{ format_date(data.detail.medical_checkup_terakhir) }}
                    </el-descriptions-item>
                </el-descriptions>
            </div>
        </el-card>
    </user-layout>
</template>


<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
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

const isProfileComplete = computed(() => {
    return completionPercentage.value >= 80
})

const completionPercentage = computed(() => {
    if (!props.data.detail) return 0
    
    const requiredFields = [
        'nik', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
        'phone', 'alamat', 'pendidikan', 'pengalaman', 'pekerjaan', 'negara_tujuan'
    ]
    
    const optionalFields = [
        'agama', 'status_perkawinan', 'tinggi_badan', 'berat_badan', 'golongan_darah',
        'email_alternatif', 'alamat_domisili', 'kontak_darurat_nama', 'objektif_karir',
        'ringkasan_profil', 'keahlian_khusus', 'hobi', 'motto_hidup'
    ]
    
    let completed = 0
    let total = requiredFields.length + optionalFields.length
    
    // Check required fields (weight: 2)
    requiredFields.forEach(field => {
        if (props.data.detail[field] && props.data.detail[field] !== '') {
            completed += 2
        }
        total += 1 // Additional weight for required fields
    })
    
    // Check optional fields (weight: 1)
    optionalFields.forEach(field => {
        if (props.data.detail[field] && props.data.detail[field] !== '') {
            completed += 1
        }
    })
    
    return Math.round((completed / total) * 100)
})

const generateCV = () => {
    // TODO: Implement CV generation logic
    ElMessage({
        type: 'info',
        message: 'Fitur download CV akan segera tersedia!'
    })
}

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

:deep(.el-alert) {
    border-radius: 8px;
}

:deep(.el-button--large) {
    padding: 12px 20px;
    font-size: 16px;
}

.profile-photo {
    border-radius: 8px;
    border: 2px solid #e4e7ed;
}

.completion-badge {
    display: inline-block;
    padding: 4px 12px;
    background: #f0f9ff;
    color: #409eff;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 600;
    margin-left: 8px;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.section-title {
    color: #303133;
    font-weight: 600;
    margin: 0;
}

@media (max-width: 768px) {
    :deep(.el-col) {
        margin-bottom: 16px;
    }
    
    :deep(.el-descriptions) {
        font-size: 14px;
    }
    
    .profile-photo {
        width: 150px;
        height: 200px;
    }
}
</style>