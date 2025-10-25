import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\UserEmailVerification::update
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
export const update = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: update.url(args, options),
    method: 'get',
})

update.definition = {
    methods: ["get","head"],
    url: '/verify-email/{id}/{hash}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserEmailVerification::update
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
update.url = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            id: args[0],
            hash: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
        hash: args.hash,
    }

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{hash}', parsedArgs.hash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserEmailVerification::update
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
update.get = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: update.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerification::update
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
update.head = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: update.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserEmailVerification::update
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
const updateForm = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: update.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerification::update
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
updateForm.get = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: update.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerification::update
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
updateForm.head = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

update.form = updateForm

const UserEmailVerification = { update }

export default UserEmailVerification