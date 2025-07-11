<template>
    <base-layout title="Kelola Lamaran">
        <div class="content">
            <!-- Page Header -->
            <div class="content-heading d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-0">Kelola Lamaran</h2>
                    <small class="text-muted">Kelola dan pantau semua lamaran pekerjaan</small>
                </div>
                <div class="space-x-1">
                    <el-button type="success" @click="exportData" :loading="exportLoading">
                        <i class="fa fa-download me-1"></i>
                        Export
                    </el-button>
                </div>
            </div>

            <!-- Filters -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-filter me-2"></i>
                        Filter & Pencarian
                    </h3>
                </div>
                <div class="block-content pb-4">
                    <el-row :gutter="16">
                        <el-col :span="6">
                            <el-input
                                v-model="filters.q"
                                placeholder="Cari nama, email, NIK..."
                                @input="handleSearch"
                                clearable
                            >
                                <template #prefix>
                                    <el-icon><Search /></el-icon>
                                </template>
                            </el-input>
                        </el-col>
                        <el-col :span="5">
                            <el-select v-model="filters.status" placeholder="Status" @change="loadData" clearable>
                                <el-option
                                    v-for="(label, value) in statusOptions"
                                    :key="value"
                                    :label="label"
                                    :value="value"
                                />
                            </el-select>
                        </el-col>
                        <el-col :span="5">
                            <el-date-picker
                                v-model="filters.tanggal_dari"
                                type="date"
                                placeholder="Tanggal Dari"
                                @change="loadData"
                                style="width: 100%"
                            />
                        </el-col>
                        <el-col :span="5">
                            <el-date-picker
                                v-model="filters.tanggal_sampai"
                                type="date"
                                placeholder="Tanggal Sampai"
                                @change="loadData"
                                style="width: 100%"
                            />
                        </el-col>
                        <el-col :span="3">
                            <div class="d-flex gap-2">
                                <el-button @click="resetFilters" type="default">
                                    <i class="fa fa-refresh me-1"></i>
                                    Reset
                                </el-button>
                            </div>
                        </el-col>
                    </el-row>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="block block-rounded" v-if="selectedRows.length > 0">
                <div class="block-content">
                    <el-alert
                        :title="`${selectedRows.length} lamaran dipilih`"
                        type="info"
                        show-icon
                        :closable="false"
                    >
                        <template #default>
                            <div class="d-flex gap-2 mt-2">
                                <el-dropdown trigger="click">
                                    <el-button type="primary" size="small">
                                        Ubah Status Massal
                                        <el-icon class="el-icon--right"><arrow-down /></el-icon>
                                    </el-button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item
                                                v-for="(label, status) in statusOptions"
                                                :key="status"
                                                @click="bulkUpdateStatus(status)"
                                            >
                                                {{ label }}
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                                <el-button @click="selectedRows = []" size="small">
                                    Batal Pilih
                                </el-button>
                            </div>
                        </template>
                    </el-alert>
                </div>
            </div>

            <!-- Data Table -->
            <div class="block block-rounded">
                <div class="block-content p-0">
                    <el-table
                        v-loading="loading"
                        :data="tableData"
                        border
                        style="width: 100%"
                        @selection-change="handleSelectionChange"
                        @sort-change="handleSortChange"
                    >
                        <el-table-column type="selection" width="55" />
                        
                        <el-table-column prop="id" label="ID" width="80" sortable="custom" />
                        
                        <el-table-column prop="user.detail.nama" label="Nama Pelamar" min-width="180">
                            <template #default="{ row }">
                                <div>
                                    <div class="fw-medium">{{ row.user?.detail?.nama || row.user?.nama }}</div>
                                    <small class="text-muted">{{ row.user?.email }}</small>
                                </div>
                            </template>
                        </el-table-column>
                        
                        <el-table-column prop="lowongan.posisi" label="Posisi" min-width="160">
                            <template #default="{ row }">
                                <div>
                                    <div class="fw-medium">{{ row.lowongan?.posisi }}</div>
                                    <small class="text-muted">{{ row.lowongan?.perusahaan }}</small>
                                </div>
                            </template>
                        </el-table-column>
                                                
                        <el-table-column prop="tanggal_lamaran" label="Tanggal" width="120" sortable="custom">
                            <template #default="{ row }">
                                {{ formatDate(row.tanggal_lamaran) }}
                            </template>
                        </el-table-column>
                        
                        <el-table-column prop="status" label="Status" width="140">
                            <template #default="{ row }">
                                <el-tag :type="getStatusType(row.status)" size="small">
                                    {{ getStatusLabel(row.status) }}
                                </el-tag>
                            </template>
                        </el-table-column>
                        
                        <el-table-column label="Aksi" width="160" fixed="right">
                            <template #default="{ row }">
                                <div class="d-flex gap-1">
                                    <el-button
                                        :tag="Link"
                                        :href="route('admin.lamaran.show', row.id)"
                                        type="primary"
                                        size="small"
                                    >
                                        Detail
                                    </el-button>
                                    <el-dropdown trigger="click">
                                        <el-button type="warning" size="small">
                                            <i class="fa fa-cog"></i>
                                        </el-button>
                                        <template #dropdown>
                                            <el-dropdown-menu>
                                                <el-dropdown-item @click="updateSingleStatus(row.id, 'diterima')">
                                                    Terima
                                                </el-dropdown-item>
                                                <el-dropdown-item @click="updateSingleStatus(row.id, 'ditolak')">
                                                    Tolak
                                                </el-dropdown-item>
                                                <el-dropdown-item @click="updateSingleStatus(row.id, 'interview')">
                                                    Set Interview
                                                </el-dropdown-item>
                                                <el-dropdown-item divided @click="deleteApplication(row.id)" class="text-danger">
                                                    Hapus
                                                </el-dropdown-item>
                                            </el-dropdown-menu>
                                        </template>
                                    </el-dropdown>
                                </div>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>
                
                <!-- Pagination -->
                <div class="block-content p-2" v-if="pagination.total > 0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ pagination.from }} - {{ pagination.to }} dari {{ pagination.total }} data
                        </div>
                        <el-pagination
                            v-model:current-page="pagination.current_page"
                            :page-size="pagination.per_page"
                            :total="pagination.total"
                            @current-change="handlePageChange"
                            layout="prev, pager, next"
                            background
                        />
                    </div>
                </div>
            </div>
        </div>
    </base-layout>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { ElMessage, ElMessageBox, ElLoading } from 'element-plus'
import { Search, ArrowDown } from '@element-plus/icons-vue'
import axios from 'axios'
import { debounce } from 'lodash'

// Reactive data
const loading = ref(false)
const exportLoading = ref(false)
const tableData = ref([])
const selectedRows = ref([])

const filters = reactive({
    q: '',
    status: '',
    tanggal_dari: '',
    tanggal_sampai: ''
})

const pagination = reactive({
    current_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0
})

const sorting = reactive({
    sort: 'id',
    sortDir: 'desc'
})

// Status options
const statusOptions = {
    'pending': 'Menunggu Review',
    'diterima': 'Diterima',
    'ditolak': 'Ditolak',
    'interview': 'Tahap Interview',
    'medical': 'Medical Check Up',
    'pelatihan': 'Pelatihan',
    'siap': 'Siap Berangkat',
    'selesai': 'Selesai'
}

// Status helpers
const getStatusLabel = (status) => statusOptions[status] || status
const getStatusType = (status) => {
    const types = {
        'pending': 'warning',
        'diterima': 'success',
        'ditolak': 'danger',
        'interview': 'primary',
        'medical': 'info',
        'pelatihan': '',
        'siap': 'success',
        'selesai': 'success'
    }
    return types[status] || ''
}

// Format date helper
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })
}

// Data loading
const loadData = async () => {
    loading.value = true
    try {
        const params = {
            page: pagination.current_page,
            limit: pagination.per_page,
            sort: sorting.sort,
            sortDir: sorting.sortDir,
            ...filters
        }

        const response = await axios.get(route('admin.lamaran.data'), { params })
        
        tableData.value = response.data.data
        pagination.current_page = response.data.current_page
        pagination.per_page = response.data.per_page
        pagination.total = response.data.total
        pagination.from = response.data.from
        pagination.to = response.data.to
    } catch (error) {
        console.error('Error loading data:', error)
        ElMessage.error('Gagal memuat data')
    } finally {
        loading.value = false
    }
}

// Search handler with debounce
const handleSearch = debounce(() => {
    pagination.current_page = 1
    loadData()
}, 500)

// Event handlers
const handleSelectionChange = (selection) => {
    selectedRows.value = selection
}

const handleSortChange = ({ prop, order }) => {
    sorting.sort = prop
    sorting.sortDir = order === 'ascending' ? 'asc' : 'desc'
    loadData()
}

const handlePageChange = (page) => {
    pagination.current_page = page
    loadData()
}

const resetFilters = () => {
    Object.keys(filters).forEach(key => {
        filters[key] = ''
    })
    pagination.current_page = 1
    loadData()
}

// Status update functions
const updateSingleStatus = async (id, status) => {
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
    }

    const loading = ElLoading.service({ lock: true, text: 'Memperbarui status...' })
    
    try {
        await axios.post(route('admin.lamaran.update-status', id), {
            status,
            catatan
        })
        
        ElMessage.success('Status berhasil diperbarui')
        loadData()
    } catch (error) {
        console.error('Error updating status:', error)
        ElMessage.error('Gagal memperbarui status')
    } finally {
        loading.close()
    }
}

const bulkUpdateStatus = async (status) => {
    if (selectedRows.value.length === 0) return

    try {
        await ElMessageBox.confirm(
            `Ubah status ${selectedRows.value.length} lamaran menjadi "${statusOptions[status]}"?`,
            'Konfirmasi',
            {
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal',
                type: 'warning'
            }
        )

        const loading = ElLoading.service({ lock: true, text: 'Memperbarui status...' })
        
        try {
            await axios.post(route('admin.lamaran.bulk-update-status'), {
                ids: selectedRows.value.map(row => row.id),
                status
            })
            
            ElMessage.success(`${selectedRows.value.length} lamaran berhasil diperbarui`)
            selectedRows.value = []
            loadData()
        } catch (error) {
            console.error('Error bulk updating:', error)
            ElMessage.error('Gagal memperbarui status')
        } finally {
            loading.close()
        }
    } catch {
        // User cancelled
    }
}

const deleteApplication = async (id) => {
    try {
        await ElMessageBox.confirm(
            'Hapus lamaran ini? Tindakan ini tidak dapat dibatalkan.',
            'Konfirmasi Hapus',
            {
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                type: 'error'
            }
        )

        const loading = ElLoading.service({ lock: true, text: 'Menghapus...' })
        
        try {
            await axios.delete(route('admin.lamaran.destroy', id))
            ElMessage.success('Lamaran berhasil dihapus')
            loadData()
        } catch (error) {
            console.error('Error deleting:', error)
            ElMessage.error('Gagal menghapus lamaran')
        } finally {
            loading.close()
        }
    } catch {
        // User cancelled
    }
}

const exportData = async () => {
    exportLoading.value = true
    try {
        const params = { ...filters }
        window.open(route('admin.lamaran.export', params))
        ElMessage.success('Export dimulai, file akan didownload secara otomatis')
    } catch (error) {
        console.error('Error exporting:', error)
        ElMessage.error('Gagal mengexport data')
    } finally {
        exportLoading.value = false
    }
}

const loadStatistics = async () => {
    try {
        const response = await axios.get(route('admin.lamaran.statistics'))
        const stats = response.data.stats
        
        let message = 'Statistik Lamaran:\n'
        Object.entries(statusOptions).forEach(([key, label]) => {
            message += `${label}: ${stats[key] || 0}\n`
        })
        
        ElMessageBox.alert(message, 'Statistik', {
            confirmButtonText: 'OK'
        })
    } catch (error) {
        console.error('Error loading statistics:', error)
        ElMessage.error('Gagal memuat statistik')
    }
}

// Initialize
onMounted(() => {
    loadData()
})
</script>

<style scoped>
.space-x-1 > * + * {
    margin-left: 0.25rem;
}

.gap-1 {
    gap: 0.25rem;
}

.gap-2 {
    gap: 0.5rem;
}
</style>