export interface Avatar {
    path?: string;
    src?: string;
    alt?: string;
}

export interface AvatarUploadProps {
    modelValue?: File | null | undefined;
    currentAvatarUrl?: string | null;
    label?: string;
    description?: string;
    maxSizeMB?: number;
    minDimensions?: { width: number; height: number };
    maxDimensions?: { width: number; height: number };
    acceptedTypes?: string[];
    disabled?: boolean;
    required?: boolean;
}

export interface AvatarUploadEmits {
    (e: "update:modelValue", value: File | null | undefined): void;

    (e: "change", value: File | null | undefined): void;

    (e: "remove"): void;
}

export const AVATAR_DEFAULTS = {
    MAX_FILE_SIZE: 2 * 1024 * 1024, // 2MB
    MIN_DIMENSIONS: { width: 200, height: 200 },
    MAX_DIMENSIONS: { width: 4096, height: 4096 },
    ACCEPTED_IMAGE_TYPES: [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/webp",
    ],
} as const;
