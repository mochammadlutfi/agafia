<template>
    <base-layout>
        <div class="content">
            <el-form label-position="top" label-width="100%" @submit.prevent="submit">
                <div class="content-heading d-flex justify-content-between align-items-center">
                    <span>{{ title }}</span>
                </div>
                
                <div class="block rounded">
                    <div class="block-content p-4" v-loading="loading">
                        <el-row :gutter="40">
                            <el-col :span="12">
                                <el-form-item label="Talent" :error="errors.user_id">
                                    <select-talent v-model="form.user_id" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="Tanggal" :error="errors.tanggal">
                                    <el-date-picker
                                        v-model="form.tanggal"
                                        class="w-100"
                                        type="date"
                                        placeholder="Pick a Date"
                                        format="YYYY/MM/DD"
                                    />
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="Waktu" :error="errors.waktu">
                                    <el-time-picker class="w-100" v-model="form.waktu" placeholder="Arbitrary time" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="Pewawancara" :error="errors.pewawancara">
                                    <select-staff v-model="form.pewawancara" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="24">
                                <el-form-item label="Lokasi" :error="errors.lokasi">
                                    <el-input v-model="form.lokasi" />
                                </el-form-item>
                            </el-col>
                            <el-col :span="24">
                                <el-form-item label="Catatan" :error="errors.catatan">
                                    <el-input type="textarea" :rows="6" v-model="form.catatan" />
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-button native-type="submit" type="primary" :loading="loading">
                            <i class="fa fa-check me-2"></i>
                            Simpan
                        </el-button>
                    </div>
                </div>
            </el-form>
        </div>
    </base-layout>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import SelectTalent from '@/Components/SelectTalent.vue';
// import SelectStaff from '@/Components/SelectStaff.vue';
import SelectStaff from '@/Components/SelectStaff.vue';

const props = defineProps({
    value: {
        type: Object,
        default: () => ({})
    },
    editMode: {
        type: Boolean,
        default: false
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

const title = ref("Tambah Jadwal Interview Baru");
const loading = ref(false);
const form = useForm({
    user_id: null,
    tanggal: null,
    waktu: null,
    lokasi: null,
    pewawancara: null,
    catatan: null
});

const setValue = () => {
    form.nama = props.value.nama;
    form.deskripsi = props.value.deskripsi;
    form.lokasi = props.value.lokasi;
    form.durasi = props.value.durasi;
    form.kapasitas = props.value.kapasitas;
    form.instruktur = props.value.instruktur;
    form.aktif = props.value.aktif;

    title.value = 'Ubah Jadwal Interview';
};

onMounted(() => {
    if (props.editMode) {
        setValue();
    }
});

const submit = () => {
    loading.value = true;
    let url = props.editMode ? route('admin.interview.jadwal.update', { id: props.value.id }) : route('admin.interview.jadwal.store');
    
    form.post(url, {
        preserveScroll: true,
        onFinish: () => {
            loading.value = false;
        },
        onSuccess: () => {
            return Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: `Jadwal Interview Baru Berhasil Ditambahkan!`,
                showCancelButton: true,
                confirmButtonText: 'Tambah Lainnya',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    router.visit(route("admin.interview.jadwal.create"));
                }
            });
        },
    });
};

</script>