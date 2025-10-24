import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
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
* @see \App\Http\Controllers\UserEmailResetNotification::create
* @see app/Http/Controllers/UserEmailResetNotification.php:16
* @route '/forgot-password'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailResetNotification::create
* @see app/Http/Controllers/UserEmailResetNotification.php:16
* @route '/forgot-password'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailResetNotification::create
* @see app/Http/Controllers/UserEmailResetNotification.php:16
* @route '/forgot-password'
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

/**
* @see \App\Http\Controllers\UserEmailResetNotification::store
* @see app/Http/Controllers/UserEmailResetNotification.php:23
* @route '/forgot-password'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserEmailResetNotification::store
* @see app/Http/Controllers/UserEmailResetNotification.php:23
* @route '/forgot-password'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const UserEmailResetNotification = { create, store }

export default UserEmailResetNotification