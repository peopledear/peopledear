<script setup lang="ts">
import RoleBadge from "@/components/RoleBadge.vue";
import { router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

interface Role {
    id: number;
    name: string;
    display_name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    avatar?: {
        src: string;
    };
    role?: Role;
    is_active: boolean;
}

interface MemberCardProps {
    user: User;
    roles: Role[];
}

const props = defineProps<MemberCardProps>();
const page = usePage();

const isCurrentUser = computed(() => {
    return page.props.auth?.user?.id === props.user.id;
});

const dropdownItems = computed(() => {
    const items = [
        [
            {
                label: "Change Role",
                icon: "i-heroicons-users",
                children: props.roles.map((role) => ({
                    label: role.display_name,
                    click: () => changeRole(role.id),
                    disabled: props.user.role?.id === role.id,
                })),
            },
        ],
    ];

    if (!isCurrentUser.value) {
        items.push([
            {
                label: props.user.is_active ? "Deactivate" : "Activate",
                icon: props.user.is_active
                    ? "i-heroicons-x-circle"
                    : "i-heroicons-check-circle",
                click: () => toggleUserStatus(),
            },
        ]);
    }

    return items;
});

const changeRole = (roleId: number) => {
    router.patch(
        `/admin/users/${props.user.id}/role`,
        { role_id: roleId },
        {
            preserveScroll: true,
            onSuccess: () => {
                useToast().add({
                    title: "Success",
                    description: "User role updated successfully",
                    color: "success",
                });
            },
        },
    );
};

const toggleUserStatus = () => {
    const url = props.user.is_active
        ? `/admin/users/${props.user.id}/deactivate`
        : `/admin/users/${props.user.id}/activate`;

    router.post(
        url,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                useToast().add({
                    title: "Success",
                    description: `User ${props.user.is_active ? "deactivated" : "activated"} successfully`,
                    color: "success",
                });
            },
        },
    );
};
</script>

<template>
    <div class="flex items-center justify-between gap-4 py-4">
        <div class="flex items-center gap-4">
            <UAvatar :alt="user.name" :src="user.avatar?.src" size="md" />
            <div>
                <div class="font-medium">
                    {{ user.name }}
                    <span
                        v-if="isCurrentUser"
                        class="text-sm font-normal text-gray-500 dark:text-gray-400"
                    >
                        (You)
                    </span>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ user.email }}
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <RoleBadge v-if="user.role" :role="user.role" />
            <UDropdownMenu :items="dropdownItems">
                <UButton
                    data-test="user-menu"
                    color="neutral"
                    variant="ghost"
                    icon="i-heroicons-ellipsis-horizontal"
                    square
                />
            </UDropdownMenu>
        </div>
    </div>
</template>

<style scoped></style>
