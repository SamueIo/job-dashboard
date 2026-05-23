import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import { createPinia } from 'pinia';
import { createApp, h } from 'vue'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    setup({ el, App, props, plugin }) {

        const pinia = createPinia()

        const app = createApp({
            render: () => h(App, props),
        })

        app.use(plugin)
        app.use(pinia)

        // ❗ SSR safe mount
        if (el) {
            app.mount(el)
        }

        return app
    },

        

    progress: {
        color: '#4B5563',
    },
});

if (typeof window !== 'undefined') {

    initializeTheme();

    initializeFlashToast();
}
