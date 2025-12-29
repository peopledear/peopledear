"use client";

import Field, { FieldProps } from "@/components/field";
import { Button } from "@/components/ui/button";
import {
    ColorArea,
    ColorField,
    ColorPicker,
    ColorSlider,
    ColorSwatch,
    ColorSwatchPicker,
    ColorSwatchPickerItem,
    ColorThumb,
    SliderTrack,
} from "@/components/ui/color";
import { Label } from "@/components/ui/label";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import { Input } from "@/components/ui/textfield";
import React from "react";
import { Color, parseColor } from "react-aria-components";

interface FieldColorPickerProps extends FieldProps {
    value?: string;
    onChange?: (value: string) => void;
    orientation?: "horizontal" | "vertical";
    label?: string;
    description?: string;
    error?: string;
}

export default function FieldColorPicker({
    value = "#ff0000",
    onChange,
    orientation = "horizontal",
    label = "Color",
    description,
    error,
}: FieldColorPickerProps) {
    const [color, setColor] = React.useState<Color>(parseColor(value));

    const handleColorChange = (newColor: Color) => {
        setColor(newColor);
        if (onChange) {
            onChange(newColor.toString("hex"));
        }
    };

    return (
        <Field
            orientation={orientation}
            label={label}
            description={description}
            error={error}
        >
            <ColorPicker value={color} onChange={handleColorChange}>
                <Popover>
                    <PopoverTrigger asChild>
                        <Button
                            variant="ghost"
                            className="flex h-fit items-center gap-2 p-1"
                        >
                            <ColorSwatch className="size-8 rounded-md border-2" />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent className="flex w-fit flex-col gap-4 p-3 outline-none">
                        <div>
                            <ColorArea
                                colorSpace="hsb"
                                xChannel="saturation"
                                yChannel="brightness"
                                className="h-[164px] rounded-b-none border-b-0"
                            >
                                <ColorThumb className="top-1/2" />
                            </ColorArea>
                            <ColorSlider channel="hue" colorSpace="hsb">
                                <SliderTrack className="rounded-t-none border-t-0">
                                    <ColorThumb className="top-1/2" />
                                </SliderTrack>
                            </ColorSlider>
                        </div>
                        <ColorField
                            colorSpace="hsb"
                            aria-label="input"
                            className="w-48"
                        >
                            <Label>Hex</Label>
                            <Input className="" />
                        </ColorField>
                        <ColorSwatchPicker className="w-48">
                            <ColorSwatchPickerItem color="#F00">
                                <ColorSwatch />
                            </ColorSwatchPickerItem>
                            <ColorSwatchPickerItem color="#f90">
                                <ColorSwatch />
                            </ColorSwatchPickerItem>
                            <ColorSwatchPickerItem color="#0F0">
                                <ColorSwatch />
                            </ColorSwatchPickerItem>
                            <ColorSwatchPickerItem color="#08f">
                                <ColorSwatch />
                            </ColorSwatchPickerItem>
                            <ColorSwatchPickerItem color="#00f">
                                <ColorSwatch />
                            </ColorSwatchPickerItem>
                        </ColorSwatchPicker>
                    </PopoverContent>
                </Popover>
            </ColorPicker>
        </Field>
    );
}
