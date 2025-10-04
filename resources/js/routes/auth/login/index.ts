import {
    queryParams,
    type RouteQueryOptions,
    type RouteDefinition,
} from "./../../../wayfinder";
/**
 * @see \App\Http\Controllers\Auth\LoginController::index
 * @see app/Http/Controllers/Auth/LoginController.php:18
 * @route '/login'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<"get"> => ({
    url: index.url(options),
    method: "get",
});

index.definition = {
    methods: ["get", "head"],
    url: "/login",
} satisfies RouteDefinition<["get", "head"]>;

/**
 * @see \App\Http\Controllers\Auth\LoginController::index
 * @see app/Http/Controllers/Auth/LoginController.php:18
 * @route '/login'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Auth\LoginController::index
 * @see app/Http/Controllers/Auth/LoginController.php:18
 * @route '/login'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<"get"> => ({
    url: index.url(options),
    method: "get",
});

/**
 * @see \App\Http\Controllers\Auth\LoginController::index
 * @see app/Http/Controllers/Auth/LoginController.php:18
 * @route '/login'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<"head"> => ({
    url: index.url(options),
    method: "head",
});

/**
 * @see \App\Http\Controllers\Auth\LoginController::store
 * @see app/Http/Controllers/Auth/LoginController.php:23
 * @route '/login'
 */
export const store = (
    options?: RouteQueryOptions,
): RouteDefinition<"post"> => ({
    url: store.url(options),
    method: "post",
});

store.definition = {
    methods: ["post"],
    url: "/login",
} satisfies RouteDefinition<["post"]>;

/**
 * @see \App\Http\Controllers\Auth\LoginController::store
 * @see app/Http/Controllers/Auth/LoginController.php:23
 * @route '/login'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Auth\LoginController::store
 * @see app/Http/Controllers/Auth/LoginController.php:23
 * @route '/login'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<"post"> => ({
    url: store.url(options),
    method: "post",
});

const login = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
};

export default login;
