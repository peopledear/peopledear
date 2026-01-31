<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ValidateCrossDomainToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

use function is_string;
use function redirect;

final class CrossDomainAuthController
{
    /**
     * @throws Throwable
     */
    public function __invoke(
        Request $request,
        ValidateCrossDomainToken $action
    ): RedirectResponse {
        $nonce = $request->query('nonce');

        throw_unless(is_string($nonce), HttpException::class, 403, 'Invalid token');

        try {
            $token = $action->handle($nonce);
        } catch (Throwable) {
            throw new HttpException(403, 'Invalid or expired token');
        }

        Auth::login($token->user);
        $request->session()->regenerate();

        return redirect()->to($token->intended);
    }
}
