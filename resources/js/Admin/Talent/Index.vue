<template>
    <base-layout>
        <div class="content">
            <div class="content-heading d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-0">Kelola Talent</h2>
                    <small class="text-muted">Kelola data talent dan lamaran kerja</small>
                </div>
                <div class="space-x-1">
                </div>
            </div>
            <div class="block rounded bordered" v-loading="isLoading" >
                <div class="block-content py-3">
                    <el-row :gutter="16">
                        <el-col :span="4">
                            <el-select v-model="limit" placeholder="Per halaman" style="width: 100%" @change="fetchData(1)">
                                <el-option label="25" :value="25"/>
                                <el-option label="50" :value="50"/>
                                <el-option label="100" :value="100"/>
                            </el-select>
                        </el-col>
                        <el-col :span="6">
                            <el-select v-model="statusFilter" placeholder="Filter Status" style="width: 100%" @change="fetchData(1)" clearable>
                                <el-option label="Semua Status" value="all"/>
                                <el-option label="Menunggu Review" value="pending"/>
                                <el-option label="Diterima" value="diterima"/>
                                <el-option label="Interview" value="interview"/>
                                <el-option label="Medical Check Up" value="medical"/>
                                <el-option label="Pelatihan" value="pelatihan"/>
                                <el-option label="Siap Berangkat" value="siap"/>
                                <el-option label="Selesai" value="selesai"/>
                                <el-option label="Ditolak" value="ditolak"/>
                            </el-select>
                        </el-col>
                        <el-col :span="8">
                            <el-input
                                v-model="searchKeyword"
                                @input="doSearch"
                                placeholder="Cari nama, email, NIK..."
                                clearable
                                >
                                <template #prefix>
                                    <i class="fa fa-search"></i>
                                </template>
                            </el-input>
                        </el-col>
                        <el-col :span="6">
                            <el-button @click="resetFilters" type="default">
                                <i class="fa fa-refresh me-1"></i>
                                Reset Filter
                            </el-button>
                        </el-col>
                    </el-row>
                </div>
                <div class="block-content p-0">
                    <el-table :data="data" class="w-100" @sort-change="sortChange" header-cell-class-name="bg-body text-dark">
                        <el-table-column type="index" width="50" />
                        <el-table-column label="Nama" sortable>
                            <template #default="scope">
                                <div>
                                    <div class="fw-medium">{{ scope.row.nama }}</div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column label="NIK" prop="nik" sortable width="120">
                            <template #default="scope">
                                <div>{{ scope.row.nik || '-' }}</div>
                            </template>
                        </el-table-column>
                        <el-table-column label="email" sortable>
                            <template #default="scope">
                              <div>{{ scope.row.email || '-' }}</div>
                            </template>
                        </el-table-column>
                        <el-table-column label="Lamaran Aktif" width="200">
                            <template #default="scope">
                                <div v-if="scope.row.active_application">
                                    <div class="fw-medium">{{ scope.row.active_application.lowongan?.posisi || '-' }}</div>
                                    <small class="text-muted">{{ scope.row.active_application.lowongan?.perusahaan || '-' }}</small>
                                </div>
                                <span v-else class="text-muted">Tidak ada lamaran aktif</span>
                            </template>
                        </el-table-column>
                        <el-table-column label="Aksi" align="center" width="120">
                            <template #default="scope">
                                <el-button 
                                    :tag="Link"
                                    :href="route('admin.talent.show', scope.row.id)"
                                    type="primary"
                                    size="small"
                                >
                                    <i class="fa fa-eye me-1"></i>
                                    Detail
                                </el-button>
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
import { Link, router } from '@inertiajs/vue3'
import { debounce } from 'lodash'

const data = ref([]);
const isLoading = ref(true);
const searchKeyword = ref('');
const statusFilter = ref('all');
const limit = ref(25);
const total = ref(0);
const from = ref(0);
const to = ref(0);
const page = ref(1);
const pageSize = ref(0);

const doSearch = debounce(() => {
  isLoading.value = true;
  fetchData();
}, 1000);

const fetchData = async (pg) => {
  try {
    isLoading.value = true;
    const response = await axios.get('/admin/talent/data', {
      params: {
        page: pg || 1,
        limit: limit.value,
        search: searchKeyword.value,
        status: statusFilter.value !== 'all' ? statusFilter.value : null,
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

const resetFilters = () => {
  searchKeyword.value = '';
  statusFilter.value = 'all';
  page.value = 1;
  fetchData();
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
    case 'diterima':
      return 'success';
    case 'interview':
      return 'primary';
    case 'medical':
      return 'info';
    case 'pelatihan':
      return '';
    case 'siap':
      return 'success';
    case 'selesai':
      return 'success';
    case 'ditolak':
      return 'danger';
    case 'none':
      return 'info';
    default:
      return 'secondary';
  }
};

const sortChange = (v) => {
    fetchData();
}

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.space-x-1 > * + * {
    margin-left: 0.25rem;
}

.fw-medium {
    font-weight: 500;
}
</style>