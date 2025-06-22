<template>
    <base-layout>
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <span>Lowongan</span>
                <div class="space-x-1">
                    <el-button type="primary" :tag="Link" :href="route('admin.lowongan.create')">
                        <i class="si si-plus me-1"></i>
                        Tambah Lowongan
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
                        <el-table-column prop="nama" label="Nama" width="250" sortable/>
                        <el-table-column prop="lokasi" label="Lokasi" width="250" sortable/>
                        <el-table-column prop="durasi" label="Durasi" width="100" sortable>
                          <template #default="scope">
                            <span>{{ scope.row.durasi }} Hari</span>
                          </template>
                        </el-table-column>
                        <el-table-column prop="lokasi" label="Lokasi" width="200" sortable/>
                        <el-table-column prop="kapasitas" label="Kapasitas" sortable/>
                        <el-table-column prop="jumlah_peserta_aktif" label="Peserta Aktif" sortable/>
                        <el-table-column label="Aksi" align="center" width="150">
                            <template #default="scope">
                                <el-dropdown popper-class="dropdown-action" trigger="click">
                                    <el-button>
                                      <i class="fa-solid fa-ellipsis"></i>
                                    </el-button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item>
                                              <Link :href="route('admin.training.program.show', {id: scope.row.id})" class="w-100 d-flex align-items-center justify-content-between space-x-1">
                                                Detail
                                                <i class="si fa-fw si-eye"></i>
                                              </Link>
                                            </el-dropdown-item>
                                            <el-dropdown-item>
                                              <Link :href="route('admin.training.program.edit', {id: scope.row.id})" class="w-100 d-flex align-items-center justify-content-between space-x-1">
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
        </div>
    </base-layout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import moment from 'moment';
import { router } from '@inertiajs/vue3'
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

const doSearch = _.debounce(() => {
  isLoading.value = true;
  fetchData();
}, 1000);

const fetchData = async (pg) => {
  try {
    isLoading.value = true;
    const response = await axios.get('/admin/training/program/data', {
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
    case 'Draft':
      return 'success';
    case 'Tervalidasi':
      return 'danger';
    case 'Sudah Interview':
      return 'warning';
    case 'Medical Check Up':
      return 'warning';
    case 'Pelatihan':
      return 'warning';
    case 'Pelatihan':
      return 'Siap Berangkat';
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
    router.delete(`/admin/training/program/${id}/delete`, {
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
onMounted(() => {
  fetchData();
});
</script>