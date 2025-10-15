import {
    queryParams,
    type RouteDefinition,
    type RouteQueryOptions,
} from "./../../../../../wayfinder";
/**
 * @see \App\Http\Controllers\Profile\UserProfileController::index
 * @see app/Http/Controllers/Profile/UserProfileController.php:19
 * @route '/profile'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<"get"> => ({
    url: index.url(options),
    method: "get",
});

index.definition = {
    methods: ["get", "head"],
    url: "/profile",
} satisfies RouteDefinition<["get", "head"]>;

/**
 * @see \App\Http\Controllers\Profile\UserProfileController::index
 * @see app/Http/Controllers/Profile/UserProfileController.php:19
 * @route '/profile'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Profile\UserProfileController::index
 * @see app/Http/Controllers/Profile/UserProfileController.php:19
 * @route '/profile'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<"get"> => ({
    url: index.url(options),
    method: "get",
});

/**
 * @see \App\Http\Controllers\Profile\UserProfileController::index
 * @see app/Http/Controllers/Profile/UserProfileController.php:19
 * @route '/profile'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<"head"> => ({
    url: index.url(options),
    method: "head",
});

/**
 * @see \App\Http\Controllers\Profile\UserProfileController::update
 * @see app/Http/Controllers/Profile/UserProfileController.php:30
 * @route '/profile'
 */
export const update = (
    options?: RouteQueryOptions,
): RouteDefinition<"put"> => ({
    url: update.url(options),
    method: "put",
});

update.definition = {
    methods: ["put"],
    url: "/profile",
} satisfies RouteDefinition<["put"]>;

/**
 * @see \App\Http\Controllers\Profile\UserProfileController::update
 * @see app/Http/Controllers/Profile/UserProfileController.php:30
 * @route '/profile'
 */
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Profile\UserProfileController::update
 * @see app/Http/Controllers/Profile/UserProfileController.php:30
 * @route '/profile'
 */
update.put = (options?: RouteQueryOptions): RouteDefinition<"put"> => ({
    url: update.url(options),
    method: "put",
});

const UserProfileController = { index, update };

export default UserProfileController;
