import {createInertiaApp} from "@inertiajs/vue3";
import { createApp, h } from "vue";
import type { DefineComponent } from "vue";

createInertiaApp({
    resolve: async (name: string) => {
        const pages = import.meta.glob<{default: DefineComponent}>(
            ['./Pages/**/*.vue'],
            { eager: true }
        )

        const page: {default: DefineComponent} | undefined = pages[`./Pages/${name}.vue`]

        if (!page) {
            throw new Error(`Page "${name}" not found`)
        }

        return page.default
    },

    setup({ el, App, props, plugin }) {
        const app = createApp({render: () => h(App, props)});

        app.use(plugin).mount(el);
    }
});
