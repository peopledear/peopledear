"use client";

import {
    Input as AriaInput,
    InputProps as AriaInputProps,
    TextArea as AriaTextArea,
    TextAreaProps as AriaTextAreaProps,
    TextField as AriaTextField,
    TextFieldProps as AriaTextFieldProps,
    ValidationResult as AriaValidationResult,
    composeRenderProps,
    Text,
} from "react-aria-components";

import { cn } from "@/lib/utils";

import { FieldError } from "./field";
import { Label } from "./label";

const TextField = AriaTextField;

const Input = ({ className, ...props }: AriaInputProps) => {
    return (
        <AriaInput
            className={composeRenderProps(className, (className) =>
                cn(
                    "border-input bg-background ring-offset-background placeholder:text-muted-foreground flex h-10 w-full rounded-md border px-3 py-2 text-sm file:border-0 file:bg-transparent file:text-sm file:font-medium",
                    /* Disabled */
                    "data-[disabled]:cursor-not-allowed data-[disabled]:opacity-50",
                    /* Focused */
                    "data-[focused]:ring-ring data-[focused]:ring-2 data-[focused]:ring-offset-2 data-[focused]:outline-none",
                    /* Resets */
                    "focus-visible:outline-none",
                    className,
                ),
            )}
            {...props}
        />
    );
};

const TextArea = ({ className, ...props }: AriaTextAreaProps) => {
    return (
        <AriaTextArea
            className={composeRenderProps(className, (className) =>
                cn(
                    "border-input bg-background ring-offset-background placeholder:text-muted-foreground flex min-h-[80px] w-full rounded-md border px-3 py-2 text-sm",
                    /* Focused */
                    "data-[focused]:ring-ring data-[focused]:ring-2 data-[focused]:ring-offset-2 data-[focused]:outline-none",
                    /* Disabled */
                    "data-[disabled]:cursor-not-allowed data-[disabled]:opacity-50",
                    /* Resets */
                    "focus-visible:outline-none",
                    className,
                ),
            )}
            {...props}
        />
    );
};

interface JollyTextFieldProps extends AriaTextFieldProps {
    label?: string;
    description?: string;
    errorMessage?: string | ((validation: AriaValidationResult) => string);
    textArea?: boolean;
}

function JollyTextField({
    label,
    description,
    errorMessage,
    textArea,
    className,
    ...props
}: JollyTextFieldProps) {
    return (
        <TextField
            className={composeRenderProps(className, (className) =>
                cn("group flex flex-col gap-2", className),
            )}
            {...props}
        >
            <Label>{label}</Label>
            {textArea ? <TextArea /> : <Input />}
            {description && (
                <Text
                    className="text-muted-foreground text-sm"
                    slot="description"
                >
                    {description}
                </Text>
            )}
            <FieldError>
                {typeof errorMessage === "function"
                    ? errorMessage({
                          isInvalid: false,
                          validationErrors: [],
                          validationDetails: {
                              badInput: false,
                              customError: false,
                              patternMismatch: false,
                              rangeOverflow: false,
                              rangeUnderflow: false,
                              stepMismatch: false,
                              tooLong: false,
                              tooShort: false,
                              typeMismatch: false,
                              valueMissing: false,
                              valid: true,
                          } as ValidityState,
                      })
                    : errorMessage}
            </FieldError>
        </TextField>
    );
}

export { Input, JollyTextField, TextArea, TextField };
export type { JollyTextFieldProps };
