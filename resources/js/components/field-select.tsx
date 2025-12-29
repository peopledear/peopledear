import Field, { FieldProps } from "@/components/field";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

interface SelectOption<TValue = string> {
    value: TValue;
    label: string;
}

interface FieldSelectProps<TValue = string> extends FieldProps {
    name: string;
    placeholder?: string;
    options: SelectOption<TValue>[];
    value?: TValue;
    onChange?: (value: TValue) => void;
    groupLabel?: string;
}

export default function FieldSelect<TValue = string>({
    name,
    placeholder,
    options,
    value,
    onChange,
    orientation,
    label,
    description,
    error,
    groupLabel,
}: FieldSelectProps<TValue>) {
    return (
        <Field
            orientation={orientation}
            label={label}
            description={description}
            error={error}
        >
            <Select
                name={name}
                value={value !== undefined ? String(value) : undefined}
                onValueChange={(val) => {
                    const newOption = options.find(
                        (option) => String(option.value) === val,
                    );
                    if (newOption && onChange) {
                        onChange(newOption.value);
                    }
                }}
            >
                <SelectTrigger className="w-full">
                    <SelectValue placeholder={placeholder} />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        {groupLabel && <SelectLabel>{groupLabel}</SelectLabel>}
                        {options.map((option, index) => (
                            <SelectItem
                                value={String(option.value)}
                                key={index}
                            >
                                {option.label}
                            </SelectItem>
                        ))}
                    </SelectGroup>
                </SelectContent>
            </Select>
        </Field>
    );
}
