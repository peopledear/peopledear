import { NavItem, VerticalMenu } from "@/components/vertical-menu";
import OrganizationTimeOffTypesController from "@/wayfinder/actions/App/Http/Controllers/OrganizationTimeOffTypesController";
import { edit as generalEdit } from "@/wayfinder/routes/org/settings/organization";
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
            label: "Time Off Types",
            href: OrganizationTimeOffTypesController.index().url,
            active: url.startsWith("/org/time-off-types"),
        },
        {
            label: "Offices",
            href: "hello",
            active: url.startsWith("/org/settings/fdf"),
        },
    ];

    return (
        <>
            <div className="min-h-0 w-full shrink-0 space-y-6 sm:w-[192px] lg:sticky lg:ml-auto">
                <h2 className="text-strong ml-3 text-base/8 font-medium lg:block lg:text-xl/8">
                    Settings
                </h2>
                <div className="flex w-full">
                    <VerticalMenu items={items} />
                </div>
            </div>
            <div className="mx-auto flex w-full shrink-0 flex-col lg:max-w-[900px]">
                <div className="mt-0 flex flex-col gap-y-6 lg:mt-14">
                    {children}
                </div>
            </div>
            <div className="mr-auto hidden w-full lg:block lg:max-w-[192px]"></div>
        </>
    );
}
