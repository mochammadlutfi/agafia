<template>
    <el-select v-model="value" value-key="id" 
    class="w-100"
    filterable 
    clearable 
    remote
    @change="selectChange"
    autocomplete="off"
    :loading="isLoading"
    :disabled="disabled"
    placeholder="Pilih">
        <el-option
            v-for="item in dataList"
            :key="item.id"
            :label="item.nama"
            :value="item.id"
        />
    </el-select>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';


const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: [Number, String],
    status: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const value = ref(props.modelValue);
const dataList = ref([]);
const isLoading = ref(false);
const fetchData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(route('admin.training.program.data'), {
            params: {
                status: props.status,
            },
        });
        if (response.status === 200) {
            dataList.value = response.data;
        }
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
};


watch(() => props.modelValue, (newValue) => {
    value.value = newValue;
});

const selectChange = (v) => {
    value.value = v;
    emit('update:modelValue', v);
};


onMounted(() => {
    fetchData();
});

</script>
