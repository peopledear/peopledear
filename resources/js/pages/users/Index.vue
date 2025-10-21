<script setup lang="ts">
import MemberCard from "@/components/MemberCard.vue";
import RoleBadge from "@/components/RoleBadge.vue";
import AppLayout from "@/layouts/AppLayout.vue";
import { router, useForm } from "@inertiajs/vue3";

interface Role {
    id: number;
    name: string;
    display_name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    avatar?: {
        src: string;
    };
    role?: Role;
    is_active: boolean;
}

interface Invitation {
    id: number;
    email: string;
    role: Role;
    inviter: {
        name: string;
    };
    expires_at: string;
}

interface UsersPageProps {
    users: {
        data: User[];
    };
    pendingInvitations: Invitation[];
    roles: Role[];
}

const props = defineProps<UsersPageProps>();

const invitationForm = useForm({
    email: "",
    role_id: null as number | null,
});

const submitInvitation = () => {
    invitationForm.post("/admin/invitations", {
        preserveScroll: true,
        onSuccess: () => {
            invitationForm.reset();
            useToast().add({
                title: "Success",
                description: "Invitation sent successfully",
                color: "success",
            });
        },
    });
};

const resendInvitation = (invitationId: number) => {
    router.post(
        `/admin/invitations/${invitationId}/resend`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                useToast().add({
                    title: "Success",
                    description: "Invitation resent successfully",
                    color: "success",
                });
            },
        },
    );
};

const revokeInvitation = (invitationId: number) => {
    router.delete(`/admin/invitations/${invitationId}`, {
        preserveScroll: true,
        onSuccess: () => {
            useToast().add({
                title: "Success",
                description: "Invitation revoked successfully",
                color: "success",
            });
        },
    });
};
</script>

<template>
    <AppLayout>
        <UDashboardPanel id="users">
            <template #header>
                <UDashboardNavbar title="Users" :shortcuts="['U']">
                    <template #leading>
                        <UDashboardSidebarCollapse />
                    </template>
                </UDashboardNavbar>
            </template>

            <template #body>
                <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <form id="invite-form" @submit.prevent="submitInvitation">
                        <UPageCard
                            title="Invite by email"
                            description="Add new members to your organization by sending them an invitation email."
                            variant="naked"
                            orientation="horizontal"
                            class="mb-4"
                        >
                            <UButton
                                form="invite-form"
                                label="Send invite"
                                color="neutral"
                                type="submit"
                                class="w-fit lg:ms-auto"
                                :loading="invitationForm.processing"
                                :disabled="invitationForm.processing"
                            />
                        </UPageCard>

                        <UPageCard variant="subtle" class="mb-8">
                            <UFormField
                                name="email"
                                label="Email"
                                description="The email address of the person you want to invite."
                                v-model="invitationForm.email"
                                required
                                :error="invitationForm.errors.email"
                                class="flex items-start justify-between gap-4 max-sm:flex-col"
                            >
                                <UInput
                                    v-model="invitationForm.email"
                                    name="email"
                                    type="email"
                                    placeholder="colleague@example.com"
                                    autocomplete="off"
                                    class="w-full lg:w-80"
                                />
                            </UFormField>

                            <USeparator />

                            <UFormField
                                name="role_id"
                                label="Role"
                                description="The role this person will have in your organization."
                                v-model="invitationForm.role_id"
                                required
                                :error="invitationForm.errors.role_id"
                                class="flex items-start justify-between gap-4 max-sm:flex-col"
                            >
                                <USelect
                                    v-model="invitationForm.role_id"
                                    name="role_id"
                                    :options="
                                        roles.map((role) => ({
                                            label: role.display_name,
                                            value: role.id,
                                        }))
                                    "
                                    placeholder="Select a role"
                                    class="w-full lg:w-80"
                                />
                            </UFormField>
                        </UPageCard>
                    </form>

                    <UPageCard
                        title="Pending invitations"
                        description="Invitations that have been sent but not yet accepted."
                        variant="naked"
                        orientation="horizontal"
                        class="mb-4"
                    />

                    <UPageCard
                        v-if="pendingInvitations.length > 0"
                        variant="subtle"
                        class="mb-8"
                    >
                        <div
                            v-for="(invitation, index) in pendingInvitations"
                            :key="invitation.id"
                        >
                            <div
                                class="flex items-center justify-between gap-4 py-4"
                            >
                                <div class="flex items-center gap-4">
                                    <UAvatar
                                        :alt="invitation.email"
                                        size="md"
                                    />
                                    <div>
                                        <div class="font-medium">
                                            {{ invitation.email }}
                                        </div>
                                        <div
                                            class="text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            Invited by
                                            {{ invitation.inviter.name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <RoleBadge :role="invitation.role" />
                                    <UButton
                                        color="neutral"
                                        variant="ghost"
                                        label="Resend"
                                        @click="resendInvitation(invitation.id)"
                                    />
                                    <UButton
                                        color="neutral"
                                        variant="ghost"
                                        label="Revoke"
                                        @click="revokeInvitation(invitation.id)"
                                    />
                                </div>
                            </div>
                            <USeparator
                                v-if="index < pendingInvitations.length - 1"
                            />
                        </div>
                    </UPageCard>

                    <UAlert
                        v-else
                        color="gray"
                        variant="subtle"
                        title="No pending invitations"
                        description="There are no pending invitations at the moment."
                        class="mb-8"
                    />

                    <UPageCard
                        title="Organization members"
                        description="People who are currently members of your organization."
                        variant="naked"
                        orientation="horizontal"
                        class="mb-4"
                    />

                    <UPageCard variant="subtle">
                        <div v-for="(user, index) in users.data" :key="user.id">
                            <MemberCard :user="user" :roles="roles" />
                            <USeparator v-if="index < users.data.length - 1" />
                        </div>
                    </UPageCard>
                </div>
            </template>
        </UDashboardPanel>
    </AppLayout>
</template>

<style scoped></style>
