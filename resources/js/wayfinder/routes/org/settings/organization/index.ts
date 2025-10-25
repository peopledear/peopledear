import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\OrgController::edit
* @see app/Http/Controllers/OrgController.php:17
* @route '/org/settings'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/org/settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OrgController::edit
* @see app/Http/Controllers/OrgController.php:17
* @route '/org/settings'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrgController::edit
* @see app/Http/Controllers/OrgController.php:17
* @route '/org/settings'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrgController::edit
* @see app/Http/Controllers/OrgController.php:17
* @route '/org/settings'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OrgController::update
* @see app/Http/Controllers/OrgController.php:29
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
* @see \App\Http\Controllers\OrgController::update
* @see app/Http/Controllers/OrgController.php:29
* @route '/org/settings/organization'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrgController::update
* @see app/Http/Controllers/OrgController.php:29
* @route '/org/settings/organization'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

const organization = {
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
}

export default organization