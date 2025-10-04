<script setup lang="ts">
import { ref } from "vue";

const open = ref(true);
</script>

<template>
    <UApp>
        <UDashboardGroup unit="rem">
            <UDashboardSidebar
                id="default"
                v-model:open="open"
                collapsible
                resizable
            >
                <template #header>
                    <h2 class="text-lg font-bold">PeopleDear</h2>
                </template>
            </UDashboardSidebar>
            <slot />
            <UDashboardPanel id="customers">
                <template #header>Dashboard!</template>
            </UDashboardPanel>
        </UDashboardGroup>
    </UApp>
</template>

<style scoped></style>
