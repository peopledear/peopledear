<script setup lang="ts"></script>

<template>
    <UApp>
        <div class="flex h-screen items-center justify-center">
            <div
                class="flex w-full max-w-sm rounded-lg border border-gray-200 bg-slate-50"
            >
                <div class="flex flex-1 flex-col gap-x-8 gap-y-4 p-4 sm:p-6">
                    <slot name="logo" />
                    <div>
                        <slot name="header" />
                    </div>
                    <slot />
                </div>
            </div>
        </div>
    </UApp>
</template>

<style scoped></style>
