import AppLogoIcon from "@/components/app-logo-icon";
import { CountrySelect } from "@/components/country-select";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { home } from "@/routes";
import type { Country } from "@/types/country";
import { Head, Link, useForm } from "@inertiajs/react";

interface CreateOrganizationProps {
    countries: Country[];
}

interface FormData {
    name: string;
    country_id: string;
}

export default function CreateOrganization({
    countries,
}: CreateOrganizationProps) {
    const form = useForm<FormData>({
        name: "",
        country_id: "",
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post("/org/create");
    };

    return (
        <>
            <Head title="Create Organization" />
            <div className="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
                <div className="flex w-full max-w-md flex-col gap-6">
                    <Link
                        href={home()}
                        className="flex items-center gap-2 self-center font-medium"
                    >
                        <div className="flex h-9 w-9 items-center justify-center">
                            <AppLogoIcon className="size-9 fill-current text-black dark:text-white" />
                        </div>
                    </Link>

                    <Card className="rounded-xl">
                        <CardHeader className="px-10 pt-8 pb-6 text-center">
                            <CardTitle className="text-xl">
                                New organization
                            </CardTitle>
                            <CardDescription>
                                Create a new organization to group and manage
                                your team, offices, and resources.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="px-10 pb-8">
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="space-y-2">
                                    <Label htmlFor="name">
                                        Organization name
                                    </Label>
                                    <Input
                                        id="name"
                                        value={form.data.name}
                                        onChange={(e) =>
                                            form.setData("name", e.target.value)
                                        }
                                        placeholder="Acme Inc."
                                        autoFocus
                                        required
                                    />
                                    {form.errors.name && (
                                        <p className="text-destructive text-sm">
                                            {form.errors.name}
                                        </p>
                                    )}
                                </div>

                                <CountrySelect
                                    value={form.data.country_id}
                                    onValueChange={(value) =>
                                        form.setData("country_id", value)
                                    }
                                    countries={countries}
                                    error={form.errors.country_id}
                                    required
                                />

                                <div className="flex justify-end gap-3">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => window.history.back()}
                                        disabled={form.processing}
                                    >
                                        Cancel
                                    </Button>
                                    <Button
                                        type="submit"
                                        disabled={
                                            form.processing ||
                                            !form.data.name ||
                                            !form.data.country_id
                                        }
                                    >
                                        {form.processing
                                            ? "Creating..."
                                            : "Create organization"}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
