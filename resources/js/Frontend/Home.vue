<template>
    <base-layout>
        <Head>
            <title>Home</title>
            <meta name="description" content="">
        </Head>
        <banner :data="banner"/>
        
         <div class="bg-white">
            <div class="content content-full">
                <el-row>
                    <el-col :md="12">
                        <img src="/images/profile-PT-Agafia-Adda-Mandiri.png" class="w-100"/>
                    </el-col>
                    <el-col :md="12">
                        <div class="tb_text_wrap">
                            <h6>Profil Perusahaan</h6>
                            <h2>PT. Agafia <span style="color: #79b931;">Adda</span> Mandiri</h2>
                            <h5>Indonesian Manpower Supplier Company</h5>
                        </div>
                        <div>
                            <p>
                                <strong>PT Agafia Adda Mandiri</strong> merupakan perusahaan yang bergerak di bidang jasa 
                                pengiriman tenaga kerja ke luar negeri. Kami menyediakan layanan terpadu untuk memenuhi 
                                kebutuhan perusahaan-perusahaan di luar negeri yang membutuhkan tenaga kerja berkualitas 
                                dari Indonesia.
                            </p>
                            <p>Dengan pengalaman yang luas dan jaringan yang luas, kami memastikan bahwa proses 
                                pengiriman tenaga kerja dilakukan secara profesional, aman, dan efisien.
                            </p>
                        </div>
                    </el-col>
                </el-row>
            </div>
         </div>
    </base-layout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import Banner from './Layouts/Banner.vue';

const props = defineProps({
    banner : {
        type : Array,
        default : []
    }
})

</script>


<style scoped>
.program_item {
  display: flex;
  flex-direction: column;
}
.program_item .block {
  flex: 1;
}

.program_item img{
    width:64px;
    text-align: center;
}
.float{
	position:fixed;
	width:60px;
	height:60px;
	bottom:40px;
	right:40px;
	background-color:#25d366;
	color:#FFF;
	border-radius:50px;
	text-align:center;
    font-size:30px;
	box-shadow: 2px 2px 3px #999;
    z-index:100;
}

.my-float{
	margin-top:16px;
}
</style>
