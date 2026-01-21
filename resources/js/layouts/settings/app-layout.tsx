import { NavItem, VerticalMenu } from "@/components/vertical-menu";
import { TenantedSharedData } from "@/types";
import { edit as editAppearance } from "@/wayfinder/routes/tenant/user/settings/appearance";
import { edit as editPassword } from "@/wayfinder/routes/tenant/user/settings/password";
import { show } from "@/wayfinder/routes/tenant/user/settings/two-factor";
import { edit } from "@/wayfinder/routes/tenant/user/settings/user-profile";
import { usePage } from "@inertiajs/react";
import { PropsWithChildren } from "react";

export default function UserSettingsLayout({ children }: PropsWithChildren) {
    const { props, url } = usePage<TenantedSharedData>();

    const items: NavItem[] = [
        {
            label: "Profile",
            href: edit(props.tenant.identifier).url,
            active: url.startsWith("/settings/profile"),
        },
        {
            label: "Password",
            href: editPassword(props.tenant.identifier).url,
            active: url.startsWith("/settings/password"),
        },
        {
            label: "Two-Factor Auth",
            href: show(props.tenant.identifier).url,
            active: url.startsWith("/settings/two-factor"),
        },
        {
            label: "Appearance",
            href: editAppearance(props.tenant.identifier).url,
            active: url.startsWith("/settings/appearance"),
        },
    ];

    return (
        <>
            <div className="min-h-0 w-full shrink-0 space-y-6 xl:sticky xl:top-28 xl:ml-auto xl:w-60">
                <h2 className="text-strong ml-3 hidden text-base/8 font-medium sm:block sm:text-xl/8">
                    Settings
                </h2>
                <div className="flex w-full">
                    <VerticalMenu items={items} />
                </div>
            </div>
            <div className="mx-auto flex w-full max-w-225 shrink-0 flex-col">
                <div className="mt-0 flex flex-col gap-y-6 sm:mt-14">
                    {children}
                </div>
            </div>
            <div className="mr-auto hidden w-full max-w-48 sm:block"></div>
        </>
    );
}
