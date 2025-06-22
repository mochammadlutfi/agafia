<template>
    <base-layout>
        <div class="content mb-4">
            <el-row :gutter="30">
                <el-col :lg="6">
                    <el-card>
                        <ul class="nav-main nav-main-landing">
                            <li class="nav-main-item">
                                <a class="nav-main-link" :class="(route().current('user.biodata.index')) ? 'active' : ''" :href="route('user.biodata.index')">
                                    <i class="nav-main-link-icon fa fa-user"></i>
                                    <span class="nav-main-link-name">Profil</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" :class="(route().current('user.interview')) ? 'active' : ''" :href="route('user.interview')">
                                    <i class="nav-main-link-icon fa fa-calendar-xmark"></i>
                                    <span class="nav-main-link-name">Jadwal Interview</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" :class="(route().current('user.medical.index')) ? 'active' : ''" :href="route('user.medical.index')">
                                    <i class="nav-main-link-icon fa fa-stethoscope"></i>
                                    <span class="nav-main-link-name">Medical Checkup</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" :class="(route().current('user.biodata.index')) ? 'active' : ''" :href="route('user.biodata.index')">
                                    <i class="fa fa-sign-out nav-main-link-icon"></i>
                                    <span class="nav-main-link-name">Keluar</span>
                                </a>
                            </li>
                        </ul>
                    </el-card>
                </el-col>
                <el-col :lg="18">
                    <slot />
                </el-col>
            </el-row>
        </div>
    </base-layout>
</template>
  
<script>
    import { Link, Head } from '@inertiajs/vue3';
    import id from 'element-plus/dist/locale/id.mjs'
    import BaseNavigation from './BaseNavigation.vue';
    import simplebar from 'simplebar-vue';
    export default {
        name : 'AuthenticatedLayout',
        components: {
            simplebar,
            ElConfigProvider,
            Link,
            BaseNavigation,
            Head
        },
        data (){
            return {
                sidebar : true,
                sidebarMobile : false,
            }
        },
        computed : {  
            classContainer() {
                return {
                    'side-scroll': true,
                    'main-content-boxed': true,
                    'side-trans-enabled': true,
                    'page-header-fixed': true,
                    'enable-page-overlay' : true,
                    'sidebar-o': this.sidebar,
                    'sidebar-o-xs': this.sidebarMobile,
                }
            },
        },
        props : {
            title : {
                type : String,
                default : '',
            }
        },
        mounted(){
            if(route().current('register.detail') || route().current('verification.notice')){
                this.sidebar = false;
            }
            // this.sidebarMobile = false;
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