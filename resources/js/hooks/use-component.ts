import { SharedData } from "@/types";
import { usePage } from "@inertiajs/react";
import axios from "axios";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ComponentType, useEffect, useState } from "react";

interface ComponentResponse {
    component: string;
    props: Record<string, unknown>;
}

const fetchComponent = async (
    href: string,
    version: string,
): Promise<ComponentResponse> => {
    const response = await axios.get(href, {
        headers: {
            Accept: "text/html, application/xhtml+xml",
            "X-Inertia": true,
            "X-Inertia-Version": version,
            "X-Dropdown": true,
            "X-Requested-With": "XMLHttpRequest",
        },
    });

    return response.data;
};

export default function useComponent(href: string, pollInterval?: number) {
    const { version } = usePage<SharedData>();
    const [component, setComponent] = useState<null | {
        default: ComponentType<Record<string, unknown>>;
    }>(null);
    const [props, setProps] = useState<Record<string, unknown> | null>(null);
    const [error, setError] = useState<Error | null>(null);

    useEffect(() => {
        if (!version) return;
        let mounted = true;
        let timer: ReturnType<typeof setInterval> | undefined;

        const run = async () => {
            try {
                const response = await fetchComponent(href, String(version));
                if (!mounted) return;

                const componentModule = await resolvePageComponent(
                    `../pages/${response.component}.tsx`,
                    import.meta.glob("../pages/**/*.tsx"),
                );

                if (!mounted) return;

                setComponent(
                    componentModule as {
                        default: ComponentType<Record<string, unknown>>;
                    },
                );
                setProps(response.props ?? {});
            } catch (err) {
                if (!mounted) return;
                setError(err as Error);
                console.error(err);
            }
        };

        run().then();

        if (pollInterval && pollInterval > 0) {
            timer = setInterval(run, pollInterval);
        }

        return () => {
            mounted = false;
            if (timer) clearInterval(timer);
        };
    }, [href, version, pollInterval]);

    return {
        component,
        props,
        error,
    };
}
