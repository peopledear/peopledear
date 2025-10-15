import {
    queryParams,
    type RouteDefinition,
    type RouteQueryOptions,
} from "./../../../wayfinder";
/**
 * @see \App\Http\Controllers\Auth\LogoutController::store
 * @see app/Http/Controllers/Auth/LogoutController.php:12
 * @route '/logout'
 */
export const store = (
    options?: RouteQueryOptions,
): RouteDefinition<"post"> => ({
    url: store.url(options),
    method: "post",
});

store.definition = {
    methods: ["post"],
    url: "/logout",
} satisfies RouteDefinition<["post"]>;

/**
 * @see \App\Http\Controllers\Auth\LogoutController::store
 * @see app/Http/Controllers/Auth/LogoutController.php:12
 * @route '/logout'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Auth\LogoutController::store
 * @see app/Http/Controllers/Auth/LogoutController.php:12
 * @route '/logout'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<"post"> => ({
    url: store.url(options),
    method: "post",
});

const logout = {
    store: Object.assign(store, store),
};

export default logout;
