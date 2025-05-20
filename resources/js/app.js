import "leaflet-draw/dist/leaflet.draw.css";
import "leaflet/dist/leaflet.css";
import "leaflet.markercluster/dist/MarkerCluster.css";
import "leaflet.markercluster/dist/MarkerCluster.Default.css";

import "primeicons/primeicons.css";
import "../css/app.css";
import "./bootstrap";

import LeafletPlugin from "@/Plugins/leaflet";
import { createInertiaApp } from "@inertiajs/vue3";
import Aura from "@primevue/themes/aura";
import { QueryClient, VueQueryPlugin } from "@tanstack/vue-query";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createPinia } from "pinia";
import "primeicons/primeicons.css";
import * as PrimeVueComponents from "primevue";
import { ToastService } from "primevue";
import PrimeVue from "primevue/config";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

const queryClient = new QueryClient();

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

const pinia = createPinia();
// pinia.use(piniaPluginPersistedstate);

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.use(VueQueryPlugin, { queryClient });
        app.use(LeafletPlugin);
        app.use(ZiggyVue);
        app.use(pinia);
        app.mixin({
            methods: {
                can(permission) {
                    return this.$page.props.auth.permissions.includes(
                        permission
                    );
                },
            },
        });
        app.use(ToastService);
        app.use(PrimeVue, {
            pt: true,
            theme: {
                preset: Aura,
                options: {
                    prefix: "p",
                    darkModeSelector: "ligth",
                    // cssLayer: false,
                },
            },
        });
        app.use(PrimeVueComponents.ConfirmationService);
        app.mount(el);
    },

    progress: {
        color: "#4B5563",
        showSpinner: true,
    },
});
