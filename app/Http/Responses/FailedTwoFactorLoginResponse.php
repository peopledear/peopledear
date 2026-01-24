<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse as FailedTwoFactorLoginResponseContract;
use Sprout\Attributes\CurrentTenant;
use Sprout\Exceptions\MisconfigurationException;
use Symfony\Component\HttpFoundation\Response;

use function App\tenant_route;

final readonly class FailedTwoFactorLoginResponse implements FailedTwoFactorLoginResponseContract
{
    public function __construct(
        #[CurrentTenant] private Organization $tenant,
    ) {}

    /**
     * @param  Request  $request
     *
     * @throws MisconfigurationException
     */
    public function toResponse($request): Response
    {
        [$key, $message] = $request->filled('recovery_code')
            ? ['recovery_code', __('The provided two factor recovery code was invalid.')]
            : ['code', __('The provided two factor authentication code was invalid.')];

        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                $key => [$message],
            ]);
        }

        return redirect()
            ->to(tenant_route('tenant.auth.two-factor.login', $this->tenant))
            ->withErrors([$key => $message]);
    }
}
