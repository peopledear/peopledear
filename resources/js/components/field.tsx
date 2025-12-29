import InputError from "@/components/input-error";
import {
    Field as BaseField,
    FieldContent,
    FieldDescription,
    FieldLabel,
} from "@/components/ui/field";
import { cn } from "@/lib/utils";
import { PropsWithChildren } from "react";

export interface FieldProps {
    label?: string;
    description?: string;
    orientation?: "horizontal" | "vertical" | "responsive";
    error?: string;
}

interface FieldComponentProps
    extends FieldProps, PropsWithChildren<FieldProps> {}

export default function Field({
    children,
    label,
    description,
    orientation = "horizontal",
    error,
}: FieldComponentProps) {
    return (
        <BaseField orientation={orientation}>
            <FieldContent>
                <FieldLabel>{label}</FieldLabel>
                {orientation === "horizontal" && description ? (
                    <FieldDescription>{description}</FieldDescription>
                ) : null}
                {orientation === "responsive" && description ? (
                    <FieldDescription className="hidden @md/field-group:block">
                        {description}
                    </FieldDescription>
                ) : null}
            </FieldContent>
            <div
                className={cn(
                    "flex flex-col items-start",
                    orientation === "horizontal" ? "items-end gap-2" : "gap-2",
                )}
            >
                {children}
                {(orientation === "vertical" || orientation === "responsive") &&
                description ? (
                    <div className="flex flex-col">
                        {orientation === "vertical" && (
                            <FieldDescription>{description}</FieldDescription>
                        )}
                        {orientation === "responsive" && (
                            <FieldDescription className="@md/field-group:hidden">
                                {description}
                            </FieldDescription>
                        )}
                        <InputError message={error} />
                    </div>
                ) : (
                    <InputError message={error} />
                )}
            </div>
        </BaseField>
    );
}
