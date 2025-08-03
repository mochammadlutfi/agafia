<template>
    <base-layout>
        <div class="content">
            <el-form label-position="top" label-width="100%" @submit.prevent="submit">
                <div class="content-heading d-flex justify-content-between align-items-center">
                    <span>Ubah Password</span>
                </div>
                
                <div class="block rounded">
                    <div class="block-content p-4" v-loading="loading">
                        <el-col :span="12">
                            <el-form-item label="Password Baru" :error="errors.password">
                                <el-input v-model="form.password" type="password" placeholder="Masukan password baru"
                                    show-password />
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="Konfirmasi Password Baru" :error="errors.password">
                                <el-input v-model="form.password_confirmation" type="password" placeholder="Masukan konfirmasi password baru"
                                    show-password />
                            </el-form-item>
                        </el-col>
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


const props = defineProps({
    errors: {
        type: Object,
        default: () => ({})
    }
});
const loading = ref(false);
const form = useForm({
    password: null,
    password_confirmation: null,
});

const submit = () => {
    loading.value = true;

    form.post(route('admin.password'), {
        preserveScroll: true,
        onFinish: () => {
            loading.value = false;
        },
        onSuccess: () => {
            form.password = null;
            form.password_confirmation = null;
            return ElMessage({
            type: 'success',
            message: 'Password Berhasil Diperbaharui!',
            });
        },
    });
};
</script>