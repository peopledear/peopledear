import { Label } from "@/components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import type { Country } from "@/types/country";

interface CountrySelectProps {
    id?: string;
    label?: string;
    value: string;
    onValueChange: (value: string) => void;
    countries: Country[];
    error?: string;
    required?: boolean;
    placeholder?: string;
    disabled?: boolean;
}

export function CountrySelect({
    id = "country_id",
    label = "Country",
    value,
    onValueChange,
    countries,
    error,
    required = false,
    placeholder = "Select a country",
    disabled = false,
}: CountrySelectProps) {
    return (
        <div className="space-y-2">
            <Label htmlFor={id}>{label}</Label>
            <Select
                value={value}
                onValueChange={onValueChange}
                required={required}
                disabled={disabled}
            >
                <SelectTrigger id={id}>
                    <SelectValue placeholder={placeholder} />
                </SelectTrigger>
                <SelectContent>
                    {countries.map((country) => (
                        <SelectItem
                            key={country.id}
                            value={country.id.toString()}
                        >
                            {country.displayName}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
            {error && (
                <p className="text-destructive text-sm" role="alert">
                    {error}
                </p>
            )}
        </div>
    );
}
