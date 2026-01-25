import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { ChevronsUpDown, Settings } from "lucide-react";
import type { ButtonHTMLAttributes } from "react";

type TenantMenuProps = ButtonHTMLAttributes<HTMLButtonElement> & {
    name: string;
};

export function TenantMenu({ name }: TenantMenuProps) {
    return (
        <div className="flex items-center gap-1">
            <Button variant="ghost" size="sm">
                {name}
            </Button>

            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <ChevronsUpDown className="size-4 text-[#a7a39b] dark:text-[#c9c5bc]" />
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    align="start"
                    sideOffset={12}
                    className="bg-weaker ring-weaker border-weak w-56"
                >
                    <DropdownMenuGroup>
                        <DropdownMenuItem className="gap-2">
                            <Settings className="size-4" />
                            <span>Settings</span>
                        </DropdownMenuItem>
                    </DropdownMenuGroup>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
}
