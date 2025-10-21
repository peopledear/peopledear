<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import { ref } from "vue";

const items = ref([
    {
        label: "General",
        to: "/settings",
    },
    {
        label: "Members",
        to: "/settings/members",
    },
    {
        label: "Roles",
        to: "/settings/roles",
    },
]);
</script>

<template>
    <AppLayout>
        <UDashboardPanel id="settings">
            <template #header>
                <UDashboardNavbar title="Settings" :shortcuts="['S']">
                    <template #leading>
                        <UDashboardSidebarCollapse />
                    </template>
                </UDashboardNavbar>
            </template>

            <template #body>
                <div class="mx-auto flex w-full max-w-7xl">
                    <div class="lg:w-64">
                        <UNavigationMenu
                            orientation="vertical"
                            :items="items"
                            class="data-[orientation=vertical]:w-48"
                        />
                    </div>
                    <div class="lg:flex-1">
                        <slot />
                    </div>
                </div>
            </template>
        </UDashboardPanel>
    </AppLayout>
</template>

<style scoped></style>
