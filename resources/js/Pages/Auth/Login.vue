<script setup lang="ts">
import AuthLayout from "@/Layouts/AuthLayout.vue";
</script>

<template>
    <div>
        <AuthLayout>
            <template #header>
                <div>Welcome to PeopleDear</div>
                <div>Don't have an account? <a href="">Register</a></div>
            </template>
        </AuthLayout>
    </div>
</template>

<style scoped></style>
