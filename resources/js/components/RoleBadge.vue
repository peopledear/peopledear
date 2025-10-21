<script setup lang="ts">
import { computed } from "vue";

interface Role {
    id?: number;
    name: string;
    display_name: string;
}

interface RoleBadgeProps {
    role: Role | string;
}

const props = defineProps<RoleBadgeProps>();

const roleName = computed(() => {
    if (typeof props.role === "string") {
        return props.role;
    }
    return props.role.display_name || props.role.name;
});

const roleColor = computed(() => {
    const name =
        typeof props.role === "string"
            ? props.role.toLowerCase()
            : props.role.name.toLowerCase();

    switch (name) {
        case "admin":
        case "administrator":
            return "success";
        case "manager":
            return "primary";
        case "employee":
        case "developer":
            return "error";
        default:
            return "secondary";
    }
});
</script>

<template>
    <UBadge :color="roleColor" variant="subtle">
        {{ roleName }}
    </UBadge>
</template>

<style scoped></style>
