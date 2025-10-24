import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::notice
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
export const notice = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notice.url(options),
    method: 'get',
})

notice.definition = {
    methods: ["get","head"],
    url: '/verify-email',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::notice
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
notice.url = (options?: RouteQueryOptions) => {
    return notice.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::notice
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
notice.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notice.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::notice
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
notice.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: notice.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::notice
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
const noticeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notice.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::notice
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
noticeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notice.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::notice
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:17
* @route '/verify-email'
*/
noticeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: notice.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

notice.form = noticeForm

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::send
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:24
* @route '/email/verification-notification'
*/
export const send = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

send.definition = {
    methods: ["post"],
    url: '/email/verification-notification',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::send
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:24
* @route '/email/verification-notification'
*/
send.url = (options?: RouteQueryOptions) => {
    return send.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::send
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:24
* @route '/email/verification-notification'
*/
send.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::send
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:24
* @route '/email/verification-notification'
*/
const sendForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserEmailVerificationNotificationController::send
* @see app/Http/Controllers/UserEmailVerificationNotificationController.php:24
* @route '/email/verification-notification'
*/
sendForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: send.url(options),
    method: 'post',
})

send.form = sendForm

/**
* @see \App\Http\Controllers\UserEmailVerification::verify
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
export const verify = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

verify.definition = {
    methods: ["get","head"],
    url: '/verify-email/{id}/{hash}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserEmailVerification::verify
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
verify.url = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions) => {
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

    return verify.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{hash}', parsedArgs.hash.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserEmailVerification::verify
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
verify.get = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerification::verify
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
verify.head = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: verify.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserEmailVerification::verify
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
const verifyForm = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerification::verify
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
verifyForm.get = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserEmailVerification::verify
* @see app/Http/Controllers/UserEmailVerification.php:14
* @route '/verify-email/{id}/{hash}'
*/
verifyForm.head = (args: { id: string | number, hash: string | number } | [id: string | number, hash: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

verify.form = verifyForm

const verification = {
    notice: Object.assign(notice, notice),
    send: Object.assign(send, send),
    verify: Object.assign(verify, verify),
}

export default verification