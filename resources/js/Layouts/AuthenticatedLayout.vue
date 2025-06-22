<template>
    <el-config-provider namespace="ep" :locale="locale">
        
        <el-container id="page-container" class="sidebar-o enable-page-overlay side-scroll main-content-boxed">

            <base-sidebar/>

            <base-header/>
            
            <!-- <el-header>Header</el-header> -->

            <el-main id="main-container">
                <slot />
            </el-main>

        </el-container>
    </el-config-provider>
</template>
  
<script>
//   import { ElConfigProvider } from 'element-plus';
    import id from 'element-plus/dist/locale/id.mjs'
    import BaseHeader from './BaseHeader.vue';
    import BaseSidebar from './BaseSidebar.vue';
    export default {
        name : 'AuthenticatedLayout',
        components: {
            BaseHeader,
            BaseSidebar,
            ElConfigProvider,
        },
        props : {
            title : {
                type : String
            }
        },
        setup() {
            return {
                zIndex: 3000,
                size: 'small',
                locale : id,
            }
        },
    }
  </script>