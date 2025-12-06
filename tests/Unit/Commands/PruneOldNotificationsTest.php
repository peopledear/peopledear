<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Session;

test('notifications older than 90 days are pruned', function (): void {
    $organization = Organization::factory()->create();

    /** @var User $user */
    $user = User::factory()->create();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->create();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('Old', 'Old notification'));

    $oldNotification = $user->notifications()->first();
    $oldNotification?->update(['created_at' => now()->subDays(91)]);

    $user->notify(new GeneralNotification('Recent', 'Recent notification'));

    expect($user->notifications()->count())->toBe(2);

    $this->artisan('model:prune', ['--model' => [Notification::class]])
        ->assertExitCode(0);

    expect($user->notifications()->count())->toBe(1)
        ->and($user->notifications()->first()?->data['title'])->toBe('Recent');
});

test('notifications exactly 89 days old are not pruned', function (): void {
    $organization = Organization::factory()->create();

    /** @var User $user */
    $user = User::factory()->create();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->create();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('Boundary', 'Boundary notification'));

    $notification = $user->notifications()->first();
    $notification?->update(['created_at' => now()->subDays(89)]);

    $this->artisan('model:prune', ['--model' => [Notification::class]])
        ->assertExitCode(0);

    expect($user->notifications()->count())->toBe(1);
});

test('recent notifications are not pruned', function (): void {
    $organization = Organization::factory()->create();

    /** @var User $user */
    $user = User::factory()->create();

    Employee::factory()
        ->for($organization)
        ->for($user)
        ->create();

    Session::put('current_organization', $organization->id);

    $user->notify(new GeneralNotification('Recent 1', 'Message 1'));
    $user->notify(new GeneralNotification('Recent 2', 'Message 2'));

    $this->artisan('model:prune', ['--model' => [Notification::class]])
        ->assertExitCode(0);

    expect($user->notifications()->count())->toBe(2);
});
