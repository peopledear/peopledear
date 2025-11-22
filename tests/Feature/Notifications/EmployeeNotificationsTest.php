<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Session;

test('employee can receive notifications', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $employee->notify(new GeneralNotification('Employee Notification', 'Message for employee'));

    expect($employee->notifications)->toHaveCount(1)
        ->and($employee->notifications->first()?->data['title'])->toBe('Employee Notification');
});

test('employee unread notifications count', function (): void {
    $organization = Organization::factory()->createQuietly();

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->for($organization)
        ->createQuietly();

    Session::put('current_organization', $organization->id);

    $employee->notify(new GeneralNotification('Title 1', 'Message 1'));
    $employee->notify(new GeneralNotification('Title 2', 'Message 2'));

    expect($employee->unreadNotifications)->toHaveCount(2);

    $employee->notifications->first()?->markAsRead();

    expect($employee->fresh()?->unreadNotifications)->toHaveCount(1);
});
