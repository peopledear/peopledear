import {
    queryParams,
    type RouteQueryOptions,
    type RouteDefinition,
} from "./../wayfinder";
/**
 * @see routes/web.php:9
 * @route '/'
 */
export const welcome = (
    options?: RouteQueryOptions,
): RouteDefinition<"get"> => ({
    url: welcome.url(options),
    method: "get",
});

welcome.definition = {
    methods: ["get", "head"],
    url: "/",
} satisfies RouteDefinition<["get", "head"]>;

/**
 * @see routes/web.php:9
 * @route '/'
 */
welcome.url = (options?: RouteQueryOptions) => {
    return welcome.definition.url + queryParams(options);
};

/**
 * @see routes/web.php:9
 * @route '/'
 */
welcome.get = (options?: RouteQueryOptions): RouteDefinition<"get"> => ({
    url: welcome.url(options),
    method: "get",
});

/**
 * @see routes/web.php:9
 * @route '/'
 */
welcome.head = (options?: RouteQueryOptions): RouteDefinition<"head"> => ({
    url: welcome.url(options),
    method: "head",
});
