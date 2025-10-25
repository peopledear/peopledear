import AdminLayout from "@/layouts/admin-layout";
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

export default function Organization({ organization }: OrganizationProps) {
    return (
        <AdminLayout>
            <Head title="Organization Settings" />
            <div className="space-y-6">
                <h1 className="text-2xl font-semibold">
                    Organization Settings
                </h1>
                <p>Organization: {organization.name}</p>
            </div>
        </AdminLayout>
    );
}
