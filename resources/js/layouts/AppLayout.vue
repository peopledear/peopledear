<script setup lang="ts">
import UserMenu from "@/components/UserMenu.vue";

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
                <template #header="{ collapsed }">
                    <template v-if="collapsed">
                        <h2 class="text-lg font-bold">P</h2>
                    </template>
                    <template v-else>
                        <h2 class="text-lg font-bold">PeopleDear</h2>
                    </template>
                </template>

                <template #default="{ collapsed }"> Hello</template>
                <template #footer="{ collapsed }">
                    <UserMenu :collapsed="collapsed" />
                </template>
            </UDashboardSidebar>
            <slot />
        </UDashboardGroup>
    </UApp>
</template>

<style scoped></style>
