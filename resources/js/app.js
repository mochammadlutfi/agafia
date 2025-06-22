import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { Vue3Mq } from "vue3-mq";

// import { ZiggyVue } from 'ziggy';
// import { Ziggy } from './ziggy';

// import ElementPlus from 'element-plus';
// import * as ElementPlusIconsVue from '@element-plus/icons-vue';
// import { Icon } from '@iconify/vue';
import BaseLayout from './Frontend/Layouts/BaseLayout.vue';
import UserLayout from './Layouts/UserLayout.vue';
import axios from 'axios';
import dayjs from 'dayjs';
import 'dayjs/locale/id';
dayjs.locale('id');
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'AGAFIA ADDA MANDIRI';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Frontend/${name}.vue`, import.meta.glob('./Frontend/**/*.vue')),
    setup({ el, App, props, plugin }) {
        el.removeAttribute("data-page");
        const app = createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue, Ziggy)
        .use(Vue3Mq)
        .mixin({
            methods : {
                currency(value){
                    if (typeof value !== "number") {
                        value = parseFloat(value);
                    }
                    var formatter = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    });
                    return formatter.format(value);
                },
            },
        })
        .component('BaseLayout', BaseLayout)
        .component('UserLayout', UserLayout);

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});