import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import settings from './settings'
import offices from './offices'
/**
* @see \App\Http\Controllers\OrgOverviewController::__invoke
* @see app/Http/Controllers/OrgOverviewController.php:12
* @route '/org'
*/
export const overview = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(options),
    method: 'get',
})

overview.definition = {
    methods: ["get","head"],
    url: '/org',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OrgOverviewController::__invoke
* @see app/Http/Controllers/OrgOverviewController.php:12
* @route '/org'
*/
overview.url = (options?: RouteQueryOptions) => {
    return overview.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrgOverviewController::__invoke
* @see app/Http/Controllers/OrgOverviewController.php:12
* @route '/org'
*/
overview.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OrgOverviewController::__invoke
* @see app/Http/Controllers/OrgOverviewController.php:12
* @route '/org'
*/
overview.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: overview.url(options),
    method: 'head',
})

const org = {
    overview: Object.assign(overview, overview),
    settings: Object.assign(settings, settings),
    offices: Object.assign(offices, offices),
}

export default org