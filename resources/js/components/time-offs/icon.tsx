import { cn } from "@/lib/utils";
import { DynamicIcon, IconName } from "lucide-react/dynamic";

interface IconProps {
    name: string;
    size?: string;
}

export default function Icon({ name, size = "size-4" }: IconProps) {
    return (
        <div className="border-border bg-muted text-foreground flex items-center justify-center rounded-sm border p-2.5">
            <DynamicIcon name={name as IconName} className={cn(size)} />
        </div>
    );
}
