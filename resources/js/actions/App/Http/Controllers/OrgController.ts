import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
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
* @see \App\Http\Controllers\OrgController::edit
* @see app/Http/Controllers/OrgController.php:17
* @route '/org/settings'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrgController::edit
* @see app/Http/Controllers/OrgController.php:17
* @route '/org/settings'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrgController::edit
* @see app/Http/Controllers/OrgController.php:17
* @route '/org/settings'
*/
editForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

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

/**
* @see \App\Http\Controllers\OrgController::update
* @see app/Http/Controllers/OrgController.php:29
* @route '/org/settings/organization'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrgController::update
* @see app/Http/Controllers/OrgController.php:29
* @route '/org/settings/organization'
*/
updateForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const OrgController = { edit, update }

export default OrgController