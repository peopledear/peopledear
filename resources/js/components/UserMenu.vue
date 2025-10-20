<script setup lang="ts">
import type { SharedProps } from "@/types/shared.ts";
import { store } from "@/wayfinder/actions/App/Http/Controllers/Auth/LogoutController";
import { index } from "@/wayfinder/actions/App/Http/Controllers/Profile/UserProfileController";
import { router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

defineProps<{
    collapsed?: boolean;
}>();

const page = usePage<SharedProps>();

const logout = () => {
    router.visit(store().url, {
        method: "post",
    });
};

const goToProfile = () => {
    router.visit(index().url, {
        preserveScroll: true,
    });
};

const items = computed(() => [
    [
        {
            type: "label",
            label: page.props.auth.user?.name,
            avatar: page.props.auth.user?.avatar,
        },
    ],
    [
        {
            label: "Account",
            icon: "i-lucide-user",
            onSelect() {
                goToProfile();
            },
        },
    ],
    [
        {
            label: "Logout",
            icon: "i-lucide-log-out",
            onSelect(e: Event) {
                logout();
            },
        },
    ],
]);
</script>

<template>
    <UDropdownMenu
        :items="items"
        :content="{ align: 'center', collisionPadding: 12 }"
        :ui="{
            content: collapsed
                ? 'w-48'
                : 'w-(--reka-dropdown-menu-trigger-width)',
        }"
    >
        <UButton
            v-bind="{
                ...page.props.auth.user,
                label: collapsed ? undefined : page.props.auth.user?.name,
                trailingIcon: collapsed
                    ? undefined
                    : 'i-lucide-chevrons-up-down',
            }"
            color="neutral"
            variant="ghost"
            block
            :square="collapsed"
            class="data-[state=open]:bg-elevated"
            :ui="{ trailingIcon: 'text-dimmed' }"
        />

        <template #chip-leading="{ item }">
            <span
                :style="{
                    '--chip-light': `var(--color-${(item as any).chip}-500)`,
                    '--chip-dark': `var(--color-${(item as any).chip}-400)`,
                }"
                class="ms-0.5 size-2 rounded-full bg-(--chip-light) dark:bg-(--chip-dark)"
            />
        </template>
    </UDropdownMenu>
</template>

<style scoped></style>
