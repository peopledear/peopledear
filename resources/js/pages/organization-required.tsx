import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Head, router } from "@inertiajs/react";
import { InfoIcon } from "lucide-react";

export default function OrganizationRequired() {
    const handleRefresh = () => {
        router.reload();
    };

    return (
        <>
            <Head title="Organization Required" />
            <div className="bg-background flex min-h-screen items-center justify-center p-4">
                <Card className="w-full max-w-md">
                    <CardHeader>
                        <CardTitle>Organization Not Set Up</CardTitle>
                        <CardDescription>
                            Your organization hasn't been configured yet
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Alert>
                            <InfoIcon className="size-4" />
                            <AlertTitle>Action Required</AlertTitle>
                            <AlertDescription>
                                An owner or people manager needs to create the
                                organization before you can access the system.
                                Please contact your administrator.
                            </AlertDescription>
                        </Alert>
                        <div className="flex justify-end">
                            <Button onClick={handleRefresh} variant="outline">
                                Refresh
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
