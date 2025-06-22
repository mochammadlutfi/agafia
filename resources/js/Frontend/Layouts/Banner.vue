<template>
    <el-carousel trigger="click" height="700px">
      <el-carousel-item v-for="(d,i) in data" :key="i">
        <img :src="d" class="w-100 h-auto"/>
      </el-carousel-item>
    </el-carousel>
</template>

<script setup>

const props = defineProps({
    data : {
        type : Array,
        default : []
    }
})

</script>


<style scoped>
.demonstration {
  color: var(--el-text-color-secondary);
}

.el-carousel__item h3 {
  color: #475669;
  opacity: 0.75;
  line-height: 150px;
  margin: 0;
  text-align: center;
}

.el-carousel__item:nth-child(2n) {
  background-color: #99a9bf;
}

.el-carousel__item:nth-child(2n + 1) {
  background-color: #d3dce6;
}
</style>