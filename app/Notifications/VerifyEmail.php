<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Override;

final class VerifyEmail extends BaseVerifyEmail
{
    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  User  $notifiable
     */
    #[Override]
    protected function verificationUrl($notifiable): string
    {
        if (self::$createUrlCallback) {
            return call_user_func(self::$createUrlCallback, $notifiable);
        }

        return URL::temporarySignedRoute(
            'tenant.auth.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'tenant' => $notifiable->organization->identifier,
                'id' => $notifiable->getKey(),
                'hash' => sha1((string) $notifiable->getEmailForVerification()),
            ]
        );
    }
}
