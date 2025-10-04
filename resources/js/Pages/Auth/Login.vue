<script setup lang="ts">
import { store } from "@/actions/App/Http/Controllers/Auth/LoginController";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const showPassword = ref(false);

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(store().url, {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <div>
        <AuthLayout>
            <template #header>
                <div>Welcome to PeopleDear</div>
                <div>Don't have an account? <a href="">Register</a></div>
            </template>

            <form @submit.prevent="submit" class="flex flex-col gap-y-6">
                <UFormField label="Email" required :error="form.errors.email">
                    <UInput
                        v-model="form.email"
                        name="email"
                        placeholder="Enter your email"
                        width="full"
                        class="w-full"
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
                        placeholder="Enter your password"
                        width="full"
                        class="w-full"
                        :type="showPassword ? 'text' : 'password'"
                        :ui="{ trailing: 'pe-1' }"
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
                <UFormField>
                    <UCheckbox v-model="form.remember" label="Remember me" />
                </UFormField>
                <UButton
                    type="submit"
                    label="Continue"
                    block
                    :loading="form.processing"
                />
            </form>
        </AuthLayout>
    </div>
</template>

<style scoped></style>
