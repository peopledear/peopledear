import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
export const overview = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(options),
    method: 'get',
})

overview.definition = {
    methods: ["get","head"],
    url: '/people-manager',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
overview.url = (options?: RouteQueryOptions) => {
    return overview.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
overview.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
overview.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: overview.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
const overviewForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
overviewForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PeopleManagerOverviewController::__invoke
* @see app/Http/Controllers/PeopleManagerOverviewController.php:12
* @route '/people-manager'
*/
overviewForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

overview.form = overviewForm

const peopleManager = {
    overview: Object.assign(overview, overview),
}

export default peopleManager