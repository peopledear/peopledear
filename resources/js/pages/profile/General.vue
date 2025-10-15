<script setup lang="ts">
import AvatarSelector from "@/components/avatar/AvatarSelector.vue";
import ProfileLayout from "@/pages/profile/Layout.vue";
import type { User } from "@/types/shared.ts";
import { destroy } from "@/wayfinder/actions/App/Http/Controllers/Profile/UserAvatarController.ts";
import { update } from "@/wayfinder/actions/App/Http/Controllers/Profile/UserProfileController.ts";
import { router, useForm } from "@inertiajs/vue3";

interface GeneralProfilePageProps {
    user: User;
}

const props = defineProps<GeneralProfilePageProps>();

const form = useForm<{
    name: string;
    email: string;
    avatar: File | null | undefined;
    _method: string;
}>({
    name: props.user.name,
    email: props.user.email,
    avatar: null as File | null | undefined,
    _method: "PUT",
});

const submit = () => {
    form.post(update().url, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset("avatar");
            useToast().add({
                title: "Success",
                description: "Profile updated successfully",
                color: "success",
            });
        },
        onError: (errors) => {
            console.error("Validation errors:", errors);
        },
    });
};

const removeAvatar = () => {
    router.delete(destroy(), {
        preserveScroll: true,
        onSuccess: () => {
            useToast().add({
                title: "Success",
                description: "Avatar removed successfully",
                color: "success",
            });
        },
    });
};
</script>

<template>
    <ProfileLayout>
        <form id="settings" @submit.prevent="submit">
            <UPageCard
                title="General"
                description="General account settings related to your profile."
                variant="naked"
                orientation="horizontal"
                class="mb-4"
            >
                <UButton
                    form="settings"
                    label="Save changes"
                    color="neutral"
                    type="submit"
                    class="w-fit lg:ms-auto"
                    :loading="form.processing"
                    :disabled="form.processing"
                />
            </UPageCard>

            <UPageCard variant="subtle">
                <UFormField
                    name="name"
                    label="Name"
                    description="Your full name."
                    v-model="form.name"
                    required
                    :error="form.errors.name"
                    class="flex items-start justify-between gap-4 max-sm:flex-col"
                >
                    <UInput
                        v-model="form.name"
                        name="name"
                        autocomplete="off"
                        class="w-full lg:w-80"
                    />
                </UFormField>

                <USeparator />

                <UFormField
                    name="email"
                    label="Email"
                    description="The email address you use for authentication and notifications."
                    v-model="form.email"
                    required
                    :error="form.errors.email"
                    class="flex items-start justify-between gap-4 max-sm:flex-col"
                >
                    <UInput
                        v-model="form.email"
                        name="email"
                        type="email"
                        autocomplete="off"
                        class="w-full lg:w-80"
                    />
                </UFormField>

                <USeparator />

                <UFormField
                    name="avatar"
                    label="Profile photo"
                    description="Used for attribution on time offs requests and other events."
                    v-model="form.avatar"
                    class="flex items-start justify-between gap-4 max-sm:flex-col"
                >
                    <AvatarSelector
                        v-model="form.avatar"
                        :current-avatar-url="props.user.avatar.src"
                        :label="form.name"
                        :error="form.errors.avatar"
                        @deleteAvatar="removeAvatar"
                    />
                </UFormField>
            </UPageCard>
        </form>
    </ProfileLayout>
</template>

<style scoped></style>
