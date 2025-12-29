import Field, { FieldProps } from "@/components/field";
import { Button } from "@/components/ui/button";
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from "@/components/ui/command";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import { cn } from "@/lib/utils";
import { Icon } from "@/types";
import { Check, ChevronsUpDown } from "lucide-react";
import { DynamicIcon, IconName } from "lucide-react/dynamic";
import { useState } from "react";

interface FieldIconPickerProps extends FieldProps {
    icons: Icon[];
    value?: Icon | null;
    onChange?: (icon: Icon | null) => void;
    onSelect?: (icon: Icon) => void;
    placeholder?: string;
    searchPlaceholder?: string;
    name?: string;
    allowClear?: boolean;
    triggerClassName?: string;
    contentClassName?: string;
    itemClassName?: string;
}

export default function FieldIconPicker({
    icons,
    value,
    onChange,
    onSelect,
    placeholder = "Select icon...",
    searchPlaceholder = "Search icon...",
    name,
    allowClear = true,
    triggerClassName,
    contentClassName,
    itemClassName,
    orientation,
    label,
    description,
    error,
}: FieldIconPickerProps) {
    const [open, setOpen] = useState<boolean>(false);

    const handleSelect = (icon: Icon) => {
        onSelect?.(icon);
        onChange?.(icon);
        setOpen(false);
    };

    const handleClear = () => {
        onChange?.(null);
        setOpen(false);
    };

    return (
        <Field
            orientation={orientation}
            label={label}
            description={description}
            error={error}
        >
            <Popover open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <Button
                        variant="outline"
                        role="combobox"
                        aria-expanded={open}
                        className={cn(
                            "w-full justify-between",
                            triggerClassName,
                        )}
                        name={name}
                    >
                        {value ? (
                            <div className="flex items-center">
                                <DynamicIcon
                                    name={value.icon as IconName}
                                    className="mr-2"
                                />
                                {value.label}
                            </div>
                        ) : (
                            placeholder
                        )}
                        <ChevronsUpDown className="opacity-50" />
                    </Button>
                </PopoverTrigger>
                <PopoverContent
                    className={cn(
                        "w-(--radix-popover-trigger-width) p-0",
                        contentClassName,
                    )}
                >
                    <Command>
                        <CommandList>
                            <CommandInput placeholder={searchPlaceholder} />
                            <CommandEmpty>No icon found.</CommandEmpty>
                            <CommandGroup>
                                {icons.map((icon) => (
                                    <CommandItem
                                        key={icon.value}
                                        value={icon.value}
                                        onSelect={() => handleSelect(icon)}
                                        className={cn(itemClassName)}
                                    >
                                        <DynamicIcon
                                            name={icon.icon as IconName}
                                        />
                                        {icon.label}
                                        <Check
                                            className={cn(
                                                "ml-auto",
                                                value?.value === icon.value
                                                    ? "opacity-100"
                                                    : "opacity-0",
                                            )}
                                        />
                                    </CommandItem>
                                ))}
                                {allowClear && value && (
                                    <CommandItem
                                        onSelect={handleClear}
                                        className={cn(
                                            "text-muted-foreground",
                                            itemClassName,
                                        )}
                                    >
                                        Clear selection
                                    </CommandItem>
                                )}
                            </CommandGroup>
                        </CommandList>
                    </Command>
                </PopoverContent>
            </Popover>
        </Field>
    );
}
