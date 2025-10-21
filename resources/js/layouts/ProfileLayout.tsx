import { Link } from "@inertiajs/react";
import { ReactNode } from "react";
import { AppLayout } from "./AppLayout";

interface ProfileLayoutProps {
    children: ReactNode;
}

const profileItems = [
    {
        label: "General",
        href: "/profile",
    },
    {
        label: "Security",
        href: "/profile/security",
    },
];

export function ProfileLayout({ children }: ProfileLayoutProps) {
    return (
        <AppLayout>
            <div className="mx-auto flex w-full max-w-7xl">
                <div className="lg:w-64">
                    <nav className="w-48">
                        <ul className="space-y-1">
                            {profileItems.map((item) => (
                                <li key={item.href}>
                                    <Link
                                        href={item.href}
                                        className="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-gray-100"
                                    >
                                        {item.label}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </nav>
                </div>
                <div className="lg:flex-1">{children}</div>
            </div>
        </AppLayout>
    );
}
