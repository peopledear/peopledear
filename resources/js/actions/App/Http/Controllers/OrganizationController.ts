import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\OrganizationController::edit
* @see app/Http/Controllers/OrganizationController.php:17
* @route '/org/settings/organization'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/org/settings/organization',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OrganizationController::edit
* @see app/Http/Controllers/OrganizationController.php:17
* @route '/org/settings/organization'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrganizationController::edit
* @see app/Http/Controllers/OrganizationController.php:17
* @route '/org/settings/organization'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrganizationController::edit
* @see app/Http/Controllers/OrganizationController.php:17
* @route '/org/settings/organization'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OrganizationController::update
* @see app/Http/Controllers/OrganizationController.php:29
* @route '/org/settings/organization'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/org/settings/organization',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\OrganizationController::update
* @see app/Http/Controllers/OrganizationController.php:29
* @route '/org/settings/organization'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrganizationController::update
* @see app/Http/Controllers/OrganizationController.php:29
* @route '/org/settings/organization'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

const OrganizationController = { edit, update }

export default OrganizationController