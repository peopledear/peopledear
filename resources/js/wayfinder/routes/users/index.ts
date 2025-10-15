import {
    queryParams,
    type RouteDefinition,
    type RouteQueryOptions,
} from "./../../wayfinder";
/**
 * @see \App\Http\Controllers\Admin\UserController::index
 * @see app/Http/Controllers/Admin/UserController.php:12
 * @route '/users'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<"get"> => ({
    url: index.url(options),
    method: "get",
});

index.definition = {
    methods: ["get", "head"],
    url: "/users",
} satisfies RouteDefinition<["get", "head"]>;

/**
 * @see \App\Http\Controllers\Admin\UserController::index
 * @see app/Http/Controllers/Admin/UserController.php:12
 * @route '/users'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Admin\UserController::index
 * @see app/Http/Controllers/Admin/UserController.php:12
 * @route '/users'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<"get"> => ({
    url: index.url(options),
    method: "get",
});

/**
 * @see \App\Http\Controllers\Admin\UserController::index
 * @see app/Http/Controllers/Admin/UserController.php:12
 * @route '/users'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<"head"> => ({
    url: index.url(options),
    method: "head",
});

const users = {
    index: Object.assign(index, index),
};

export default users;
