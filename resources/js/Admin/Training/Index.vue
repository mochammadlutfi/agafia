<template>
    <base-layout>
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Peserta Training</span>
                <div class="space-x-1">
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
                        <el-table-column prop="talent.nama" label="Nama" width="200px" sortable/>
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
                                            <el-dropdown-item>
                                              <Link :href="route('admin.training.edit', {id: scope.row.id})" class="d-flex align-items-center justify-content-between space-x-1">
                                                Ubah
                                                <i class="si fa-fw si-note"></i>
                                              </Link>
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
        <el-dialog v-model="formShow" :title="formTitle" width="40%" v-loading="loadingForm" :close-on-click-modal="false" :close-on-press-escape="false">
            <el-form :model="form" label-position="top">
                <el-form-item label="Talent" :error="errors.user_id">
                    <select-talent v-model="form.user_id" status="medical"/>
                </el-form-item>
                <el-form-item label="Program" :error="errors.program_id">
                    <select-program v-model="form.program_id"/>
                </el-form-item>
                <el-form-item label="Tanggal" :error="errors.tanggal">
                  <el-date-picker
                    v-model="form.tanggal"
                    type="daterange"
                    range-separator="Sampai"
                    start-placeholder="Tangal Mulai"
                    end-placeholder="Tanggal Selesai"
                    :size="size"
                  />
                </el-form-item>
                <div class="text-end">
                  
                  <el-button @click="onCloseForm">
                        <i class="fa fa-close me-2"></i>
                        Batal
                    </el-button>
                    <el-button type="primary" @click.prevent="onSubmit">
                        <i class="fa fa-check me-2"></i>
                        Simpan
                    </el-button>
                </div>
            </el-form>
        </el-dialog>
        </div>
    </base-layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import moment from 'moment';
import { router, Link, useForm } from '@inertiajs/vue3'
import SelectTalent from '@/Components/SelectTalent.vue';
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
const limit = 25;
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
    router.delete(`/admin/training/${id}/delete`, {
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

const form = useForm({
  id : null,
  user_id : null,
  program_id : null,
  tanggal : null,
});

const onOpenForm = () => {
  formShow.value = true;
}

const onCloseForm = () => {
  formShow.value = false;
}



const onSubmit = () => {
  
    let url = editMode.value ? route('admin.training.update', {id : form.id}) : route('admin.training.store');
    form.post(url, {
        preserveScroll: true,
        onFinish:() => {
            loadingForm.value = false;
            fetchData();
        },
        onSuccess: () => {
            ElMessage({
                type: 'success',
                message: 'Data Berhasil Disimpan!',
            });
            this.onCloseForm();
        },
    });
}

onMounted(() => {
  fetchData();
});
</script>