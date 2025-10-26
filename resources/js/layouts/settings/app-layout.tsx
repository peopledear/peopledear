import { NavItem, VerticalMenu } from "@/components/vertical-menu";
import { edit as editAppearance } from "@/routes/appearance";
import { edit as editPassword } from "@/routes/password";
import { show } from "@/routes/two-factor";
import { edit } from "@/routes/user-profile";
import { usePage } from "@inertiajs/react";
import { PropsWithChildren } from "react";

export default function UserSettingsLayout({ children }: PropsWithChildren) {
    const { url } = usePage();

    const items: NavItem[] = [
        {
            label: "Profile",
            href: edit().url,
            active: url.startsWith("/settings/profile"),
        },
        {
            label: "Password",
            href: editPassword().url,
            active: url.startsWith("/settings/password"),
        },
        {
            label: "Two-Factor Auth",
            href: show().url,
            active: url.startsWith("/settings/two-factor"),
        },
        {
            label: "Appearance",
            href: editAppearance().url,
            active: url.startsWith("/settings/appearance"),
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
