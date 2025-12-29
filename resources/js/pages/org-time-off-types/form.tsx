import FieldColorPicker from "@/components/field-color-picker";
import FieldIconPicker from "@/components/field-icon-picker";
import FieldSelect from "@/components/field-select";
import FieldSwitch from "@/components/field-switch";
import InputError from "@/components/input-error";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";

import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import {
    Field,
    FieldContent,
    FieldDescription,
    FieldGroup,
    FieldLabel,
} from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import {
    MultiSelect,
    MultiSelectContent,
    MultiSelectItem,
    MultiSelectTrigger,
    MultiSelectValue,
} from "@/components/ui/multi-select";

import { BalanceType, Icon as IconType, TimeOffUnit } from "@/types";
import OrganizationTimeOffTypesController from "@/wayfinder/actions/App/Http/Controllers/OrganizationTimeOffTypesController";
import { Fieldset, Transition } from "@headlessui/react";
import { Form } from "@inertiajs/react";
import { ChevronRight, XIcon } from "lucide-react";

import { useState } from "react";

interface TimeOffTypeFormProps {
    balanceTypes: BalanceType[];
    timeOffUnits: TimeOffUnit[];
    icons: IconType[];
}

export default function TimeOffTypeForm({
    balanceTypes,
    timeOffUnits,
    icons,
}: TimeOffTypeFormProps) {
    const [openAdvancedSettings, setOpenAdvancedSettings] =
        useState<boolean>(false);
    const [selectedUnits, setSelectedUnits] = useState<number[]>([]);
    const [selectedColor, setSelectedColor] = useState<string>("#ff0000");
    const [selectedIcon, setSelectedIcon] = useState<IconType | null>(null);

    const [requiresApproval, setRequiresApproval] = useState<boolean>(false);
    const [requiresJustification, setRequiresJustification] =
        useState<boolean>(false);
    const [requiresJustificationDocument, setRequiresJustificationDocument] =
        useState<boolean>(false);

    return (
        <Card>
            <CardContent>
                <Form
                    action={OrganizationTimeOffTypesController.store()}
                    transform={(data) => ({
                        ...data,
                        allowed_units: selectedUnits,
                        icon: selectedIcon?.value,
                        color: selectedColor,
                        requires_approval: requiresApproval,
                        requires_justification: requiresJustification,
                        requires_justification_document:
                            requiresJustificationDocument,
                    })}
                    options={{
                        preserveScroll: true,
                    }}
                    className="space-y-4"
                >
                    {({ processing, recentlySuccessful, errors }) => (
                        <>
                            <Fieldset>
                                <FieldGroup>
                                    <Field
                                        orientation="vertical"
                                        className="gap-0.5 space-y-1.5"
                                    >
                                        <FieldLabel htmlFor="name">
                                            Name
                                        </FieldLabel>
                                        <Input
                                            id="name"
                                            name="name"
                                            type="text"
                                            autoComplete="off"
                                        />
                                        <div>
                                            <FieldDescription className="mt-24 block">
                                                The name of the time off type.
                                            </FieldDescription>
                                            <InputError
                                                message={errors.name}
                                                className="font-semibold"
                                            />
                                        </div>
                                    </Field>

                                    <Field
                                        orientation="vertical"
                                        className="gap-0.5 space-y-1.5"
                                    >
                                        <FieldContent>
                                            <FieldLabel>
                                                Allowed Units
                                            </FieldLabel>
                                        </FieldContent>
                                        <MultiSelect
                                            values={selectedUnits.map((u) =>
                                                u.toString(),
                                            )}
                                            onValuesChange={(values) => {
                                                setSelectedUnits(
                                                    values.map((v) =>
                                                        parseInt(v),
                                                    ),
                                                );
                                            }}
                                        >
                                            <MultiSelectTrigger className="w-full">
                                                <MultiSelectValue placeholder="Select allowed units" />
                                            </MultiSelectTrigger>
                                            <MultiSelectContent>
                                                {timeOffUnits.map(
                                                    (unit: TimeOffUnit) => (
                                                        <MultiSelectItem
                                                            value={unit.value.toString()}
                                                            key={unit.value}
                                                        >
                                                            {unit.label}
                                                        </MultiSelectItem>
                                                    ),
                                                )}
                                            </MultiSelectContent>
                                        </MultiSelect>
                                        <div>
                                            <FieldDescription>
                                                The units that can be used
                                            </FieldDescription>
                                            <InputError
                                                message={errors.allowed_units}
                                                className="font-semibold"
                                            />
                                        </div>
                                    </Field>

                                    <FieldSelect
                                        orientation="vertical"
                                        name="balance_mode"
                                        placeholder="Select the Balance Type"
                                        label="Balance Type"
                                        description="Choose how employee time off balances are managed and calculated."
                                        groupLabel="Balance Types"
                                        options={balanceTypes}
                                        error={errors.balance_type}
                                    />

                                    <FieldIconPicker
                                        orientation="vertical"
                                        label="Icon"
                                        description="Select a visual icon to represent this time off type in the application."
                                        icons={icons}
                                        value={selectedIcon}
                                        onChange={setSelectedIcon}
                                        error={errors.icon}
                                    />
                                </FieldGroup>
                            </Fieldset>

                            <div className="flex flex-col items-center gap-y-4">
                                <div className="ml-auto">
                                    <Button
                                        type="button"
                                        variant="link"
                                        className="cursor-pointer text-teal-500 hover:text-teal-600 hover:no-underline"
                                        onClick={() =>
                                            setOpenAdvancedSettings(true)
                                        }
                                    >
                                        Advanced settings
                                    </Button>
                                </div>
                                <Button
                                    disabled={processing}
                                    data-test="submit-time-off-type-form"
                                    type="submit"
                                    className="flex w-full items-center"
                                >
                                    Create time off type{" "}
                                    <ChevronRight className="size-3.5" />
                                </Button>
                                <Transition
                                    show={recentlySuccessful}
                                    enter="transition ease-in-out"
                                    enterFrom="opacity-0"
                                    leave="transition ease-in-out"
                                    leaveTo="opacity-0"
                                >
                                    <p className="text-sm text-neutral-600">
                                        Saved
                                    </p>
                                </Transition>
                            </div>

                            <Dialog
                                open={openAdvancedSettings}
                                onOpenChange={setOpenAdvancedSettings}
                            >
                                <DialogContent
                                    className="sm:max-w-131.25"
                                    showCloseButton={false}
                                >
                                    <div className="flex items-center justify-between gap-x-6 pb-4">
                                        <DialogHeader>
                                            <DialogTitle>
                                                Advanced settings
                                            </DialogTitle>
                                        </DialogHeader>
                                        <div>
                                            <DialogClose>
                                                <Button
                                                    variant="ghost"
                                                    className="size-8"
                                                >
                                                    <XIcon className="size-4" />
                                                </Button>
                                            </DialogClose>
                                        </div>
                                    </div>

                                    <div className="flex flex-col gap-y-4">
                                        <FieldColorPicker
                                            label="Color"
                                            value={selectedColor}
                                            onChange={setSelectedColor}
                                            error={errors.color}
                                        />

                                        <FieldSwitch
                                            orientation="horizontal"
                                            label="Requires Approval"
                                            description="If enabled, time off requests of this type will require approval from a manager or administrator."
                                            defaultChecked={requiresApproval}
                                            onChange={setRequiresApproval}
                                        />

                                        <FieldSwitch
                                            orientation="horizontal"
                                            label="Requires Justification"
                                            description="If enabled, employees will need to provide a justification when requesting time offs of this type."
                                            defaultChecked={
                                                requiresJustification
                                            }
                                            onChange={setRequiresJustification}
                                        />

                                        <FieldSwitch
                                            orientation="horizontal"
                                            label="Requires Documentation"
                                            description="If enabled, employees will need to provide supporting documents when requesting time offs of this type."
                                            defaultChecked={
                                                requiresJustificationDocument
                                            }
                                            onChange={
                                                setRequiresJustificationDocument
                                            }
                                        />
                                    </div>
                                    <DialogFooter className="mt-8">
                                        <Button
                                            className="w-full"
                                            type="button"
                                            size="sm"
                                            onClick={() =>
                                                setOpenAdvancedSettings(false)
                                            }
                                        >
                                            Save settings
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                        </>
                    )}
                </Form>
            </CardContent>
        </Card>
    );
}
