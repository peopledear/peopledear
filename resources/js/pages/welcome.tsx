import AppLogo from "@/components/app-logo";
import HeroAnimation from "@/components/hero-animation";
import type { SharedData } from "@/types";
import { dashboard } from "@/wayfinder/routes";
import { register } from "@/wayfinder/routes/auth";
import { Head, Link, usePage } from "@inertiajs/react";

const stats = [
    {
        value: "120+",
        label: "teams automate onboarding",
    },
    {
        value: "24h",
        label: "average implementation",
    },
    {
        value: "98%",
        label: "employee satisfaction",
    },
];

export default function Welcome(): JSX.Element {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="PeopleDear – HR that feels effortless">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link
                    href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700"
                    rel="stylesheet"
                />
            </Head>

            <div className="relative min-h-screen overflow-hidden bg-[#fdf9f4] text-[#1c1c1a] dark:bg-[#030202] dark:text-[#ededec]">
                <div className="pointer-events-none absolute inset-0">
                    <div className="absolute -top-1/3 -left-1/4 h-[32rem] w-[32rem] rounded-full bg-[#ffe2cc] opacity-70 blur-[160px] dark:bg-[#ff6d4a]/30" />
                    <div className="absolute top-[15%] right-[-10%] h-[28rem] w-[28rem] rounded-full bg-[#ffd8f3] opacity-60 blur-[160px] dark:bg-[#8b5cf6]/20" />
                    <div className="absolute inset-x-0 bottom-[-30%] h-[32rem] bg-gradient-to-t from-[#fff7ee] via-transparent to-transparent opacity-80 dark:from-[#0b0503]" />
                </div>

                <div className="relative z-10 flex min-h-screen flex-col">
                    <header className="flex items-center justify-between px-6 py-7 lg:px-12">
                        <Link
                            href="/"
                            className="flex items-center gap-2 text-base font-semibold"
                        >
                            <AppLogo />
                        </Link>

                        <div className="flex items-center gap-3">
                            {auth.user ? (
                                <Link
                                    href={dashboard()}
                                    className="inline-flex items-center gap-2 rounded-full border border-transparent bg-[#1c1c1a] px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-[#ff6d2d]/40 transition-all hover:-translate-y-0.5 hover:bg-black focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ff6d2d] dark:bg-[#ededec] dark:text-[#161513] dark:shadow-none dark:hover:bg-white"
                                >
                                    Go to dashboard
                                </Link>
                            ) : (
                                <Link
                                    href={register()}
                                    className="inline-flex items-center gap-2 rounded-full border border-[#f7c7a7] bg-white/90 px-5 py-2 text-sm font-semibold text-[#49230f] shadow-lg shadow-[#ff7a45]/30 transition-all hover:-translate-y-0.5 hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#fb6f1f] dark:border-[#2d120b] dark:bg-[#130804] dark:text-[#ffddb8] dark:shadow-none dark:hover:bg-[#1f0c06]"
                                >
                                    Register
                                </Link>
                            )}
                        </div>
                    </header>

                    <main className="flex flex-1 flex-col gap-12 px-6 pt-4 pb-16 lg:flex-row lg:items-center lg:px-12 lg:pb-24">
                        <section className="max-w-2xl space-y-7">
                            <p className="inline-flex items-center gap-2 rounded-full border border-[#f7c7a7] bg-white/80 px-4 py-1 text-xs font-semibold tracking-[0.35em] text-[#bc4c1f] uppercase dark:border-[#2c1812] dark:bg-[#140904] dark:text-[#ffb791]">
                                PeopleDear
                            </p>
                            <div className="space-y-4 text-balance">
                                <h1 className="text-4xl font-semibold tracking-tight text-[#16140f] sm:text-5xl lg:text-6xl dark:text-white">
                                    The calm HQ for modern people operations.
                                </h1>
                                <p className="text-base leading-7 text-[#5e5c55] dark:text-[#c9c6be]">
                                    Automate onboarding, approvals, reviews, and
                                    every workflow that slows down growing
                                    teams. PeopleDear centralizes your employee
                                    data, surfaces context, and helps HR leaders
                                    ship thoughtful experiences in minutes.
                                </p>
                            </div>
                            <div className="flex flex-wrap items-center gap-4">
                                <Link
                                    href={register()}
                                    className="inline-flex items-center gap-3 rounded-full border border-transparent bg-[#1c1c1a] px-6 py-3 text-sm font-semibold text-white shadow-[0_20px_60px_rgba(28,28,26,0.25)] transition-all hover:-translate-y-0.5 hover:bg-black focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ff6d2d] dark:bg-[#f5f3ef] dark:text-[#13110f] dark:hover:bg-white"
                                >
                                    <span>Create your account</span>
                                    <svg
                                        width={16}
                                        height={16}
                                        viewBox="0 0 16 16"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        className="size-4"
                                    >
                                        <path
                                            d="M4 12L12 4M12 4H5.33333M12 4V10.6667"
                                            stroke="currentColor"
                                            strokeWidth={1.5}
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                        />
                                    </svg>
                                </Link>
                                <span className="text-sm text-[#7b7972] dark:text-[#b9b6ae]">
                                    No credit card required
                                </span>
                            </div>
                            <div className="grid gap-6 pt-4 sm:grid-cols-2">
                                {stats.map((stat) => (
                                    <div
                                        key={stat.label}
                                        className="flex flex-col gap-1"
                                    >
                                        <p className="text-3xl font-semibold text-[#1c1c1a] dark:text-white">
                                            {stat.value}
                                        </p>
                                        <p className="text-sm text-[#6a6861] dark:text-[#c1beb6]">
                                            {stat.label}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </section>

                        <HeroAnimation className="mt-4 lg:mt-0" />
                    </main>
                </div>
            </div>
        </>
    );
}
