import { AppLayout } from "@/layouts/AppLayout";

export default function Dashboard() {
    return (
        <AppLayout>
            <div className="p-6">
                <h1 className="text-2xl font-bold">Dashboard</h1>
                <p className="text-gray-600">Welcome to PeopleDear</p>
            </div>
        </AppLayout>
    );
}
