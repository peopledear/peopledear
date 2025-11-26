import InputError from "@/components/input-error";
import { Button } from "@/components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import { Checkbox } from "@/components/ui/checkbox";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Label } from "@/components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import AppLayout from "@/layouts/app-layout";
import { Employee, SharedData } from "@/types";
import EmployeeOverviewController from "@/wayfinder/actions/App/Http/Controllers/EmployeeOverviewController";
import EmployeeTimeOffController from "@/wayfinder/actions/App/Http/Controllers/EmployeeTimeOffController";
import { Head, Link, useForm, usePage } from "@inertiajs/react";
import { ArrowLeftIcon } from "lucide-react";
import React from "react";
import type { DateRange } from "react-day-picker";

interface CreateTimeOffProps {
    employee: Employee;
    types: Record<number, string>;
}

export default function CreateTimeOffPage({
    types,
    employee,
}: CreateTimeOffProps) {
    const page = usePage<SharedData>();
    const { previousPath } = page.props;

    const back =
        previousPath == page.url
            ? EmployeeOverviewController.index().url
            : previousPath;

    const [dialogOpen, setDialogOpen] = React.useState(false);

    const { data, setData, post, processing, errors } = useForm({
        type: "",
        is_half_day: false,
        start_date: undefined as Date | undefined,
        end_date: undefined as Date | undefined,
        employee_id: employee.id,
        organization_id: employee.organization?.id,
    });

    const calculateWeekdays = (startDate: Date, endDate: Date): number => {
        let count = 0;
        const current = new Date(startDate);

        while (current <= endDate) {
            const dayOfWeek = current.getDay();
            if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                count++;
            }
            current.setDate(current.getDate() + 1);
        }

        return count;
    };

    const formatDateRange = () => {
        if (!data.start_date) return "Choose dates";

        const formatDate = (date: Date) => {
            return date.toLocaleDateString("en-US", {
                month: "short",
                day: "numeric",
                year: "numeric",
            });
        };

        if (!data.end_date) return formatDate(data.start_date);

        const weekdays = calculateWeekdays(data.start_date, data.end_date);
        const daysText = weekdays === 1 ? "day" : "days";

        return `${formatDate(data.start_date)} - ${formatDate(data.end_date)} (${weekdays} ${daysText})`;
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(EmployeeTimeOffController.store().url);
    };

    return (
        <AppLayout>
            <Head title="Request new Time Off" />
            <div className="flex w-full max-w-xl flex-col space-y-6 p-4 sm:p-0">
                <div className="mb-10 flex cursor-pointer items-center space-x-1 text-sm">
                    <Link
                        href={back}
                        className="flex items-center space-x-1 text-sm text-gray-500 hover:text-gray-700"
                    >
                        <ArrowLeftIcon size="16" />
                        <span>Back</span>
                    </Link>
                </div>
                <div className="flex flex-col space-y-2">
                    <h2 className="font-semibold">Request Time Off</h2>
                    <p className="text-muted-foreground text-sm">
                        Quickly submit a leave request, attach supporting
                        documents. Check your available balance and upcoming
                        company holidays before you go.
                    </p>
                </div>

                <div className="flex flex-col space-y-6">
                    <div className="w-full rounded-md bg-white p-5 text-sm ring-1 ring-gray-200">
                        <form
                            onSubmit={submit}
                            className="flex flex-col space-y-6"
                        >
                            <FieldGroup>
                                <Field>
                                    <FieldLabel htmlFor="type">
                                        What kind of time off?
                                    </FieldLabel>

                                    <Select
                                        name="type"
                                        value={data.type}
                                        onValueChange={(value) =>
                                            setData("type", value)
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Choose time off type" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {Object.entries(types).map(
                                                ([value, label]) => (
                                                    <SelectItem
                                                        key={value}
                                                        value={value}
                                                    >
                                                        {label}
                                                    </SelectItem>
                                                ),
                                            )}
                                        </SelectContent>
                                    </Select>
                                    <InputError message={errors.type} />
                                </Field>

                                {data.type === "1" && (
                                    <Field>
                                        <div className="flex items-start gap-3">
                                            <Checkbox
                                                id="is_half_day"
                                                checked={data.is_half_day}
                                                onCheckedChange={(checked) =>
                                                    setData(
                                                        "is_half_day",
                                                        checked === true,
                                                    )
                                                }
                                            />
                                            <Label htmlFor="is_half_day">
                                                Half day?
                                            </Label>
                                        </div>
                                    </Field>
                                )}

                                <Field>
                                    <FieldLabel htmlFor="duration">
                                        When?
                                    </FieldLabel>
                                    <Dialog
                                        open={dialogOpen}
                                        onOpenChange={setDialogOpen}
                                    >
                                        <DialogTrigger asChild>
                                            <Button
                                                variant="outline"
                                                className="w-full justify-start text-left font-normal"
                                            >
                                                <span
                                                    className={
                                                        !data.start_date
                                                            ? "text-muted-foreground"
                                                            : ""
                                                    }
                                                >
                                                    {formatDateRange()}
                                                </span>
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent className="w-full sm:max-w-xl">
                                            <DialogHeader>
                                                <DialogTitle>
                                                    Select dates
                                                </DialogTitle>
                                            </DialogHeader>
                                            <Calendar
                                                mode="range"
                                                numberOfMonths={1}
                                                selected={{
                                                    from: data.start_date,
                                                    to: data.end_date,
                                                }}
                                                onSelect={(
                                                    range:
                                                        | DateRange
                                                        | undefined,
                                                ) => {
                                                    setData({
                                                        ...data,
                                                        start_date: range?.from,
                                                        end_date: range?.to,
                                                    });
                                                }}
                                                className="w-full rounded-lg border shadow-sm"
                                            />
                                            <div className="flex justify-end gap-2">
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    onClick={() =>
                                                        setDialogOpen(false)
                                                    }
                                                >
                                                    Cancel
                                                </Button>
                                                <Button
                                                    type="button"
                                                    onClick={() =>
                                                        setDialogOpen(false)
                                                    }
                                                    disabled={
                                                        !data.start_date ||
                                                        !data.end_date
                                                    }
                                                >
                                                    Confirm
                                                </Button>
                                            </div>
                                        </DialogContent>
                                    </Dialog>
                                    <InputError message={errors.start_date} />
                                </Field>
                            </FieldGroup>
                            <Button type="submit" disabled={processing}>
                                {processing ? "Submitting..." : "Submit"}
                            </Button>
                        </form>
                    </div>
                    <div className="w-24 shrink-0">
                        Balance
                        <ul>
                            <li>Year: 15</li>
                        </ul>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
