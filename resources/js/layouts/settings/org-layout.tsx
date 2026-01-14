import { NavItem, VerticalMenu } from "@/components/vertical-menu";
import { TenantedSharedData } from "@/types";
import OrganizationTimeOffTypesController from "@/wayfinder/actions/App/Http/Controllers/OrganizationTimeOffTypesController";
import { edit as generalEdit } from "@/wayfinder/routes/tenant/settings/organization";
import { usePage } from "@inertiajs/react";
import { type ReactNode } from "react";

interface OrgLayoutProps {
    children: ReactNode;
}

export default function OrgSettingsLayout({ children }: OrgLayoutProps) {
    const { url, component } = usePage<TenantedSharedData>();

    const items: NavItem[] = [
        {
            label: "General",
            href: generalEdit().url,
            active: component === "org-settings-general/edit",
        },
        {
            label: "Time Off Types",
            href: OrganizationTimeOffTypesController.index().url,
            active: url.startsWith("/org/settings/time-off-types"),
        },
        {
            label: "Offices",
            href: "hello",
            active: url.startsWith("/org/settings/fdf"),
        },
    ];

    return (
        <>
            <div className="min-h-0 w-full shrink-0 space-y-6 xl:sticky xl:top-40 xl:ml-auto xl:w-60">
                <h2 className="text-strong ml-3 text-base/8 font-medium xl:block xl:text-xl/8">
                    Settings
                </h2>
                <div className="flex w-full">
                    <VerticalMenu items={items} />
                </div>
            </div>
            <div className="mx-auto flex w-full shrink-0 flex-col xl:max-w-225">
                <div className="mt-0 flex flex-col gap-y-6 xl:mt-14">
                    {children}
                </div>
            </div>
            <div className="mr-auto hidden w-full xl:block xl:max-w-48"></div>
        </>
    );
}
