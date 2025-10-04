import "../css/app.css";
import { createInertiaApp } from "@inertiajs/vue3";
import { createApp, h } from "vue";
import type { DefineComponent } from "vue";
import ui from "@nuxt/ui/vue-plugin";

const appName = import.meta.env.VITE_APP_NAME || "Laravel Nuxt UI";

createInertiaApp({
    title: (title: string) => (title ? `${title} - ${appName}` : appName),
    resolve: async (name: string) => {
        const pages = import.meta.glob<{ default: DefineComponent }>(
            ["./Pages/**/*.vue"],
            { eager: true },
        );

        const page: { default: DefineComponent } | undefined =
            pages[`./Pages/${name}.vue`];

        if (!page) {
            throw new Error(`Page "${name}" not found`);
        }

        return page.default;
    },

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin).use(ui).mount(el);
    },
});
