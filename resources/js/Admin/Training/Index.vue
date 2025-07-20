<template>
    <base-layout>
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Peserta Training</span>
                <div class="space-x-1">
                    <el-dropdown trigger="click">
                        <el-button type="success" :loading="exportLoading">
                            <i class="fa fa-download me-1"></i>
                            Export
                            <el-icon class="el-icon--right"><arrow-down /></el-icon>
                        </el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item @click="showPdfExportModal = true">
                                    <i class="fa fa-file-pdf-o me-2"></i>
                                    Export PDF
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                    <el-button @click="onOpenForm" type="primary">
                      <i class="si si-plus me-1"></i>
                        Tambah Peserta
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
                        <el-table-column prop="user.nama" label="Nama" width="200px" sortable/>
                        <el-table-column prop="program.nama" label="Program" width="200px" sortable/>
                        <el-table-column prop="tanggal_daftar" label="Tgl Daftar" sortable>
                          <template #default="scope">
                            <span>{{ format_date(scope.row.tanggal_daftar) }}</span>
                          </template>
                        </el-table-column>
                        <el-table-column prop="tanggal_mulai" label="Tgl Mulai" sortable>
                          <template #default="scope">
                            <span>{{ format_date(scope.row.tanggal_mulai) }}</span>
                          </template>
                        </el-table-column>
                        <el-table-column prop="tanggal_selesai" label="Tgl Selesai" sortable>
                          <template #default="scope">
                            <span>{{ format_date(scope.row.tanggal_selesai) }}</span>
                          </template>
                        </el-table-column>
                        <el-table-column prop="status" label="Status" sortable>
                          <template #default="scope">
                            <el-tag :type="getTypeStatus(scope.row.status)" size="small">
                                {{ scope.row.status_label }}
                            </el-tag>
                          </template>
                        </el-table-column>
                        <el-table-column label="Aksi" align="center" width="100px">
                            <template #default="scope">
                                <el-dropdown popper-class="dropdown-action" trigger="click">
                                    <el-button>
                                      <i class="fa-solid fa-ellipsis"></i>
                                    </el-button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item>
                                              <Link :href="route('admin.training.show', {id: scope.row.id})" class="d-flex align-items-center justify-content-between space-x-1">
                                                Detail
                                                <i class="si fa-fw si-eye"></i>
                                              </Link>
                                            </el-dropdown-item>
                                            <el-dropdown-item class="d-flex align-items-center justify-content-between space-x-1" @click.prevent="onEditForm(scope.row)">
                                                Ubah
                                                <i class="si fa-fw si-note"></i>
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
        <el-dialog v-model="formShow" :title="formTitle" width="600px" v-loading="loadingForm" :close-on-click-modal="false" :close-on-press-escape="false">
            <el-form :model="form" label-position="top">
                <el-form-item label="Lamaran" :error="errors.lamaran_id">
                    <select-lamaran 
                        v-model="form.lamaran_id" 
                        placeholder="Pilih Lamaran"
                        style="width: 100%"
                    />
                </el-form-item>
                <el-form-item label="Program Pelatihan" :error="errors.program_id">
                    <select-program v-model="form.program_id"/>
                </el-form-item>
                <el-row :gutter="20">
                    <el-col :md="12">
                        <el-form-item label="Tanggal Daftar" :error="errors.tanggal_daftar">
                            <el-date-picker
                                v-model="form.tanggal_daftar"
                                type="date"
                                placeholder="Pilih tanggal daftar"
                                format="DD/MM/YYYY"
                                value-format="YYYY-MM-DD"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Status" :error="errors.status">
                            <el-select v-model="form.status" placeholder="Pilih status" style="width: 100%">
                                <el-option label="Terdaftar" value="terdaftar" />
                                <el-option label="Sedang Pelatihan" value="sedang_pelatihan" />
                                <el-option label="Selesai" value="selesai" />
                                <el-option label="Mengundurkan Diri" value="mengundurkan_diri" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Tanggal Mulai" :error="errors.tanggal_mulai">
                            <el-date-picker
                                v-model="form.tanggal_mulai"
                                type="date"
                                placeholder="Pilih tanggal mulai"
                                format="DD/MM/YYYY"
                                value-format="YYYY-MM-DD"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Tanggal Selesai" :error="errors.tanggal_selesai">
                            <el-date-picker
                                v-model="form.tanggal_selesai"
                                type="date"
                                placeholder="Pilih tanggal selesai"
                                format="DD/MM/YYYY"
                                value-format="YYYY-MM-DD"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Nilai Akhir" :error="errors.nilai_akhir">
                            <el-input-number
                                v-model="form.nilai_akhir"
                                :min="0"
                                :max="100"
                                :step="1"
                                :precision="0"
                                placeholder="Nilai 0-100"
                                style="width: 100%"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :md="12">
                        <el-form-item label="Sertifikat">
                            <el-checkbox v-model="form.sertifikat_diterbitkan">
                                Sertifikat sudah diterbitkan
                            </el-checkbox>
                        </el-form-item>
                    </el-col>
                    <el-col :md="12" v-if="form.sertifikat_diterbitkan">
                        <el-form-item label="No. Sertifikat">
                            <el-input v-model="form.nomor_sertifikat" placeholder="Nomor sertifikat" />
                        </el-form-item>
                    </el-col>
                </el-row>
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
                    <el-button @click="onCloseForm">Batal</el-button>
                    <el-button type="success" @click.prevent="onSubmit" :loading="loadingForm">
                        {{ editMode ? 'Update' : 'Daftarkan' }}
                    </el-button>
                </span>
            </template>
        </el-dialog>

        <!-- PDF Export Modal -->
        <el-dialog
            v-model="showPdfExportModal"
            title="Export Laporan Training PDF"
            width="600px"
            :before-close="handlePdfModalClose"
        >
            <el-form :model="pdfExportForm" label-position="top" ref="pdfExportFormRef">
                <el-alert
                    title="Export Laporan Training PDF"
                    type="info"
                    description="Pilih filter untuk menggenerate laporan PDF yang komprehensif dengan detail lengkap data training."
                    show-icon
                    :closable="false"
                    class="mb-4"
                />

                <el-row :gutter="16">
                    <el-col :span="12">
                        <el-form-item label="Filter Status">
                            <el-select v-model="pdfExportForm.status" placeholder="Pilih status (opsional)" clearable style="width: 100%">
                                <el-option
                                    v-for="(label, value) in statusOptions"
                                    :key="value"
                                    :label="label"
                                    :value="value"
                                />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Program ID">
                            <el-input v-model="pdfExportForm.program_id" placeholder="ID Program (opsional)" />
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="16">
                    <el-col :span="12">
                        <el-form-item label="Tanggal Daftar Mulai">
                            <el-date-picker
                                v-model="pdfExportForm.tanggal_dari"
                                type="date"
                                placeholder="Pilih tanggal mulai"
                                style="width: 100%"
                                format="DD/MM/YYYY"
                                value-format="YYYY-MM-DD"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Tanggal Daftar Akhir">
                            <el-date-picker
                                v-model="pdfExportForm.tanggal_sampai"
                                type="date"
                                placeholder="Pilih tanggal akhir"
                                style="width: 100%"
                                format="DD/MM/YYYY"
                                value-format="YYYY-MM-DD"
                            />
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="16">
                    <el-col :span="12">
                        <el-form-item label="Tanggal Mulai Training">
                            <el-date-picker
                                v-model="pdfExportForm.tanggal_mulai_dari"
                                type="date"
                                placeholder="Pilih tanggal mulai training"
                                style="width: 100%"
                                format="DD/MM/YYYY"
                                value-format="YYYY-MM-DD"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Tanggal Mulai Training Akhir">
                            <el-date-picker
                                v-model="pdfExportForm.tanggal_mulai_sampai"
                                type="date"
                                placeholder="Pilih tanggal akhir training"
                                style="width: 100%"
                                format="DD/MM/YYYY"
                                value-format="YYYY-MM-DD"
                            />
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-form-item label="Preview Filter">
                    <div class="border rounded p-3 bg-light">
                        <div class="small text-muted">
                            <div v-if="pdfExportForm.status">
                                <strong>Status:</strong> {{ statusOptions[pdfExportForm.status] }}
                            </div>
                            <div v-if="pdfExportForm.program_id">
                                <strong>Program ID:</strong> {{ pdfExportForm.program_id }}
                            </div>
                            <div v-if="pdfExportForm.tanggal_dari || pdfExportForm.tanggal_sampai">
                                <strong>Periode Daftar:</strong> 
                                {{ pdfExportForm.tanggal_dari ? format_date(pdfExportForm.tanggal_dari) : 'Awal' }} - 
                                {{ pdfExportForm.tanggal_sampai ? format_date(pdfExportForm.tanggal_sampai) : 'Sekarang' }}
                            </div>
                            <div v-if="pdfExportForm.tanggal_mulai_dari || pdfExportForm.tanggal_mulai_sampai">
                                <strong>Periode Training:</strong> 
                                {{ pdfExportForm.tanggal_mulai_dari ? format_date(pdfExportForm.tanggal_mulai_dari) : 'Awal' }} - 
                                {{ pdfExportForm.tanggal_mulai_sampai ? format_date(pdfExportForm.tanggal_mulai_sampai) : 'Sekarang' }}
                            </div>
                            <div v-if="!pdfExportForm.status && !pdfExportForm.program_id && !pdfExportForm.tanggal_dari && !pdfExportForm.tanggal_sampai && !pdfExportForm.tanggal_mulai_dari && !pdfExportForm.tanggal_mulai_sampai">
                                <em>Semua data training akan disertakan dalam laporan</em>
                            </div>
                        </div>
                    </div>
                </el-form-item>
            </el-form>

            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showPdfExportModal = false">Batal</el-button>
                    <el-button type="danger" @click="exportPdf" :loading="pdfExportLoading">
                        <i class="fa fa-file-pdf-o me-1"></i>
                        Generate PDF
                    </el-button>
                </span>
            </template>
        </el-dialog>
        </div>
    </base-layout>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import moment from 'moment';
import { router, Link, useForm } from '@inertiajs/vue3'
import { ElMessage } from 'element-plus'
import { ArrowDown } from '@element-plus/icons-vue'
import SelectLamaran from '@/Components/SelectLamaran.vue';
import SelectProgram from '@/Components/SelectProgram.vue';


const props = defineProps({
  errors: {
      type: Object,
      default: () => ({})
  }
})

const data = ref([]);
const isLoading = ref(true);
const searchKeyword = ref('');
const limit = ref(25);
const total = ref(0);
const from = ref(0);
const to = ref(0);
const page = ref(1);
const pageSize = ref(0);

const doSearch = _.debounce(() => {
  isLoading.value = true;
  fetchData();
}, 1000);

const fetchData = async (pg) => {
  try {
    isLoading.value = true;
    const response = await axios.get('/admin/training/data', {
      params: {
        page: pg || 1,
        limit: limit.value,
        search: searchKeyword.value,
      },
    });
    if (response.status === 200) {
      data.value = response.data.data;
      page.value = response.data.current_page;
      total.value = response.data.total;
      pageSize.value = response.data.per_page;
      from.value = response.data.from;
      to.value = response.data.to;
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
    case 'terdaftar':
      return 'warning';
    case 'sedang_pelatihan':
      return 'info';
    case 'selesai':
      return 'success';
    default:
      return 'info';
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
    router.delete(route('admin.training.delete', {id :id }), {
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


const formShow = ref(false);
const formTitle = ref('Tambah Peserta');
const editMode = ref(false);
const loadingForm = ref(false);

// PDF Export state
const exportLoading = ref(false);
const pdfExportLoading = ref(false);
const showPdfExportModal = ref(false);
const pdfExportFormRef = ref();

// PDF Export form
const pdfExportForm = reactive({
    status: '',
    program_id: '',
    tanggal_dari: '',
    tanggal_sampai: '',
    tanggal_mulai_dari: '',
    tanggal_mulai_sampai: ''
});

// Status options for training
const statusOptions = {
    'terdaftar': 'Terdaftar',
    'sedang_pelatihan': 'Sedang Pelatihan',
    'selesai': 'Selesai',
    'mengundurkan_diri': 'Mengundurkan Diri'
};

const form = useForm({
  id : null,
  lamaran_id : null,
  program_id : null,
  tanggal_daftar : null,
  tanggal_mulai : null,
  tanggal_selesai : null,
  status : 'terdaftar',
  nilai_akhir : null,
  sertifikat_diterbitkan : false,
  nomor_sertifikat : '',
  catatan : '',
});

const onOpenForm = () => {
  formShow.value = true;
  editMode.value = false;
  formTitle.value = 'Tambah Peserta';
  form.reset();
}

const onCloseForm = () => {
  formShow.value = false;
  editMode.value = false;
  form.reset();
  form.clearErrors();
}

const onEditForm = (data) => {
  editMode.value = true;
  formTitle.value = 'Ubah Peserta';
  formShow.value = true;
  
  form.id = data.id;
  form.lamaran_id = data.lamaran_id;
  form.program_id = data.program_id;
  form.tanggal_daftar = data.tanggal_daftar;
  form.tanggal_mulai = data.tanggal_mulai;
  form.tanggal_selesai = data.tanggal_selesai;
  form.status = data.status;
  form.nilai_akhir = data.nilai_akhir;
  form.sertifikat_diterbitkan = data.sertifikat_diterbitkan;
  form.nomor_sertifikat = data.nomor_sertifikat;
  form.catatan = data.catatan;
}



const onSubmit = async () => {
    loadingForm.value = true;
    
    try {
        let url = editMode.value ? route('admin.training.update', {id : form.id}) : route('admin.training.store');
        await axios.post(url, form)
        
        ElMessage.success('Pelatihan berhasil didaftarkan')
        fetchData();
        onCloseForm();
        
        // Refresh page data
        router.visit(route('admin.training.index'), {
            preserveState: false,
            preserveScroll: true
        })
    } catch (error) {
        console.error('Error scheduling training:', error)
        ElMessage.error('Gagal mendaftarkan pelatihan')
    } finally {
      loadingForm.value = false
    }
    // form[method](url, {
    //     preserveScroll: true,
    //     onFinish:() => {
    //         loadingForm.value = false;
    //     },
    //     onSuccess: (response) => {
    //         ElMessage({
    //             type: 'success',
    //             message: 'Data Berhasil Disimpan!',
    //         });
    //         onCloseForm();
    //         fetchData();
    //     },
    //     onError: (errors) => {
    //         ElMessage({
    //             type: 'error',
    //             message: 'Terjadi kesalahan, silakan coba lagi!',
    //         });
    //     }
    // });
}

// PDF Export functions
const exportPdf = async () => {
    pdfExportLoading.value = true;
    try {
        const params = new URLSearchParams();
        
        // Add non-empty parameters
        Object.keys(pdfExportForm).forEach(key => {
            if (pdfExportForm[key]) {
                params.append(key, pdfExportForm[key]);
            }
        });
        
        const url = route('admin.training.export-pdf') + (params.toString() ? '?' + params.toString() : '');
        window.open(url, '_blank');
        
        ElMessage.success('PDF sedang diproses, file akan didownload secara otomatis');
        showPdfExportModal.value = false;
    } catch (error) {
        console.error('Error exporting PDF:', error);
        ElMessage.error('Gagal mengexport PDF');
    } finally {
        pdfExportLoading.value = false;
    }
};

const handlePdfModalClose = (done) => {
    // Reset form
    Object.keys(pdfExportForm).forEach(key => {
        pdfExportForm[key] = '';
    });
    done();
}

onMounted(() => {
  fetchData();
});
</script>