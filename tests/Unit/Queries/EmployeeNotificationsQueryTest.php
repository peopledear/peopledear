<?php

declare(strict_types=1);

use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Queries\EmployeeNotificationsQuery;
use Illuminate\Support\Facades\Session;

beforeEach(function (): void {

    $organization = Organization::factory()
        ->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    $this->actingAs($user);

    Session::put('current_organization', $organization->id);

    $this->employee = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    $this->query = app(EmployeeNotificationsQuery::class);

});

test('query returns notifications for user', function (): void {

    $this->employee->notify(new GeneralNotification('Test', 'Test message'));

    $notifications = $this->query->builder()->get();

    expect($notifications)->toHaveCount(1)
        ->and($notifications->first()->data['title'])->toBe('Test');
});

test('query returns unread count', function (): void {

    $this->employee->notify(new GeneralNotification('Test 1', 'Message 1'));
    $this->employee->notify(new GeneralNotification('Test 2', 'Message 2'));

    expect($this->query->unreadCount())->toBe(2);
});

test('query orders unread notifications first', function (): void {

    $this->employee->notify(new GeneralNotification('First', 'First message'));

    $this->travel(1)->minute();

    $this->employee->notify(new GeneralNotification('Second', 'Second message'));

    $this->employee->notifications()->reorder()->oldest()->first()?->markAsRead();

    $notifications = $this->query->builder()->get();

    // Unread (Second) should come before read (First)
    expect($notifications->first()->data['title'])->toBe('Second')
        ->and($notifications->last()->data['title'])->toBe('First');
});

test('unread count returns zero for no notifications', function (): void {

    expect($this->query->unreadCount())->toBe(0);
});

test('unread count excludes read notifications', function (): void {
    $this->employee->notify(new GeneralNotification('Test 1', 'Message 1'));
    $this->employee->notify(new GeneralNotification('Test 2', 'Message 2'));

    // Mark one as read
    $this->employee->notifications()->first()?->markAsRead();

    expect($this->query->unreadCount())->toBe(1);
});
