import { Card, CardContent } from "@/components/ui/card";
import { ReactNode } from "react";

interface AuthLayoutProps {
    children: ReactNode;
    logo?: ReactNode;
    header?: ReactNode;
}

export function AuthLayout({ children, logo, header }: AuthLayoutProps) {
    return (
        <div className="flex h-screen items-center justify-center">
            <Card className="w-full max-w-sm">
                <CardContent className="flex flex-1 flex-col gap-x-8 gap-y-4 p-4 sm:p-6">
                    {logo}
                    {header && <div>{header}</div>}
                    {children}
                </CardContent>
            </Card>
        </div>
    );
}
