import {
    queryParams,
    type RouteDefinition,
    type RouteQueryOptions,
} from "./../wayfinder";
/**
 * @see routes/web.php:10
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
 * @see routes/web.php:10
 * @route '/'
 */
welcome.url = (options?: RouteQueryOptions) => {
    return welcome.definition.url + queryParams(options);
};

/**
 * @see routes/web.php:10
 * @route '/'
 */
welcome.get = (options?: RouteQueryOptions): RouteDefinition<"get"> => ({
    url: welcome.url(options),
    method: "get",
});

/**
 * @see routes/web.php:10
 * @route '/'
 */
welcome.head = (options?: RouteQueryOptions): RouteDefinition<"head"> => ({
    url: welcome.url(options),
    method: "head",
});

/**
 * @see \App\Http\Controllers\DashboardController::dashboard
 * @see app/Http/Controllers/DashboardController.php:12
 * @route '/dashboard'
 */
export const dashboard = (
    options?: RouteQueryOptions,
): RouteDefinition<"get"> => ({
    url: dashboard.url(options),
    method: "get",
});

dashboard.definition = {
    methods: ["get", "head"],
    url: "/dashboard",
} satisfies RouteDefinition<["get", "head"]>;

/**
 * @see \App\Http\Controllers\DashboardController::dashboard
 * @see app/Http/Controllers/DashboardController.php:12
 * @route '/dashboard'
 */
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\DashboardController::dashboard
 * @see app/Http/Controllers/DashboardController.php:12
 * @route '/dashboard'
 */
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<"get"> => ({
    url: dashboard.url(options),
    method: "get",
});

/**
 * @see \App\Http\Controllers\DashboardController::dashboard
 * @see app/Http/Controllers/DashboardController.php:12
 * @route '/dashboard'
 */
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<"head"> => ({
    url: dashboard.url(options),
    method: "head",
});
