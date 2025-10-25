import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\UserEmailResetNotification::create
* @see app/Http/Controllers/UserEmailResetNotification.php:16
* @route '/forgot-password'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/forgot-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserEmailResetNotification::create
* @see app/Http/Controllers/UserEmailResetNotification.php:16
* @route '/forgot-password'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserEmailResetNotification::create
* @see app/Http/Controllers/UserEmailResetNotification.php:16
* @route '/forgot-password'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailResetNotification::create
* @see app/Http/Controllers/UserEmailResetNotification.php:16
* @route '/forgot-password'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserEmailResetNotification::store
* @see app/Http/Controllers/UserEmailResetNotification.php:23
* @route '/forgot-password'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/forgot-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserEmailResetNotification::store
* @see app/Http/Controllers/UserEmailResetNotification.php:23
* @route '/forgot-password'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserEmailResetNotification::store
* @see app/Http/Controllers/UserEmailResetNotification.php:23
* @route '/forgot-password'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

const UserEmailResetNotification = { create, store }

export default UserEmailResetNotification