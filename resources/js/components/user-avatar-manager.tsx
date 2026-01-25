import InputError from "@/components/input-error";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { useInitials } from "@/hooks/use-initials";
import { cn } from "@/lib/utils";
import UserAvatarController from "@/wayfinder/actions/App/Http/Controllers/UserAvatarController";
import { useForm } from "@inertiajs/react";
import { Trash2 } from "lucide-react";
import { useCallback, useEffect, useId, useRef, useState } from "react";

type UserAvatarManagerProps = {
    tenantIdentifier: string | number;
    userName: string;
    avatarUrl?: string | null;
    initialError?: string;
    className?: string;
};

export default function UserAvatarManager({
    tenantIdentifier,
    userName,
    avatarUrl,
    initialError,
    className,
}: UserAvatarManagerProps) {
    const fileInputRef = useRef<HTMLInputElement>(null);
    const [previewUrl, setPreviewUrl] = useState<string | null>(null);
    const uploadForm = useForm<{ avatar: File | null }>({
        avatar: null,
    });
    const deleteForm = useForm({});
    const initials = useInitials();
    const inputId = useId();

    useEffect(() => {
        return () => {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }
        };
    }, [previewUrl]);

    const resetInput = useCallback(() => {
        if (fileInputRef.current) {
            fileInputRef.current.value = "";
        }
    }, []);

    const handleUpload = (file: File) => {
        uploadForm.clearErrors();
        uploadForm.transform(() => ({
            avatar: file,
        }));

        uploadForm.post(UserAvatarController.store(tenantIdentifier).url, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                setPreviewUrl(null);
                uploadForm.clearErrors();
            },
            onError: (errors) => {
                if (typeof errors.avatar !== "string") {
                    uploadForm.setError("avatar", "Unable to upload avatar.");
                }
            },
            onFinish: () => {
                resetInput();
                uploadForm.reset();
                uploadForm.transform((data) => data);
            },
        });
    };

    const handleFileChange = (
        event: React.ChangeEvent<HTMLInputElement>,
    ): void => {
        const file = event.target.files?.[0];

        if (!file) {
            return;
        }

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }

        const objectUrl = URL.createObjectURL(file);
        setPreviewUrl(objectUrl);

        handleUpload(file);
    };

    const handleDelete = (): void => {
        setPreviewUrl(null);
        uploadForm.clearErrors();

        deleteForm.delete(UserAvatarController.destroy(tenantIdentifier).url, {
            preserveScroll: true,
            onSuccess: () => {
                uploadForm.clearErrors();
            },
            onError: () => {
                uploadForm.setError("avatar", "Unable to delete avatar.");
            },
            onFinish: () => {
                resetInput();
            },
        });
    };

    const openFileDialog = (): void => {
        fileInputRef.current?.click();
    };

    const isBusy = uploadForm.processing || deleteForm.processing;
    const displayImage = previewUrl ?? avatarUrl ?? undefined;
    const hasAvatar = Boolean(displayImage);
    const currentError = uploadForm.errors.avatar ?? initialError ?? undefined;

    return (
        <div className={cn("flex flex-col gap-2", className)}>
            <div className="flex items-center gap-4">
                <Avatar className="size-16 overflow-hidden rounded-full">
                    <AvatarImage src={displayImage} alt={userName} />
                    <AvatarFallback className="bg-muted text-foreground text-lg">
                        {initials(userName)}
                    </AvatarFallback>
                </Avatar>

                <label htmlFor={inputId} className="sr-only">
                    Upload avatar
                </label>
                <input
                    ref={fileInputRef}
                    id={inputId}
                    type="file"
                    className="sr-only"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    onChange={handleFileChange}
                />

                <Button
                    type="button"
                    variant="outline"
                    onClick={openFileDialog}
                    disabled={isBusy}
                >
                    {uploadForm.processing ? "Uploading..." : "Upload image"}
                </Button>

                {hasAvatar && (
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        onClick={handleDelete}
                        disabled={isBusy}
                    >
                        <Trash2 className="size-4" />
                        <span className="sr-only">Remove avatar</span>
                    </Button>
                )}
            </div>
            <InputError message={currentError} />
        </div>
    );
}
