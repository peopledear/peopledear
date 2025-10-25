import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\OrganizationOverviewController::__invoke
* @see app/Http/Controllers/OrganizationOverviewController.php:12
* @route '/org'
*/
const OrganizationOverviewController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: OrganizationOverviewController.url(options),
    method: 'get',
})

OrganizationOverviewController.definition = {
    methods: ["get","head"],
    url: '/org',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OrganizationOverviewController::__invoke
* @see app/Http/Controllers/OrganizationOverviewController.php:12
* @route '/org'
*/
OrganizationOverviewController.url = (options?: RouteQueryOptions) => {
    return OrganizationOverviewController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrganizationOverviewController::__invoke
* @see app/Http/Controllers/OrganizationOverviewController.php:12
* @route '/org'
*/
OrganizationOverviewController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: OrganizationOverviewController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrganizationOverviewController::__invoke
* @see app/Http/Controllers/OrganizationOverviewController.php:12
* @route '/org'
*/
OrganizationOverviewController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: OrganizationOverviewController.url(options),
    method: 'head',
})

export default OrganizationOverviewController