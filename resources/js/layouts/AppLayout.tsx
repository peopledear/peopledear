import { UserMenu } from "@/components/UserMenu";
import { Link, usePage } from "@inertiajs/react";
import { Home, Users } from "lucide-react";
import { ReactNode } from "react";

interface AppLayoutProps {
    children: ReactNode;
}

export function AppLayout({ children }: AppLayoutProps) {
    const { auth } = usePage().props as { auth: { user: any } };
    const isAdmin = auth.user?.role?.name === "admin";

    const navigationItems = [
        {
            label: "Dashboard",
            href: "/dashboard",
            icon: Home,
        },
        ...(isAdmin
            ? [
                  {
                      label: "Users",
                      href: "/admin/users",
                      icon: Users,
                  },
              ]
            : []),
    ];

    return (
        <div className="flex h-screen">
            {/* Sidebar */}
            <aside className="flex w-64 flex-col border-r border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950">
                {/* Header */}
                <div className="border-b border-gray-200 p-4 dark:border-gray-800">
                    <h2 className="text-lg font-bold">PeopleDear</h2>
                </div>

                {/* Navigation */}
                <nav className="flex-1 overflow-y-auto p-4">
                    <ul className="space-y-2">
                        {navigationItems.map((item) => (
                            <li key={item.href}>
                                <Link
                                    href={item.href}
                                    className="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-gray-100"
                                >
                                    <item.icon className="h-4 w-4" />
                                    {item.label}
                                </Link>
                            </li>
                        ))}
                    </ul>
                </nav>

                {/* Footer */}
                <div className="border-t border-gray-200 p-4 dark:border-gray-800">
                    <UserMenu collapsed={false} />
                </div>
            </aside>

            {/* Main Content */}
            <main className="flex-1 overflow-y-auto">{children}</main>
        </div>
    );
}
