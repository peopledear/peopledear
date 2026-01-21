import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import {
    Field,
    FieldContent,
    FieldDescription,
    FieldGroup,
    FieldLabel,
    FieldSeparator,
    FieldSet,
} from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import AdminLayout from "@/layouts/org-layout";
import OrgSettingsLayout from "@/layouts/settings/org-layout";
import { Head } from "@inertiajs/react";

interface OrganizationProps {
    organization: {
        id: number;
        name: string;
        vat_number: string | null;
        ssn: string | null;
        phone: string | null;
    };
}

export default function Edit({ organization }: OrganizationProps) {
    return (
        <AdminLayout>
            <Head title="Settings" />
            <OrgSettingsLayout>
                <Card>
                    <CardHeader>
                        <CardTitle>General</CardTitle>
                        <CardDescription>
                            General settings related to the organization.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <FieldSet>
                            <FieldSeparator className="-mx-6" />
                            <FieldGroup>
                                <Field orientation="responsive">
                                    <FieldContent>
                                        <FieldLabel htmlFor="organization-name">
                                            Organization name
                                        </FieldLabel>
                                        <FieldDescription>
                                            The name used to identify your
                                            organization.
                                        </FieldDescription>
                                    </FieldContent>
                                    <Input
                                        id="name"
                                        defaultValue={organization.name}
                                        placeholder="Organization name"
                                        className="sm:min-w-75"
                                    />
                                </Field>

                                <FieldSeparator className="-mx-6" />

                                <Field orientation="responsive">
                                    <FieldContent>
                                        <FieldLabel htmlFor="vat-number">
                                            VAT number
                                        </FieldLabel>
                                        <FieldDescription>
                                            Your organization's VAT
                                            identification number.
                                        </FieldDescription>
                                    </FieldContent>
                                    <Input
                                        id="vat-number"
                                        defaultValue={
                                            organization.vat_number || ""
                                        }
                                        placeholder="VAT number"
                                        className="sm:min-w-75"
                                    />
                                </Field>

                                <FieldSeparator className="-mx-6" />

                                <Field orientation="responsive">
                                    <FieldContent>
                                        <FieldLabel htmlFor="ssn">
                                            SSN / Tax ID
                                        </FieldLabel>
                                        <FieldDescription>
                                            Social Security Number or Tax
                                            Identification Number.
                                        </FieldDescription>
                                    </FieldContent>
                                    <Input
                                        id="ssn"
                                        defaultValue={organization.ssn || ""}
                                        placeholder="SSN or Tax ID"
                                        className="sm:min-w-75"
                                    />
                                </Field>

                                <FieldSeparator className="-mx-6" />

                                {/* Phone */}
                                <Field orientation="responsive">
                                    <FieldContent>
                                        <FieldLabel htmlFor="phone">
                                            Phone number
                                        </FieldLabel>
                                        <FieldDescription>
                                            Main contact phone number for the
                                            organization.
                                        </FieldDescription>
                                    </FieldContent>
                                    <Input
                                        id="phone"
                                        type="tel"
                                        defaultValue={organization.phone || ""}
                                        placeholder="Phone number"
                                        className="sm:min-w-75"
                                    />
                                </Field>

                                <FieldSeparator className="-mx-6" />

                                {/* Save Button */}
                                <Field orientation="responsive">
                                    <Button type="submit" className="w-fit">
                                        Save Changes
                                    </Button>
                                </Field>
                            </FieldGroup>
                        </FieldSet>
                    </CardContent>
                </Card>
            </OrgSettingsLayout>
        </AdminLayout>
    );
}
