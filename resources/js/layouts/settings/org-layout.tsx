import { NavItem, VerticalMenu } from "@/components/vertical-menu";
import { edit as generalEdit } from "@/routes/org/settings/organization";
import { usePage } from "@inertiajs/react";
import { type ReactNode } from "react";

interface OrgLayoutProps {
    children: ReactNode;
}

export default function OrgSettingsLayout({ children }: OrgLayoutProps) {
    const { url, component } = usePage();

    const items: NavItem[] = [
        {
            label: "General",
            href: generalEdit().url,
            active: component === "org-settings-general/edit",
        },
        {
            label: "Offices",
            href: "hello",
            active: url.startsWith("/org/settings/fdf"),
        },
    ];

    return (
        <>
            <div className="min-h-0 w-full shrink-0 space-y-6 sm:sticky sm:ml-auto sm:w-[192px]">
                <h2 className="text-strong ml-3 hidden text-base/8 font-medium sm:block sm:text-xl/8">
                    Settings
                </h2>
                <div className="flex w-full">
                    <VerticalMenu items={items} />
                </div>
            </div>
            <div className="mx-auto flex w-full max-w-[900px] shrink-0 flex-col">
                <div className="mt-0 flex flex-col gap-y-6 sm:mt-14">
                    {children}
                </div>
            </div>
            <div className="mr-auto hidden w-full max-w-[192px] sm:block"></div>
        </>
    );
}
