<template>
    <base-layout>
        <div class="content mb-4">
            <el-row :gutter="30">
                <el-col :lg="6">
                    <el-card>
                        <ul class="nav-main nav-main-landing mb-0">
                            <li class="nav-main-item">
                                <a class="nav-main-link" :class="(route().current('user.dashboard')) ? 'active' : ''" :href="route('user.dashboard')">
                                    <i class="nav-main-link-icon fa fa-home"></i>
                                    <span class="nav-main-link-name">Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" :class="(route().current('user.biodata.index')) ? 'active' : ''" :href="route('user.biodata.index')">
                                    <i class="nav-main-link-icon fa fa-user"></i>
                                    <span class="nav-main-link-name">Profil</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" :class="(route().current('user.lamaran.index')) ? 'active' : ''" :href="route('user.lamaran.index')">
                                    <i class="nav-main-link-icon fa fa-calendar-xmark"></i>
                                    <span class="nav-main-link-name">Lamaran Saya</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link" :class="(route().current('user.training.index')) ? 'active' : ''" :href="route('user.training.index')">
                                    <i class="nav-main-link-icon fa fa-chalkboard-teacher"></i>
                                    <span class="nav-main-link-name">Training Saya</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <Link :href="route('admin.logout')" method="post" as="button" type="button" class="btn btn-logout nav-main-link w-100">
                                    <i class="fa fa-sign-out nav-main-link-icon"></i>
                                    <span class="nav-main-link-name text-start">Keluar</span>
                                </Link>
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