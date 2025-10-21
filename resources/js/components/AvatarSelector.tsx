import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Trash2, Upload } from "lucide-react";
import { useRef, useState } from "react";

interface AvatarSelectorProps {
    modelValue?: File | null;
    currentAvatarUrl?: string | null;
    label?: string;
    description?: string;
    acceptedTypes?: string[];
    disabled?: boolean;
    error?: string;
    onChange?: (file: File | null) => void;
    onDelete?: () => void;
}

export function AvatarSelector({
    modelValue,
    currentAvatarUrl,
    label = "Avatar",
    description = "JPG, PNG or WebP. 2MB Max.",
    acceptedTypes = ["image/jpeg", "image/jpg", "image/png", "image/webp"],
    disabled = false,
    error,
    onChange,
    onDelete,
}: AvatarSelectorProps) {
    const fileInputRef = useRef<HTMLInputElement>(null);
    const [previewUrl, setPreviewUrl] = useState<string | null>(null);

    const formatBytes = (bytes: number): string => {
        if (bytes === 0) return "0 Bytes";
        const k = 1024;
        const sizes = ["Bytes", "KB", "MB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return (
            Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i]
        );
    };

    const handleFileChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0] || null;
        if (file) {
            const url = URL.createObjectURL(file);
            setPreviewUrl(url);
        }
        onChange?.(file);
    };

    const handleRemove = () => {
        if (fileInputRef.current) {
            fileInputRef.current.value = "";
        }
        setPreviewUrl(null);
        onChange?.(null);
    };

    const handleDeleteAvatar = () => {
        handleRemove();
        onDelete?.();
    };

    const openFileDialog = () => {
        fileInputRef.current?.click();
    };

    const displayUrl = previewUrl || currentAvatarUrl;

    return (
        <div className="space-y-4">
            <div className="flex min-w-56 flex-wrap items-center justify-between gap-4">
                <Avatar className="h-20 w-20">
                    <AvatarImage src={displayUrl || undefined} alt={label} />
                    <AvatarFallback className="text-lg">
                        {label.charAt(0).toUpperCase()}
                    </AvatarFallback>
                </Avatar>

                <div className="flex flex-col gap-2">
                    <Button
                        type="button"
                        variant="outline"
                        disabled={disabled}
                        onClick={openFileDialog}
                        className="w-full"
                    >
                        <Upload className="mr-2 h-4 w-4" />
                        Upload image
                    </Button>

                    {currentAvatarUrl && (
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            disabled={disabled}
                            onClick={handleDeleteAvatar}
                            className="text-gray-500 hover:text-gray-700"
                        >
                            <Trash2 className="h-4 w-4" />
                        </Button>
                    )}
                </div>
            </div>

            <input
                ref={fileInputRef}
                type="file"
                accept={acceptedTypes.join(",")}
                onChange={handleFileChange}
                className="hidden"
                disabled={disabled}
            />

            {modelValue && (
                <p className="text-xs text-gray-600 dark:text-gray-400">
                    {modelValue.name} {formatBytes(modelValue.size)}
                    <Button
                        type="button"
                        variant="link"
                        size="sm"
                        className="ml-2 p-0 text-red-600"
                        disabled={disabled}
                        onClick={handleRemove}
                    >
                        Remove
                    </Button>
                </p>
            )}

            {error && (
                <p className="text-sm text-red-600 dark:text-red-400">
                    {error}
                </p>
            )}
        </div>
    );
}
