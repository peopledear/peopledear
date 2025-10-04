<script setup lang="ts">
import AuthLayout from "@/Layouts/AuthLayout.vue";
import { ref } from "vue";

const showPassword = ref(false);

</script>

<template>
    <div>
        <AuthLayout>
            <template #header>
                <div>Welcome to PeopleDear</div>
                <div>Don't have an account? <a href="">Register</a></div>
            </template>
            <div class="flex flex-col gap-y-6">
                <UFormField label="Email" required>
                    <UInput name="email" placeholder="Enter your email" width="full" class="w-full" />
                </UFormField>
                <UFormField label="Password" required>
                    <UInput
                        name="password"
                        placeholder="Enter your password"
                        width="full"
                        class="w-full"
                        :type="showPassword ? 'text' : 'password'"
                        :ui="{trailing: 'pe-1'}"
                    >
                        <template #trailing>
                            <UButton
                                color="neutral"
                                variant="link"
                                size="sm"
                                :icon="showPassword ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                :aria-pressed="showPassword"
                                aria-controls="password"
                                @click="showPassword = !showPassword"
                            />
                        </template>
                    </UInput>
                </UFormField>
                <UFormField>
                    <UCheckbox label="Remember me"/>
                </UFormField>
                <UButton label="Continue" block />
            </div>
        </AuthLayout>
    </div>
</template>

<style scoped></style>
