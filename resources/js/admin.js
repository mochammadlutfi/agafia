import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';


// import { ZiggyVue } from 'ziggy';
// import { Ziggy } from './ziggy';

import BaseLayout from './Layouts/AuthenticatedLayout.vue';
import dayjs from 'dayjs';
import 'dayjs/locale/id';
dayjs.locale('id');

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'AGAFIA ADDA MANDIRI';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Admin/${name}.vue`, import.meta.glob('./Admin/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue, Ziggy)
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
        .component('BaseLayout', BaseLayout);
        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});