<script setup lang="ts">
import RoleBadge from "@/components/RoleBadge.vue";
import AuthLayout from "@/layouts/AuthLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

interface AcceptInvitationPageProps {
    invitation: {
        email: string;
        role: string;
        token: string;
    };
}

const props = defineProps<AcceptInvitationPageProps>();

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const form = useForm({
    name: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(`/invitation/${props.invitation.token}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div>
        <AuthLayout>
            <template #header>
                <div class="text-xl font-semibold">Accept Your Invitation</div>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    You've been invited to join PeopleDear
                </div>
            </template>

            <div
                class="mb-6 flex flex-col gap-2 rounded-lg bg-gray-100 p-4 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Email
                    </span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ invitation.email }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span
                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Role
                    </span>
                    <RoleBadge :role="invitation.role" />
                </div>
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-y-6">
                <UFormField
                    label="Full Name"
                    required
                    :error="form.errors.name"
                >
                    <UInput
                        v-model="form.name"
                        name="name"
                        placeholder="Enter your full name"
                        width="full"
                        class="w-full"
                        autocomplete="name"
                    />
                </UFormField>

                <UFormField
                    label="Password"
                    required
                    :error="form.errors.password"
                >
                    <UInput
                        v-model="form.password"
                        name="password"
                        placeholder="Create a password"
                        width="full"
                        class="w-full"
                        :type="showPassword ? 'text' : 'password'"
                        :ui="{ trailing: 'pe-1' }"
                        autocomplete="new-password"
                    >
                        <template #trailing>
                            <UButton
                                color="neutral"
                                variant="link"
                                size="sm"
                                :icon="
                                    showPassword
                                        ? 'i-lucide-eye-off'
                                        : 'i-lucide-eye'
                                "
                                :aria-label="
                                    showPassword
                                        ? 'Hide password'
                                        : 'Show password'
                                "
                                :aria-pressed="showPassword"
                                aria-controls="password"
                                @click="showPassword = !showPassword"
                            />
                        </template>
                    </UInput>
                </UFormField>

                <UFormField
                    label="Confirm Password"
                    required
                    :error="form.errors.password_confirmation"
                >
                    <UInput
                        v-model="form.password_confirmation"
                        name="password_confirmation"
                        placeholder="Confirm your password"
                        width="full"
                        class="w-full"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        :ui="{ trailing: 'pe-1' }"
                        autocomplete="new-password"
                    >
                        <template #trailing>
                            <UButton
                                color="neutral"
                                variant="link"
                                size="sm"
                                :icon="
                                    showPasswordConfirmation
                                        ? 'i-lucide-eye-off'
                                        : 'i-lucide-eye'
                                "
                                :aria-label="
                                    showPasswordConfirmation
                                        ? 'Hide password'
                                        : 'Show password'
                                "
                                :aria-pressed="showPasswordConfirmation"
                                aria-controls="password_confirmation"
                                @click="
                                    showPasswordConfirmation =
                                        !showPasswordConfirmation
                                "
                            />
                        </template>
                    </UInput>
                </UFormField>

                <UButton
                    type="submit"
                    label="Create Account"
                    block
                    :loading="form.processing"
                />
            </form>
        </AuthLayout>
    </div>
</template>

<style scoped></style>
