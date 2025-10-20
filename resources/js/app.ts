import { createInertiaApp } from "@inertiajs/vue3";
import ui from "@nuxt/ui/vue-plugin";
import type { DefineComponent } from "vue";
import { createApp, h } from "vue";
import "../css/app.css";

const appName = import.meta.env.VITE_APP_NAME || "Laravel Nuxt UI";

createInertiaApp({
    title: (title: string) => (title ? `${title} - ${appName}` : appName),
    resolve: async (name: string) => {
        const pages = import.meta.glob<{ default: DefineComponent }>(
            ["./pages/**/*.vue"],
            { eager: true },
        );

        const page: { default: DefineComponent } | undefined =
            pages[`./pages/${name}.vue`];

        if (!page) {
            throw new Error(`Page "${name}" not found`);
        }

        return page.default;
    },

    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(ui).use(plugin).mount(el);
    },
});
