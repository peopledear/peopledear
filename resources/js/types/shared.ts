import type { Avatar } from "@/types/avatar.ts";

export interface Role {
    id: number;
    name: string;
    display_name: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar: Avatar;
    role?: Role;
}

export interface SharedProps {
    auth: {
        user?: User;
    };
    errors: Record<string, string[]>;

    [key: string]: unknown;
}
