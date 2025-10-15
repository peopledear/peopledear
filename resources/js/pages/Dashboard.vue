<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
</script>

<template>
    <AppLayout>
        <UDashboardPanel id="dashboard">
            <template #header>
                <UDashboardNavbar title="Dashboard" :shortcuts="['D']">
                    <template #leading>
                        <UDashboardSidebarCollapse />
                    </template>
                </UDashboardNavbar>
            </template>
        </UDashboardPanel>
    </AppLayout>
</template>

<style scoped></style>
