<script setup lang="ts">
import type { AvatarUploadProps } from "@/types/avatar.ts";
import { computed } from "vue";

const props = withDefaults(
    defineProps<AvatarUploadProps & { error?: string }>(),
    {
        label: "Avatar",
        description: "JPG, PNG or WebP. 2MB Max.",
        acceptedTypes: () => [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/webp",
        ],
        disabled: false,
        required: false,
    },
);

const emit = defineEmits<{
    "update:modelValue": [value: File | null | undefined];
    change: [value: File | null | undefined];
    remove: [];
    deleteAvatar: [];
}>();

const buttonLabel = computed(() => {
    return "Upload image";
});

const previewUrl = computed(() => {
    if (props.modelValue) return URL.createObjectURL(props.modelValue);
    return props.currentAvatarUrl ?? undefined;
});

const formatBytes = (bytes: number): string => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
};

const handleFileChange = (file: File | null | undefined) => {
    emit("update:modelValue", file);
    emit("change", file);
};

const handleDeleteAvatar = () => {
    emit("update:modelValue", null);
    emit("deleteAvatar");
};

const handleRemove = (removeFileFn: () => void) => {
    removeFileFn();
    emit("update:modelValue", null);
    emit("remove");
};
</script>

<template>
    <div>
        <UFileUpload
            v-slot="{ open, removeFile }"
            :modelValue="modelValue"
            :accept="acceptedTypes.join(',')"
            :disabled="disabled"
            @update:modelValue="handleFileChange"
        >
            <div
                class="flex min-w-56 flex-wrap items-center justify-between gap-4"
            >
                <UAvatar
                    size="3xl"
                    :src="previewUrl"
                    :alt="label"
                    :initials="label"
                />
                <UButton
                    :label="buttonLabel"
                    color="neutral"
                    variant="outline"
                    :disabled="disabled"
                    @click="open()"
                />

                <template v-if="currentAvatarUrl">
                    <UButton
                        color="neutral"
                        variant="ghost"
                        icon="i-lucide-trash"
                        :disabled="disabled"
                        @click="handleDeleteAvatar()"
                        class="text-gray-500 dark:text-gray-300"
                    />
                </template>
            </div>

            <p
                v-if="modelValue"
                class="mt-1.5 text-xs text-gray-600 dark:text-gray-400"
            >
                {{ modelValue.name }} {{ formatBytes(modelValue.size) }}
                <UButton
                    label="Remove"
                    color="error"
                    variant="link"
                    size="xs"
                    class="p-0"
                    :disabled="disabled"
                    @click="handleRemove(removeFile)"
                />
            </p>
        </UFileUpload>

        <!-- Display validation errors from Laravel -->
        <p v-if="error" class="text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </p>
    </div>
</template>

<style scoped></style>
