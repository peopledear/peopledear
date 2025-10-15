import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import ui from "@nuxt/ui/vite";
import tailwindcss from "@tailwindcss/vite";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.ts"],
            refresh: true,
        }),
        wayfinder({
            command:
                "php artisan wayfinder:generate --path=resources/js/wayfinder",
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        ui({
            inertia: true,
        }),
        tailwindcss(),
    ],
});
