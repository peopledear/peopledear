import { cn } from "@/lib/utils";
import { DynamicIcon, IconName } from "lucide-react/dynamic";

interface IconProps {
    name: string;
    size?: string;
}

export default function Icon({ name, size = "size-4" }: IconProps) {
    return (
        <div className="bg-weak flex items-center justify-center rounded-sm p-2.5 text-white dark:text-gray-400">
            <DynamicIcon name={name as IconName} className={cn(size)} />
        </div>
    );
}
