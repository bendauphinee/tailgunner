import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

/* Set up Font Awesome */
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import { faTrashCan, faClone } from '@fortawesome/free-regular-svg-icons'
library.add(faTrashCan, faClone)

import { faSort, faPlus, faEye, faGripVertical, faTimes, faTriangleExclamation, faCircleCheck, faSpinner } from '@fortawesome/free-solid-svg-icons'
library.add(faSort, faPlus, faEye, faGripVertical, faTimes, faTriangleExclamation, faCircleCheck, faSpinner)

import FlashMessage from '@/Components/FlashMessage.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('font-awesome-icon', FontAwesomeIcon)
            .component('FlashMessage', FlashMessage)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
