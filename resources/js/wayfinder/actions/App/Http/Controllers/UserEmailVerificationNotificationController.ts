import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::create
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/verify-email',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::create
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::create
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::create
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::store
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:24
* @route '/email/verification-notification'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/email/verification-notification',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::store
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:24
* @route '/email/verification-notification'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::store
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:24
* @route '/email/verification-notification'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

const UserEmailVerificationNotificationController = { create, store }

export default UserEmailVerificationNotificationController