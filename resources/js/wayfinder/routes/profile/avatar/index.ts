import {
    queryParams,
    type RouteDefinition,
    type RouteQueryOptions,
} from "./../../../wayfinder";
/**
 * @see \App\Http\Controllers\Profile\UserAvatarController::destroy
 * @see app/Http/Controllers/Profile/UserAvatarController.php:14
 * @route '/profile/avatar'
 */
export const destroy = (
    options?: RouteQueryOptions,
): RouteDefinition<"delete"> => ({
    url: destroy.url(options),
    method: "delete",
});

destroy.definition = {
    methods: ["delete"],
    url: "/profile/avatar",
} satisfies RouteDefinition<["delete"]>;

/**
 * @see \App\Http\Controllers\Profile\UserAvatarController::destroy
 * @see app/Http/Controllers/Profile/UserAvatarController.php:14
 * @route '/profile/avatar'
 */
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Profile\UserAvatarController::destroy
 * @see app/Http/Controllers/Profile/UserAvatarController.php:14
 * @route '/profile/avatar'
 */
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<"delete"> => ({
    url: destroy.url(options),
    method: "delete",
});

const avatar = {
    destroy: Object.assign(destroy, destroy),
};

export default avatar;
