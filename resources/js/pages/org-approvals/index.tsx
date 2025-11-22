import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { useInitials } from "@/hooks/use-initials";
import OrgLayout from "@/layouts/org-layout";
import { Head, router } from "@inertiajs/react";
import { CheckIcon, XIcon } from "lucide-react";
import { useState } from "react";

interface Employee {
    id: number;
    name: string;
    email: string;
}

interface TimeOffRequest {
    id: number;
    employee_id: number;
    type: string;
    start_date: string;
    end_date: string;
    employee: Employee;
}

interface Approval {
    id: number;
    approvable_type: string;
    approvable_id: number;
    status: string;
    created_at: string;
    approvable: TimeOffRequest;
}

interface OrgApprovalsPageProps {
    pendingApprovals: Approval[];
}

export default function OrgApprovalsPage({
    pendingApprovals,
}: OrgApprovalsPageProps) {
    const getInitials = useInitials();
    const [processing, setProcessing] = useState<number | null>(null);

    const handleApprove = (approvalId: number) => {
        setProcessing(approvalId);
        router.post(
            `/org/approvals/${approvalId}/approve`,
            {},
            {
                onFinish: () => setProcessing(null),
            },
        );
    };

    const handleReject = (approvalId: number, reason: string) => {
        setProcessing(approvalId);
        router.post(
            `/org/approvals/${approvalId}/reject`,
            { rejection_reason: reason },
            {
                onFinish: () => setProcessing(null),
            },
        );
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
            year: "numeric",
        });
    };

    const getTimeOffTypeLabel = (type: string) => {
        const labels: Record<string, string> = {
            vacation: "Vacation",
            personal_day: "Personal Day",
            sick_leave: "Sick Leave",
        };
        return labels[type] || type;
    };

    return (
        <OrgLayout>
            <Head title="Approval Queue" />
            <div className="flex w-full max-w-6xl flex-col">
                <div className="mb-4">
                    <h2 className="font-medium">Approval Queue</h2>
                    <p className="text-muted-foreground text-sm">
                        Review and approve time-off requests from your team
                    </p>
                </div>

                {pendingApprovals.length === 0 ? (
                    <Card>
                        <CardContent className="py-8 text-center">
                            <p className="text-muted-foreground">
                                No pending approvals
                            </p>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="space-y-4">
                        {pendingApprovals.map((approval) => (
                            <Card key={approval.id}>
                                <CardHeader className="pb-3">
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center space-x-3">
                                            <Avatar className="size-10">
                                                <AvatarFallback className="bg-neutral-200 text-xs font-bold text-black dark:bg-neutral-700 dark:text-white">
                                                    {getInitials(
                                                        approval.approvable
                                                            .employee.name,
                                                    )}
                                                </AvatarFallback>
                                            </Avatar>
                                            <div>
                                                <CardTitle className="text-base">
                                                    {
                                                        approval.approvable
                                                            .employee.name
                                                    }
                                                </CardTitle>
                                                <CardDescription>
                                                    {
                                                        approval.approvable
                                                            .employee.email
                                                    }
                                                </CardDescription>
                                            </div>
                                        </div>
                                        <div className="flex space-x-2">
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                onClick={() =>
                                                    handleReject(
                                                        approval.id,
                                                        "Request denied",
                                                    )
                                                }
                                                disabled={
                                                    processing === approval.id
                                                }
                                            >
                                                <XIcon className="mr-1 h-4 w-4" />
                                                Reject
                                            </Button>
                                            <Button
                                                size="sm"
                                                onClick={() =>
                                                    handleApprove(approval.id)
                                                }
                                                disabled={
                                                    processing === approval.id
                                                }
                                            >
                                                <CheckIcon className="mr-1 h-4 w-4" />
                                                Approve
                                            </Button>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex items-center space-x-4 text-sm">
                                        <span className="rounded-md bg-blue-100 px-2 py-1 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {getTimeOffTypeLabel(
                                                approval.approvable.type,
                                            )}
                                        </span>
                                        <span className="text-muted-foreground">
                                            {formatDate(
                                                approval.approvable.start_date,
                                            )}{" "}
                                            -{" "}
                                            {formatDate(
                                                approval.approvable.end_date,
                                            )}
                                        </span>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </OrgLayout>
    );
}
