import type { JSX } from "react";

import AppLogo from "@/components/app-logo";
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
                    <div className="absolute -top-1/3 -left-1/4 h-128 w-lg rounded-full bg-[#ffe2cc] opacity-70 blur-[160px] dark:bg-[#ff6d4a]/30" />
                    <div className="absolute top-[15%] right-[-10%] h-112 w-md rounded-full bg-[#ffd8f3] opacity-60 blur-[160px] dark:bg-[#8b5cf6]/20" />
                    <div className="absolute inset-x-0 bottom-[-30%] h-128 bg-linear-to-t from-[#fff7ee] via-transparent to-transparent opacity-80 dark:from-[#0b0503]" />
                </div>

                <div className="relative z-10 flex min-h-screen flex-col">
                    <header className="px-6 py-7 lg:px-12">
                        <div className="mx-auto flex w-full max-w-295 items-center justify-between">
                            <Link href="/" className="flex items-center">
                                <AppLogo variant="external" />
                            </Link>

                            <div className="flex items-center gap-3">
                                {auth.user ? (
                                    <Link
                                        href={dashboard()}
                                        className="inline-flex items-center gap-2 rounded-full border border-transparent bg-[#1c1c1a] px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-[#ff6d2d]/40 transition-all hover:-translate-y-0.5 hover:bg-black focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-[#ff6d2d] dark:bg-[#ededec] dark:text-[#161513] dark:shadow-none dark:hover:bg-white"
                                    >
                                        Go to dashboard
                                    </Link>
                                ) : (
                                    <Link
                                        href={register()}
                                        className="inline-flex items-center gap-2 rounded-full border border-[#f7c7a7] bg-white/90 px-5 py-2 text-sm font-semibold text-[#49230f] shadow-lg shadow-[#ff7a45]/30 transition-all hover:-translate-y-0.5 hover:bg-white focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-[#fb6f1f] dark:border-[#2d120b] dark:bg-[#130804] dark:text-[#ffddb8] dark:shadow-none dark:hover:bg-[#1f0c06]"
                                    >
                                        Register
                                    </Link>
                                )}
                            </div>
                        </div>
                    </header>

                    <main className="flex flex-1 flex-col justify-center px-6 pt-6 pb-16 lg:px-12 lg:pb-24">
                        <div className="mx-auto grid w-full max-w-295 items-center gap-10 lg:grid-cols-[minmax(0,0.95fr)_minmax(0,0.85fr)]">
                            <section className="space-y-7">
                                <p className="inline-flex items-center gap-2 rounded-full border border-[#f7c7a7] bg-white/80 px-4 py-1 text-xs font-semibold tracking-[0.35em] text-[#bc4c1f] uppercase dark:border-[#2c1812] dark:bg-[#140904] dark:text-[#ffb791]">
                                    PeopleDear
                                </p>
                                <div className="space-y-4 text-balance">
                                    <h1 className="text-4xl font-semibold tracking-tight text-[#16140f] sm:text-5xl lg:text-6xl dark:text-white">
                                        The calm HQ for modern people
                                        operations.
                                    </h1>
                                    <p className="text-base leading-7 text-[#5e5c55] dark:text-[#c9c6be]">
                                        Automate onboarding, approvals, reviews,
                                        and every workflow that slows down
                                        growing teams. PeopleDear centralizes
                                        your employee data, surfaces context,
                                        and helps HR leaders ship thoughtful
                                        experiences in minutes.
                                    </p>
                                </div>
                                <div className="flex flex-wrap items-center gap-4">
                                    <Link
                                        href={register()}
                                        className="inline-flex items-center gap-3 rounded-full border border-transparent bg-[#1c1c1a] px-6 py-3 text-sm font-semibold text-white shadow-[0_20px_60px_rgba(28,28,26,0.25)] transition-all hover:-translate-y-0.5 hover:bg-black focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-[#ff6d2d] dark:bg-[#f5f3ef] dark:text-[#13110f] dark:hover:bg-white"
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

                            <div className="relative mt-2 overflow-hidden rounded-[28px] border border-white/60 bg-linear-to-br from-[#fff7ef] via-[#ffe6d8] to-[#ffe8fb] p-8 shadow-[0px_30px_80px_rgba(255,126,63,0.25)] dark:border-[#2d140c] dark:from-[#1b0c05] dark:via-[#150608] dark:to-[#210b16] dark:shadow-[0px_25px_70px_rgba(255,108,64,0.2)]">
                                <div className="pointer-events-none absolute -top-24 right-2 h-60 w-60 rounded-full bg-white/60 blur-[150px] dark:bg-[#f87171]/20" />
                                <div className="pointer-events-none absolute -bottom-20 left-6 h-48 w-48 rounded-full bg-[#ffd7b8]/70 blur-[140px] dark:bg-[#a855f7]/20" />

                                <div className="relative space-y-6">
                                    <div className="space-y-3">
                                        <p className="text-sm font-semibold tracking-[0.3em] text-[#a9471b]/70 uppercase dark:text-[#ffbe98]">
                                            Workflow preview
                                        </p>
                                        <p className="text-2xl font-semibold text-[#1b1713] dark:text-white">
                                            Launch autopilot onboarding in three
                                            steps.
                                        </p>
                                    </div>

                                    <ol className="space-y-4 text-sm">
                                        <li className="flex items-start gap-3 rounded-2xl border border-white/80 bg-white/90 p-4 shadow-lg shadow-[#ffbe9a]/30 dark:border-white/10 dark:bg-white/5 dark:text-[#f5f0e9]">
                                            <span className="flex h-7 w-7 items-center justify-center rounded-full bg-[#ffefe3] text-xs font-semibold text-[#d4511e] dark:bg-[#2a140c]">
                                                01
                                            </span>
                                            <div>
                                                <p className="font-semibold text-[#1c1b19] dark:text-white">
                                                    Assign your template
                                                </p>
                                                <p className="text-[#6d6a63] dark:text-[#d8d4cb]">
                                                    Pick the curated journey and
                                                    every document, task, and
                                                    assignee appears instantly.
                                                </p>
                                            </div>
                                        </li>
                                        <li className="flex items-start gap-3 rounded-2xl border border-transparent bg-white/85 p-4 shadow-lg shadow-[#ffd9bd]/30 dark:bg-white/5 dark:text-[#f5f0e9]">
                                            <span className="flex h-7 w-7 items-center justify-center rounded-full bg-[#ffe1d1] text-xs font-semibold text-[#d4511e] dark:bg-[#2a140c]">
                                                02
                                            </span>
                                            <div>
                                                <p className="font-semibold text-[#1c1b19] dark:text-white">
                                                    Route approvals
                                                </p>
                                                <p className="text-[#6d6a63] dark:text-[#d8d4cb]">
                                                    Managers approve in Slack or
                                                    email and PeopleDear tracks
                                                    every touchpoint.
                                                </p>
                                            </div>
                                        </li>
                                        <li className="flex items-start gap-3 rounded-2xl border border-white/70 bg-white/90 p-4 shadow-lg shadow-[#ffe5ff]/30 dark:border-white/10 dark:bg-white/5 dark:text-[#f5f0e9]">
                                            <span className="flex h-7 w-7 items-center justify-center rounded-full bg-[#ffeafc] text-xs font-semibold text-[#d4511e] dark:bg-[#2a140c]">
                                                03
                                            </span>
                                            <div>
                                                <p className="font-semibold text-[#1c1b19] dark:text-white">
                                                    Measure sentiment
                                                </p>
                                                <p className="text-[#6d6a63] dark:text-[#d8d4cb]">
                                                    Feedback pulses roll up into
                                                    a single dashboard so you
                                                    can iterate quickly.
                                                </p>
                                            </div>
                                        </li>
                                    </ol>

                                    <div className="rounded-2xl border border-white/70 bg-white/90 p-5 text-sm shadow-xl shadow-white/40 backdrop-blur dark:border-white/10 dark:bg-white/10 dark:text-white">
                                        <p className="text-xs font-semibold tracking-[0.35em] text-[#a9471b]/60 uppercase dark:text-[#f9caa9]">
                                            status board
                                        </p>
                                        <div className="mt-3 flex flex-wrap gap-5">
                                            <div>
                                                <p className="text-2xl font-semibold text-[#1c1b19] dark:text-white">
                                                    42
                                                </p>
                                                <p className="text-xs tracking-[0.32em] text-[#6f6c66] uppercase dark:text-[#cfcabf]">
                                                    onboarding
                                                </p>
                                            </div>
                                            <div>
                                                <p className="text-2xl font-semibold text-[#1c1b19] dark:text-white">
                                                    8m
                                                </p>
                                                <p className="text-xs tracking-[0.32em] text-[#6f6c66] uppercase dark:text-[#cfcabf]">
                                                    avg response
                                                </p>
                                            </div>
                                            <div>
                                                <p className="text-2xl font-semibold text-[#1c1b19] dark:text-white">
                                                    +23%
                                                </p>
                                                <p className="text-xs tracking-[0.32em] text-[#6f6c66] uppercase dark:text-[#cfcabf]">
                                                    engagement
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </>
    );
}
