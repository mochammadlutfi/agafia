<template>
    <base-layout>
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Medical Checkup</span>
                <div class="space-x-1">
                    <el-button type="primary" @click="openAddModal" v-if="['owner', 'admin'].includes($page.props.user.level)">
                        <i class="fa fa-plus me-2"></i>
                        Tambah Medical Checkup
                    </el-button>
                </div>
            </div>
            <div class="block rounded bordered" v-loading="isLoading" >
                <div class="block-content py-3">
                    <el-row justify="space-between">
                        <el-col :span="12">
                            <el-select v-model="limit" placeholder="Pilih" style="width: 110px" @change="fetchData(1)">
                                <el-option label="25" value="25"/>
                                <el-option label="50" value="50"/>
                                <el-option label="100" value="100"/>
                            </el-select>
                        </el-col>
                        <el-col :span="7">
                            <el-input
                                v-model="searchKeyword"
                                @input="doSearch"
                                clearable
                                >
                                <template #prefix>
                                    <span>
                                        <i class="fa fa-search"></i></span>
                                </template>
                            </el-input>
                        </el-col>
                    </el-row>
                </div>
                <div class="block-content p-0">
                    <el-table :data="data" class="w-100" @sort-change="sortChange" header-cell-class-name="bg-body text-dark">
                        <el-table-column type="index" width="50" />
                        <el-table-column label="Lamaran" width="200" sortable>
                          <template #default="scope">
                                <div>
                                    <div class="fw-medium">{{ scope.row.lamaran.user.nama }}</div>
                                    <small class="text-muted">{{ scope.row.lamaran.lowongan.posisi }}</small>
                                </div>
                          </template>  
                        </el-table-column>
                        <el-table-column prop="nama" label="Nama Faskes" sortable/>
                        <el-table-column label="Tanggal" prop="tanggal">
                            <template #default="scope">
                              {{ format_date(scope.row.tanggal)}}
                            </template>
                        </el-table-column>
                        <el-table-column label="Status" prop="status" align="center" width="180">
                            <template #default="scope">
                                <el-tag :type="getTypeStatus(scope.row.status)" size="small">
                                    {{ scope.row.status_label }}
                                </el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="Aksi" align="center" width="150">
                            <template #default="scope">
                                <el-dropdown popper-class="dropdown-action" trigger="click">
                                    <el-button>
                                      <i class="fa-solid fa-ellipsis"></i>
                                    </el-button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item>
                                              <Link :href="route('admin.medical.show', {id: scope.row.id})" class="w-100 d-flex align-items-center justify-content-between space-x-1">
                                                Detail
                                                <i class="si fa-fw si-eye"></i>
                                              </Link>
                                            </el-dropdown-item>
                                            <el-dropdown-item class="d-flex align-items-center justify-content-between space-x-1" @click.prevent="openEditModal(scope.row)">
                                                Ubah
                                                <i class="si fa-fw si-trash"></i>
                                            </el-dropdown-item>
                                            <el-dropdown-item class="d-flex align-items-center justify-content-between space-x-1" @click.prevent="hapus(scope.row.id)">
                                                Hapus
                                                <i class="si fa-fw si-trash"></i>
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                            </template> 
                        </el-table-column>
                    </el-table>
                </div>
                <div class="block-content py-2">
                    <el-row justify="space-between">
                        <el-col :lg="12" class="d-flex">
                            <p class="my-auto text-xs">Menampilkan {{ from }} sampai {{ to }} dari {{ total }}</p>
                        </el-col>
                        <el-col :lg="12" class="text-end">
                            <el-pagination class="float-end" background layout="prev, pager, next" :page-size="pageSize" :total="total" :current-page="page" @current-change="fetchData"/>
                        </el-col>
                    </el-row>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <el-dialog 
            v-model="dialogVisible" 
            :title="isEdit ? 'Edit Medical Checkup' : 'Tambah Medical Checkup'"
            width="600px"
            :before-close="handleClose"
        >
            <el-form 
                ref="formRef" 
                :model="form" 
                :rules="rules" 
                label-position="top"
                v-loading="dialogLoading"
            >
                <el-form-item label="Pilih Lamaran" prop="lamaran_id" v-if="!isEdit">
                    <select-lamaran 
                        v-model="form.lamaran_id" 
                        placeholder="Pilih lamaran untuk medical checkup"
                        style="width: 100%"
                    />
                </el-form-item>
                
                <el-form-item label="Tanggal Medical" prop="tanggal">
                    <el-date-picker
                        v-model="form.tanggal"
                        type="date"
                        placeholder="Pilih tanggal"
                        format="DD/MM/YYYY"
                        value-format="YYYY-MM-DD"
                        style="width: 100%"
                    />
                </el-form-item>
                
                <el-form-item label="Nama Fasilitas Kesehatan" prop="nama">
                    <el-input 
                        v-model="form.nama" 
                        placeholder="Nama Fasilitas Kesehatan (misal: Rumah Sakit Bunga, dll)"
                    />
                </el-form-item>
                
                <el-form-item label="Upload File" prop="file" v-if="!isEdit">
                    <single-file-upload v-model="form.file"/>
                </el-form-item>
                
                <el-form-item label="Hasil" prop="hasil">
                    <el-input 
                        v-model="form.hasil" 
                        placeholder="Hasil medical check"
                    />
                </el-form-item>
                
                <el-form-item label="Status" prop="status" v-if="isEdit">
                    <el-select v-model="form.status" placeholder="Pilih status" style="width: 100%">
                        <el-option label="Pending" value="pending" />
                        <el-option label="Valid" value="valid" />
                        <el-option label="Tidak Valid" value="tidak_valid" />
                    </el-select>
                </el-form-item>
                
                <el-form-item label="Catatan">
                    <el-input
                        v-model="form.catatan"
                        type="textarea"
                        :rows="3"
                        placeholder="Catatan tambahan (opsional)"
                    />
                </el-form-item>
            </el-form>
            
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="handleClose">Batal</el-button>
                    <el-button type="primary" @click="submitForm" :loading="dialogLoading">
                        {{ isEdit ? 'Update' : 'Simpan' }}
                    </el-button>
                </span>
            </template>
        </el-dialog>
    </base-layout>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import moment from 'moment';
import { router } from '@inertiajs/vue3';
import { ElMessage, ElMessageBox } from 'element-plus';
import SelectLamaran from '@/Components/SelectLamaran.vue';
import SingleFileUpload from '@/Components/SingleFileUpload.vue';
import { Link } from '@inertiajs/vue3';
const data = ref([]);
const isLoading = ref(true);
const searchKeyword = ref('');
const limit = 25;
const total = ref(0);
const from = ref(0);
const to = ref(0);
const page = ref(1);
const pageSize = ref(0);

// Modal state
const dialogVisible = ref(false);
const dialogLoading = ref(false);
const isEdit = ref(false);
const formRef = ref();

// Form data
const form = reactive({
    id: null,
    lamaran_id: '',
    nama: '',
    tanggal: '',
    hasil: '',
    status: 'pending',
    catatan: '',
    file: null
});

// Form validation rules
const rules = reactive({
    lamaran_id: [
        { required: true, message: 'Pilih lamaran terlebih dahulu', trigger: 'change' }
    ],
    tanggal: [
        { required: true, message: 'Tanggal medical wajib diisi', trigger: 'blur' }
    ],
    nama: [
        { required: true, message: 'Nama Faskes wajib diisi', trigger: 'blur' }
    ],
    file: [
        { required: true, message: 'File wajib diupload', trigger: 'change' }
    ],
    hasil: [
        { required: true, message: 'Hasil wajib diisi', trigger: 'blur' }
    ],
    status: [
        { required: true, message: 'Status wajib dipilih', trigger: 'change' }
    ]
});

const doSearch = _.debounce(() => {
  isLoading.value = true;
  fetchData();
}, 1000);

const fetchData = async (pg) => {
  try {
    isLoading.value = true;
    const response = await axios.get('/admin/medical/data', {
      params: {
        page: pg || 1,
        limit,
        search: searchKeyword.value,
      },
    });
    if (response.status === 200) {
      data.value = response.data.data;
      page.value = response.data.current_page;
      total.value = response.data.total;
      pageSize.value = response.data.per_page;
      from.value = response.data.from;
      from.to = response.data.to;
    }
  } catch (error) {
    console.error(error);
  }finally{
    isLoading.value = false;
  }
};

const format_date = (value) => {
  if (value) {
    return moment(String(value)).format('DD MMM YYYY');
  }
};

const getTypeStatus = (status) => {
  switch (status) {
    case 'pending':
      return 'warning';
    case 'valid':
      return 'success';
    default:
      return 'danger';
  }
};

const sortChange = (v) => {
    fetchData();
}

// Modal functions
const openAddModal = () => {
    isEdit.value = false;
    resetForm();
    // File is required for add mode
    rules.file = [{ required: true, message: 'File dokumen wajib diupload', trigger: 'change' }];
    dialogVisible.value = true;
};

const openEditModal = (row) => {
    isEdit.value = true;
    form.id = row.id;
    form.nama = row.nama;
    form.tanggal = row.tanggal;
    form.hasil = row.hasil;
    form.status = row.status;
    form.catatan = row.catatan || '';
    // File is not required for edit mode
    rules.file = [];
    dialogVisible.value = true;
};

const handleClose = () => {
    formRef.value?.resetFields();
    resetForm();
    dialogVisible.value = false;
};

const resetForm = () => {
    form.id = null;
    form.lamaran_id = '';
    form.nama = '';
    form.tanggal = '';
    form.hasil = '';
    form.status = 'pending';
    form.catatan = '';
    form.file = null;
};

const submitForm = async () => {
    if (!formRef.value) return;
    
    await formRef.value.validate(async (valid) => {
        if (valid) {
            dialogLoading.value = true;
            
            try {
                const formData = new FormData();
                
                if (isEdit.value) {
                    // Edit mode
                    formData.append('_method', 'PUT');
                    formData.append('nama', form.nama);
                    formData.append('tanggal', form.tanggal);
                    formData.append('hasil', form.hasil);
                    formData.append('status', form.status);
                    if (form.catatan) {
                        formData.append('catatan', form.catatan);
                    }
                    
                    const response = await axios.post(route('admin.medical.update', {id : form.id}), formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    });
                } else {
                    // Add mode
                    formData.append('lamaran_id', form.lamaran_id);
                    formData.append('nama', form.nama);
                    formData.append('tanggal', form.tanggal);
                    formData.append('hasil', form.hasil);
                    if (form.catatan) {
                        formData.append('catatan', form.catatan);
                    }
                    if (form.file) {
                        formData.append('file', form.file);
                    }
                    
                    const response = await axios.post(route('admin.medical.store'), formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    });
                }
                
                ElMessage.success(isEdit.value ? 'Data berhasil diupdate!' : 'Data berhasil ditambahkan!');
                handleClose();
                fetchData();
                
            } catch (error) {
                console.error('Error:', error);
                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    Object.keys(errors).forEach(key => {
                        ElMessage.error(errors[key][0]);
                    });
                } else {
                    ElMessage.error('Terjadi kesalahan saat menyimpan data');
                }
            } finally {
                dialogLoading.value = false;
            }
        }
    });
};

const hapus = (id) => {
  ElMessageBox.confirm('Data yang dihapus tidak bisa dikembalikan!', 'Peringatan', {
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal',
    type: 'warning',
  }).then(async () => {
    try {
        await axios.delete(route('admin.medical.delete', {id : id}));
        ElMessage.success('Data berhasil dihapus!');
        fetchData();
    } catch (error) {
        console.error('Error:', error);
        ElMessage.error('Terjadi kesalahan saat menghapus data');
    }
  }).catch(() => {
    ElMessage.info('Penghapusan dibatalkan');
  });
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.btn-group {
  display: flex;
  gap: 4px;
}

.ep-button--sm {
  padding: 4px 8px;
  font-size: 12px;
}

:deep(.el-dialog__body) {
  padding: 20px;
}

:deep(.el-form-item__label) {
  font-weight: 600;
}

:deep(.el-upload-dragger) {
  width: 100%;
}

:deep(.el-upload__tip) {
  margin-top: 7px;
  color: #606266;
  font-size: 12px;
  line-height: 1.4;
}
</style>