import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { AuthLayout } from "@/layouts/AuthLayout";
import { store } from "@/wayfinder/actions/App/Http/Controllers/Auth/LoginController";
import { useForm } from "@inertiajs/react";
import { Eye, EyeOff } from "lucide-react";
import { useState } from "react";

export default function Login() {
    const [showPassword, setShowPassword] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        email: "",
        password: "",
        remember: false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store().url, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    return (
        <AuthLayout
            header={
                <div>
                    <div className="text-xl font-semibold">
                        Welcome to PeopleDear
                    </div>
                    <div className="text-sm text-gray-600">
                        Don't have an account?{" "}
                        <a href="#" className="text-blue-600 hover:underline">
                            Register
                        </a>
                    </div>
                </div>
            }
        >
            <form onSubmit={submit} className="flex flex-col gap-y-6">
                <div className="space-y-2">
                    <Label htmlFor="email">Email</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData("email", e.target.value)}
                        placeholder="Enter your email"
                        className="w-full"
                    />
                    {errors.email && (
                        <p className="text-sm text-red-600">{errors.email}</p>
                    )}
                </div>

                <div className="space-y-2">
                    <Label htmlFor="password">Password</Label>
                    <div className="relative">
                        <Input
                            id="password"
                            name="password"
                            type={showPassword ? "text" : "password"}
                            value={data.password}
                            onChange={(e) =>
                                setData("password", e.target.value)
                            }
                            placeholder="Enter your password"
                            className="w-full pr-10"
                        />
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            className="absolute top-0 right-0 h-full px-3 py-2 hover:bg-transparent"
                            onClick={() => setShowPassword(!showPassword)}
                            aria-label={
                                showPassword ? "Hide password" : "Show password"
                            }
                        >
                            {showPassword ? (
                                <EyeOff className="h-4 w-4" />
                            ) : (
                                <Eye className="h-4 w-4" />
                            )}
                        </Button>
                    </div>
                    {errors.password && (
                        <p className="text-sm text-red-600">
                            {errors.password}
                        </p>
                    )}
                </div>

                <div className="flex items-center space-x-2">
                    <Checkbox
                        id="remember"
                        checked={data.remember}
                        onCheckedChange={(checked) =>
                            setData("remember", checked as boolean)
                        }
                    />
                    <Label htmlFor="remember" className="text-sm">
                        Remember me
                    </Label>
                </div>

                <Button type="submit" className="w-full" disabled={processing}>
                    {processing ? "Signing in..." : "Continue"}
                </Button>
            </form>
        </AuthLayout>
    );
}
