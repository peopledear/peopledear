import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\OrgOfficeController::store
* @see app/Http/Controllers/OrgOfficeController.php:20
* @route '/org/offices'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/org/offices',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\OrgOfficeController::store
* @see app/Http/Controllers/OrgOfficeController.php:20
* @route '/org/offices'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrgOfficeController::store
* @see app/Http/Controllers/OrgOfficeController.php:20
* @route '/org/offices'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrgOfficeController::store
* @see app/Http/Controllers/OrgOfficeController.php:20
* @route '/org/offices'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrgOfficeController::store
* @see app/Http/Controllers/OrgOfficeController.php:20
* @route '/org/offices'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\OrgOfficeController::update
* @see app/Http/Controllers/OrgOfficeController.php:35
* @route '/org/offices/{office}'
*/
export const update = (args: { office: number | { id: number } } | [office: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/org/offices/{office}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\OrgOfficeController::update
* @see app/Http/Controllers/OrgOfficeController.php:35
* @route '/org/offices/{office}'
*/
update.url = (args: { office: number | { id: number } } | [office: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { office: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { office: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            office: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        office: typeof args.office === 'object'
        ? args.office.id
        : args.office,
    }

    return update.definition.url
            .replace('{office}', parsedArgs.office.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrgOfficeController::update
* @see app/Http/Controllers/OrgOfficeController.php:35
* @route '/org/offices/{office}'
*/
update.put = (args: { office: number | { id: number } } | [office: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\OrgOfficeController::update
* @see app/Http/Controllers/OrgOfficeController.php:35
* @route '/org/offices/{office}'
*/
const updateForm = (args: { office: number | { id: number } } | [office: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrgOfficeController::update
* @see app/Http/Controllers/OrgOfficeController.php:35
* @route '/org/offices/{office}'
*/
updateForm.put = (args: { office: number | { id: number } } | [office: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\OrgOfficeController::destroy
* @see app/Http/Controllers/OrgOfficeController.php:48
* @route '/org/offices/{office}'
*/
export const destroy = (args: { office: number | { id: number } } | [office: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/org/offices/{office}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\OrgOfficeController::destroy
* @see app/Http/Controllers/OrgOfficeController.php:48
* @route '/org/offices/{office}'
*/
destroy.url = (args: { office: number | { id: number } } | [office: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { office: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { office: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            office: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        office: typeof args.office === 'object'
        ? args.office.id
        : args.office,
    }

    return destroy.definition.url
            .replace('{office}', parsedArgs.office.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrgOfficeController::destroy
* @see app/Http/Controllers/OrgOfficeController.php:48
* @route '/org/offices/{office}'
*/
destroy.delete = (args: { office: number | { id: number } } | [office: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\OrgOfficeController::destroy
* @see app/Http/Controllers/OrgOfficeController.php:48
* @route '/org/offices/{office}'
*/
const destroyForm = (args: { office: number | { id: number } } | [office: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrgOfficeController::destroy
* @see app/Http/Controllers/OrgOfficeController.php:48
* @route '/org/offices/{office}'
*/
destroyForm.delete = (args: { office: number | { id: number } } | [office: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const OrgOfficeController = { store, update, destroy }

export default OrgOfficeController