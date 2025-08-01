<template>
    <base-layout>
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Interview</span>
                <div class="space-x-1">
                    <el-button type="primary" @click="openModal" v-if="['owner', 'admin'].includes($page.props.user.level)">
                        <i class="si si-plus me-1"></i>
                        Tambah Jadwal
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
                        <el-table-column prop="tanggal" label="Tanggal" sortable>
                          <template #default="scope">
                            <span>{{ format_date(scope.row.tanggal) }}</span>
                          </template>
                        </el-table-column>
                        <el-table-column prop="lokasi" label="Lokasi" sortable/>
                        <el-table-column prop="pewawancara.nama" label="Pewawancara" sortable/>
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
                                              <Link :href="route('admin.interview.show', {id: scope.row.id})" class="w-100 d-flex align-items-center justify-content-between space-x-1">
                                                Detail
                                                <i class="si fa-fw si-eye"></i>
                                              </Link>
                                            </el-dropdown-item>
                                            <el-dropdown-item class="d-flex align-items-center justify-content-between space-x-1" @click.prevent="edit(scope.row)">
                                                Ubah Jadwal
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

        <!-- Interview Schedule Modal -->
        <el-dialog
            v-model="showInterviewModal"
            title="Jadwalkan Interview"
            width="700px"
            :before-close="handleInterviewModalClose"
        >
            <el-form :model="interviewForm" :rules="interviewRules" ref="interviewFormRef" label-width="150px">
                
              <el-form-item label="Lamaran" prop="lamaran_id">
                    <select-lamaran v-model="interviewForm.lamaran_id" status="diterima" />
                </el-form-item>  
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
    </base-layout>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import moment from 'moment';
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3';
import SelectLamaran from '@/Components/SelectLamaran.vue';
import selectStaff from '@/Components/SelectStaff.vue';


const props = defineProps({
  error: Object,
});

const data = ref([]);
const isLoading = ref(true);
const searchKeyword = ref('');
const limit = 25;
const total = ref(0);
const from = ref(0);
const to = ref(0);
const page = ref(1);
const pageSize = ref(0);

// Modal visibility
const showInterviewModal = ref(false)

// Loading states
const interviewLoading = ref(false)

// Form refs
const interviewFormRef = ref()

// Interview Form
const interviewForm = reactive({
  id: null,
  lamaran_id: null,
  tanggal: '',
  lokasi: '',
  pewawancara_id: '',
  status: 'dijadwalkan',
  catatan: ''
})

const interviewRules = {
    lamaran: [{ required: true, message: 'Lamaran wajib diisi', trigger: 'change' }],
    tanggal: [{ required: true, message: 'Tanggal interview wajib diisi', trigger: 'blur' }],
    lokasi: [{ required: true, message: 'Lokasi interview wajib diisi', trigger: 'blur' }],
    pewawancara_id: [{ required: true, message: 'Pewawancara wajib dipilih', trigger: 'change' }],
    status: [{ required: true, message: 'Status wajib dipilih', trigger: 'change' }]
}

const doSearch = _.debounce(() => {
  isLoading.value = true;
  fetchData();
}, 1000);

const fetchData = async (pg) => {
  try {
    isLoading.value = true;
    const response = await axios.get('/admin/interview/data', {
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
    case 'dijadwalkan':
      return 'info';
    case 'selesai':
      return 'success';
    case 'dijadwal_ulang':
      return 'warning';
    default:
      return 'danger';
  }
};

const sortChange = (v) => {
    fetchData();
}

const hapus = (id) => {
  ElMessageBox.alert('Data yang dihapus tidak bisa dikembalikan!', 'Peringatan', {
    showCancelButton: true,
    confirmButtonText: 'Ya!',
    cancelButtonText: 'Tidak!',
    type: 'warning',
  }).then(() => {
    router.delete(route('admin.interview.delete', {id : id}), {
      preserveScroll: true,
      onSuccess: () => {
        fetchData();
        ElMessage({
          type: 'success',
          message: 'Data Berhasil Dihapus!',
        });
      },
    });
  });
};


const openModal = () => {
    showInterviewModal.value = true;
    interviewFormRef.value?.resetFields();
    interviewForm.id = null;
    interviewForm.lamaran_id = null;
    interviewForm.tanggal = '';
    interviewForm.lokasi = '';
    interviewForm.pewawancara_id = '';
    interviewForm.status = 'dijadwalkan';
    interviewForm.catatan = '';
}

const edit = (row) => {
    showInterviewModal.value = true;
    interviewFormRef.value?.resetFields();
    interviewForm.id = row.id;
    interviewForm.lamaran_id = row.lamaran_id;
    interviewForm.tanggal = row.tanggal;
    interviewForm.lokasi = row.lokasi;
    interviewForm.pewawancara_id = row.pewawancara_id;
    interviewForm.status = row.status;
    interviewForm.catatan = row.catatan || '';
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

// Form submissions
const submitInterviewForm = async () => {
    if (!interviewFormRef.value) return
    
    await interviewFormRef.value.validate((valid) => {
        if (valid) {
          interviewLoading.value = true
          const url = interviewForm.id ? route('admin.interview.update', {id : interviewForm.id}) : route('admin.interview.store');
          try {        
              axios.post(url, interviewForm)
              
              ElMessage.success('Interview berhasil disimpan')
              showInterviewModal.value = false
              router.visit(route('admin.interview.index'), {
                  preserveState: false,
                  preserveScroll: true
              });
              
          } catch (error) {
              ElMessage.error('Gagal menjadwalkan interview')
          } finally {
              interviewLoading.value = false;
          }
        }
    });
    
    fetchData();
}

onMounted(() => {
  fetchData();
});
</script>