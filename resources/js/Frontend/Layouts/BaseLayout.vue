<template>
    <el-config-provider namespace="ep" :locale="locale">
        <div id="page-container" :class="classContainer">
            
            <header id="page-header" style="height:4.25rem"> 
                <!-- Header Content -->
                <div class="content-header" style="height:4.25rem">
                    <!-- Left Section -->
                    <div class="space-x-2 d-flex align-items-center">
                        <!-- Logo -->
                        <Link :href="route('home')">
                            <img src="/images/logo/logo2.png" width="250"/>
                        </Link>
                        <!-- END Logo -->
                    </div>
                    <!-- END Left Section -->

                    <!-- Right Section -->
                    <div class="space-x-1 d-flex">
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <ul class="nav-main nav-main-horizontal nav-main-hover d-none d-lg-block my-md-auto">
                            <li class="nav-main-item">
                                <Link class="nav-main-link" :href="route('home')">
                                    <span class="nav-main-link-name">Home</span>
                                </Link>
                            </li>
                            <li class="nav-main-item">
                                <Link class="nav-main-link" href="/tentang-kami">
                                    <span class="nav-main-link-name">Tentang Kami</span>
                                </Link>
                            </li>
                            <li class="nav-main-item">
                                <Link class="nav-main-link" href="/layanan">
                                    <span class="nav-main-link-name">Layanan Kami</span>
                                </Link>
                            </li>
                            <li class="nav-main-item">
                                <Link class="nav-main-link" :href="route('lowongan')">
                                    <span class="nav-main-link-name">Lowongan</span>
                                </Link>
                            </li>
                        </ul>

                        <button type="button" class="btn btn-sm btn-alt-secondary d-lg-none" @click.prevent="sidebar = true">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>
                        <el-dropdown class="d-none d-lg-block" trigger="click"  popper-class="dropdown-user" v-if="$page.props.user">
                            <div class="d-flex">
                                <el-avatar>
                                    {{ $page.props.user.initial_avatar }}
                                </el-avatar>
                                {{ $page.props.user.nama }}
                            </div>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item>
                                        <Link :href="route('user.biodata.index')" type="button" class="dropdown-item d-flex align-items-center justify-content-between space-x-1">
                                            <span class="fs-sm fw-500">Profil</span>
                                            <i class="fa fa-fw fa-user opacity-25"></i>
                                        </Link>
                                    </el-dropdown-item>
                                    <!-- <el-dropdown-item>
                                        <Link :href="route('user.interview.index')" method="post" as="button" type="button" class="btn-logout dropdown-item d-flex align-items-center justify-content-between space-x-1">
                                            <span class="fs-sm fw-500">Interview</span>
                                            <i class="fa fa-fw fa-sign-out-alt opacity-25"></i>
                                        </Link>
                                    </el-dropdown-item>
                                    <el-dropdown-item>
                                        <Link :href="route('user.medical.index')" method="post" as="button" type="button" class="btn-logout dropdown-item d-flex align-items-center justify-content-between space-x-1">
                                            <span class="fs-sm fw-500">Interview</span>
                                            <i class="fa fa-fw fa-sign-out-alt opacity-25"></i>
                                        </Link>
                                    </el-dropdown-item> -->
                                    <el-dropdown-item>
                                        <Link :href="route('logout')" method="post" as="button" type="button" class="btn-logout dropdown-item d-flex align-items-center justify-content-between space-x-1">
                                            <span class="fs-sm fw-500">Keluar</span>
                                            <i class="fa fa-fw fa-sign-out-alt opacity-25"></i>
                                        </Link>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                        <template v-else>
                            <el-button type="primary" :tag="Link" class="my-md-auto"
                                :href="route('login')">
                                Masuk 
                            </el-button>
                            <el-button class="my-md-auto" :tag="Link" 
                                :href="route('register')">
                                Daftar 
                            </el-button>
                        </template>
                    </div>
                    <!-- END Right Section -->
                </div>
                <!-- END Header Content -->
            </header>

            <nav id="sidebar">
                <!-- Sidebar Content -->
                <div class="sidebar-content">
                    <!-- Side Header -->
                    <div class="content-header justify-content-lg-center">
                        <!-- Logo -->
                        <!-- <div>
                            <a :href="route('home')">
                                <img src="/images/logo/logo.png" width="150px"/>
                            </a>
                        </div> -->
                        <!-- END Logo -->

                        <!-- Options -->
                        <div>
                            <!-- Close Sidebar, Visible only on mobile screens -->
                            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                            <button type="button" class="btn btn-sm btn-alt-danger d-lg-none" @click="sidebar = false">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                            <!-- END Close Sidebar -->
                        </div>
                        <!-- END Options -->
                    </div>
                    <!-- END Side Header -->

                    <!-- Sidebar Scrolling -->
                    <div class="js-sidebar-scroll" data-simplebar="init">
                        <div class="simplebar-wrapper" style="margin: 0px;">
                            <div class="simplebar-height-auto-observer-wrapper">
                                <div class="simplebar-height-auto-observer"></div>
                            </div>
                            <div class="simplebar-mask">
                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                    <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                        aria-label="scrollable content" style="height: 100%; overflow: hidden;">
                                        <div class="simplebar-content" style="padding: 0px;">
                                            <!-- Side Main Navigation -->
                                            <div class="content-side content-side-full">
                                                <ul class="nav-main">
                                                    <li class="nav-main-item">
                                                        <a class="nav-main-link" href="#program">
                                                            <span class="nav-main-link-name">Program</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-main-item">
                                                        <a class="nav-main-link" href="#harga">
                                                            <span class="nav-main-link-name">Harga</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-main-item">
                                                        <a class="nav-main-link" href="#lokasi">
                                                            <span class="nav-main-link-name">Lokasi Kami</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- END Side Main Navigation -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="simplebar-placeholder" style="width: 390px; height: 706px;"></div>
                        </div>
                        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                            <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                        </div>
                        <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                            <div class="simplebar-scrollbar"
                                style="height: 0px; display: none; transform: translate3d(0px, 0px, 0px);"></div>
                        </div>
                    </div>
                    <!-- END Sidebar Scrolling -->
                </div>
                <!-- Sidebar Content -->
            </nav>

            <!-- Main Container -->
            <div id="main-container">
                <slot />
            </div>
            <!-- END Main Container -->

            <Footer/>
        </div>
    </el-config-provider>
</template>


<script setup>
    import Footer from './Footer.vue';
    import id from 'element-plus/dist/locale/id.mjs'
    import { ElConfigProvider } from 'element-plus'
    import { ref, computed } from 'vue'
    import { Link } from '@inertiajs/vue3'


    const sidebar = ref(false);
    const limitPosition = 500;
    const scrolled = ref(false);
    const lastPosition = ref(0);
    const locale = id;
    const zIndex = 3000;
    const size = 'small';

    
    const classContainer = computed(() => {
        return {
            'side-scroll': true,
            'main-content-boxed': true,
            'side-trans-enabled': true,
            'page-header-fixed': scrolled.value,
            'sidebar-o-xs': sidebar.value,
        }
    });

    const handleScroll = () => {
        if (100 < window.scrollY) {
            scrolled.value = true;
        } else {
            scrolled.value = false;
        }
        lastPosition.value = window.scrollY;
    };

    window.addEventListener("scroll", handleScroll);
</script>
