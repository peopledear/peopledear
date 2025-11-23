import { Badge } from "@/components/ui/badge";
import {
    Card,
    CardAction,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { VacationBalance } from "@/types";

interface SectionCardsProps {
    vacationBalance: VacationBalance;
}

export function SectionCards({ vacationBalance }: SectionCardsProps) {
    return (
        <div className="*:data-[slot=card]:from-primary/5 *:data-[slot=card]:to-card dark:*:data-[slot=card]:bg-card grid grid-cols-1 gap-4 *:data-[slot=card]:bg-gradient-to-t *:data-[slot=card]:shadow-xs sm:grid-cols-2 lg:grid-cols-4">
            <Card className="@container/card">
                <CardHeader>
                    <CardDescription>Vacations left</CardDescription>
                    <CardTitle className="text-2xl font-semibold tabular-nums @[250px]/card:text-3xl">
                        {vacationBalance.remaining}{" "}
                        <span className="text-lg">days</span>
                    </CardTitle>
                    <CardAction>
                        <Badge variant="outline">
                            {vacationBalance.taken} taken
                        </Badge>
                    </CardAction>
                </CardHeader>
                <CardFooter className="flex-col items-start gap-1.5 text-sm">
                    <div className="font-medium">
                        {vacationBalance.yearBalance} days total for{" "}
                        {vacationBalance.year}
                    </div>
                    {parseFloat(vacationBalance.lastYearBalance) > 0 ? (
                        <div className="text-muted-foreground">
                            Includes {vacationBalance.lastYearBalance} from last
                            year
                        </div>
                    ) : parseFloat(vacationBalance.fromLastYear) > 0 ? (
                        <div className="text-muted-foreground">
                            Took {vacationBalance.fromLastYear} days from last
                            year,{" "}
                            {parseFloat(vacationBalance.taken) -
                                parseFloat(vacationBalance.fromLastYear)}{" "}
                            days from this year
                        </div>
                    ) : null}
                </CardFooter>
            </Card>
            {/*
            <Card className="@container/card"></Card>*/}
        </div>
    );
}
