<script setup lang="ts">
import UserMenu from "@/components/UserMenu.vue";
import { usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const open = ref(true);
const page = usePage();

const isAdmin = computed(() => {
    return page.props.auth?.user?.role?.name === "admin";
});

const navigationItems = computed(() => {
    const items = [
        {
            label: "Dashboard",
            to: "/dashboard",
            icon: "i-heroicons-home",
        },
    ];

    if (isAdmin.value) {
        items.push({
            label: "Users",
            to: "/admin/users",
            icon: "i-heroicons-users",
        });
    }

    return items;
});
</script>

<template>
    <UApp>
        <div class="flex h-screen">
            <!-- Custom Sidebar (replacing UDashboardSidebar) -->
            <aside
                class="flex w-64 flex-col border-r border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950"
            >
                <!-- Header -->
                <div class="border-b border-gray-200 p-4 dark:border-gray-800">
                    <h2 class="text-lg font-bold">PeopleDear</h2>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto p-4">
                    <UNavigationMenu
                        :items="navigationItems"
                        orientation="vertical"
                    />
                </nav>

                <!-- Footer -->
                <div class="border-t border-gray-200 p-4 dark:border-gray-800">
                    <UserMenu :collapsed="false" />
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto">
                <slot />
            </main>
        </div>
    </UApp>
</template>

<style scoped></style>
