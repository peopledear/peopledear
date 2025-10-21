import { Badge } from "@/components/ui/badge";

interface Role {
    id?: number;
    name: string;
    display_name: string;
}

interface RoleBadgeProps {
    role: Role | string;
}

export function RoleBadge({ role }: RoleBadgeProps) {
    const roleName =
        typeof role === "string" ? role : role.display_name || role.name;

    const getRoleColor = (roleName: string) => {
        const name = roleName.toLowerCase();

        switch (name) {
            case "admin":
            case "administrator":
                return "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300";
            case "manager":
                return "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300";
            case "employee":
            case "developer":
                return "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300";
            default:
                return "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300";
        }
    };

    return <Badge className={getRoleColor(roleName)}>{roleName}</Badge>;
}
