import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\OrgOverviewController::__invoke
* @see app/Http/Controllers/OrgOverviewController.php:12
* @route '/org'
*/
const OrgOverviewController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: OrgOverviewController.url(options),
    method: 'get',
})

OrgOverviewController.definition = {
    methods: ["get","head"],
    url: '/org',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OrgOverviewController::__invoke
* @see app/Http/Controllers/OrgOverviewController.php:12
* @route '/org'
*/
OrgOverviewController.url = (options?: RouteQueryOptions) => {
    return OrgOverviewController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrgOverviewController::__invoke
* @see app/Http/Controllers/OrgOverviewController.php:12
* @route '/org'
*/
OrgOverviewController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: OrgOverviewController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrgOverviewController::__invoke
* @see app/Http/Controllers/OrgOverviewController.php:12
* @route '/org'
*/
OrgOverviewController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: OrgOverviewController.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OrgOverviewController::__invoke
* @see app/Http/Controllers/OrgOverviewController.php:12
* @route '/org'
*/
const OrgOverviewControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: OrgOverviewController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrgOverviewController::__invoke
* @see app/Http/Controllers/OrgOverviewController.php:12
* @route '/org'
*/
OrgOverviewControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: OrgOverviewController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrgOverviewController::__invoke
* @see app/Http/Controllers/OrgOverviewController.php:12
* @route '/org'
*/
OrgOverviewControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: OrgOverviewController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

OrgOverviewController.form = OrgOverviewControllerForm

export default OrgOverviewController