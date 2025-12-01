export function formatDateRange(
    startDate: string,
    endDate: string | null,
): string {
    const start = new Date(startDate).toLocaleDateString();

    if (!endDate || startDate === endDate) {
        return start;
    }

    return `${start} - ${new Date(endDate).toLocaleDateString()}`;
}
