import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
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
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::create
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::create
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::create
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

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

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::store
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:24
* @route '/email/verification-notification'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::store
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:24
* @route '/email/verification-notification'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const UserEmailVerificationNotificationController = { create, store }

export default UserEmailVerificationNotificationController