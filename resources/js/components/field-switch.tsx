import Field, { FieldProps } from "@/components/field";
import { Switch } from "@/components/ui/switch";
import { useState } from "react";

interface FieldSwitchProps extends FieldProps {
    id?: string;
    name?: string;
    defaultChecked?: boolean;
    onChange?: (checked: boolean) => void;
}

export default function FieldSwitch({
    id,
    name,
    orientation,
    defaultChecked = false,
    onChange,
    label,
    description,
    error,
}: FieldSwitchProps) {
    const [checked, setChecked] = useState(defaultChecked);

    const handleOnChange = (checked: boolean) => {
        if (onChange) {
            onChange(checked);
        }
        setChecked(checked);
    };

    return (
        <Field
            label={label}
            description={description}
            error={error}
            orientation={orientation}
        >
            {name && (
                <input type="hidden" name={name} value={checked ? "1" : "0"} />
            )}
            <Switch
                id={id}
                checked={checked}
                onCheckedChange={handleOnChange}
            />
        </Field>
    );
}
