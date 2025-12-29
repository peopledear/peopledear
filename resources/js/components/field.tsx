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
    orientation?: "horizontal" | "vertical";
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
                {description ? (
                    <FieldDescription>{description}</FieldDescription>
                ) : null}
            </FieldContent>
            <div
                className={cn(
                    "flex flex-col items-start gap-2",
                    orientation === "horizontal" ? "items-end" : null,
                )}
            >
                {children}
                <InputError message={error} className="font-semibold" />
            </div>
        </BaseField>
    );
}
