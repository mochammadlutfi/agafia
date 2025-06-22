<template>
    <div class="w-100">
        <el-row :gutter="10" v-if="val">
            <el-col :md="12">
                <el-button native-type="button" class="w-100" @click="openFile">
                    Lihat
                </el-button>
            </el-col>
            <el-col :md="12">
                <el-button native-type="button" class="w-100" type="danger" @click="removeFile">
                    Hapus
                </el-button>
            </el-col>
        </el-row>
        <input v-else type="file" class="form-control" @change="handleFileChange" />
        <div v-if="error" class="text-danger fs-sm">{{ error }}</div>
        <el-dialog v-model="showModal" title="Detail File" class="w-75 min-vh-100">
            <iframe :src="isFile(val) ? val : `/uploads/${val}`" width="100%" height="100%" class="min-vh-100"></iframe>
        </el-dialog>
    </div>

</template>
  
  <script setup>
  import {
      ref,
      defineProps,
      defineEmits,
      watch
  } from 'vue';

  const props = defineProps({
      modelValue: {
          type: [String, File],
          default: null
      },
      inputClass: String,
      id: {
          type: String,
          default: '',
      }
  });

  const emit = defineEmits(['update:modelValue']);

  const error = ref('');
  const showModal = ref(false);
  const val = ref(props.modelValue);

  const validateFile = (file) => {
      const validFormats = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
      const maxSize = 1 * 1024 * 1024; // 1MB in bytes

      if (!validFormats.includes(file.type)) {
          return 'Format file salah, hanya boleh PDF, JPG, JPEG, dan PNG.';
      }

      if (file.size > maxSize) {
          val.value = null;
          return 'Ukuran file tidak boleh lebih dari 1MB.';
      }

      return '';
  };

  const handleFileChange = (event) => {
      const file = event.target.files[0];
      const validationError = validateFile(file);

      if (validationError) {
          error.value = validationError;
          emit('update:modelValue', null);
          val.value = null;
      } else {
          error.value = '';
          const reader = new FileReader();
          reader.onload = (e) => {
              val.value = e.target.result;
          };
          reader.readAsDataURL(file);
          emit('update:modelValue', file);
      }
  };

  const openFile = () => {
      showModal.value = true;
  };

  const removeFile = () => {
      val.value = null;
      emit('update:modelValue', null);
  }

  const isFile = (val) => {
    if (val instanceof File) {
        return true;
    }else{
        return false
    }
  }

  watch(() => props.modelValue, (newValue) => {
      if (newValue) {
            if(newValue instanceof File){
                error.value = validateFile(newValue);
            }
          val.value = newValue;
      }
  });
  </script>
  