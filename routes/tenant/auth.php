<?php

declare(strict_types=1);

use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserEmailResetNotification;
use App\Http\Controllers\UserEmailVerification;
use App\Http\Controllers\UserEmailVerificationNotificationController;
use App\Http\Controllers\UserPasswordController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;

Route::middleware('guest')->group(function (): void {

    // User Password...
    Route::get('reset-password/{token}', [UserPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [UserPasswordController::class, 'store'])
        ->name('password.store');

    // User Email Reset Notification...
    Route::get('forgot-password', [UserEmailResetNotification::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [UserEmailResetNotification::class, 'store'])
        ->name('password.email');

    // Session...
    Route::get('login', [SessionController::class, 'create'])
        ->name('login');
    Route::post('login', [SessionController::class, 'store'])
        ->name('login.store');

    // Two-Factor Challenge...
    Route::get('two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
        ->name('two-factor.login');
    Route::post('two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->name('two-factor.login.store');
});

Route::middleware('auth')->group(function (): void {
    // User Email Verification...
    Route::get('verify-email', [UserEmailVerificationNotificationController::class, 'create'])
        ->name('verification.notice');

    Route::post('verify-email', [UserEmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // User Email Verification...
    Route::get('verify-email/{id}/{hash}', [UserEmailVerification::class, 'update'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Session...
    Route::post('logout', [SessionController::class, 'destroy'])
        ->name('logout');

    Route::get('user/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('user/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->name('password.confirm.store');

    Route::post('user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
        ->name('two-factor.enable');
    Route::delete('user/two-factor', [TwoFactorAuthenticationController::class, 'destroy'])
        ->name('two-factor.disable');

    // Password Confirmation Status...
    Route::get('user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
        ->name('password.confirmation');

    // Confirm Two-Factor Authentication...
    Route::post('user/confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
        ->name('two-factor.confirm');

    // Two-Factor QR Code...
    Route::get('user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
        ->name('two-factor.qr-code');

    // Two-Factor Recovery Codes...
    Route::get('user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
        ->name('two-factor.recovery-codes');
    Route::post('user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
        ->name('two-factor.regenerate-recovery-codes');

    // Two-Factor Secret Key...
    Route::get('user/two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show'])
        ->name('two-factor.secret-key');
});
