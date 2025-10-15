import type { Avatar } from "@/types/avatar.ts";

export interface User {
    id: number;
    name: string;
    email: string;
    avatar: Avatar;
}

export interface SharedProps {
    auth: {
        user?: User;
    };
    errors: Record<string, string[]>;

    [key: string]: unknown;
}
