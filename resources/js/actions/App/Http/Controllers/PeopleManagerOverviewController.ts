import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
const PeopleManagerOverviewController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PeopleManagerOverviewController.url(options),
    method: 'get',
})

PeopleManagerOverviewController.definition = {
    methods: ["get","head"],
    url: '/people-manager',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
PeopleManagerOverviewController.url = (options?: RouteQueryOptions) => {
    return PeopleManagerOverviewController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
PeopleManagerOverviewController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PeopleManagerOverviewController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
PeopleManagerOverviewController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: PeopleManagerOverviewController.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
const PeopleManagerOverviewControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PeopleManagerOverviewController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
PeopleManagerOverviewControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PeopleManagerOverviewController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
PeopleManagerOverviewControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: PeopleManagerOverviewController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

PeopleManagerOverviewController.form = PeopleManagerOverviewControllerForm

export default PeopleManagerOverviewController