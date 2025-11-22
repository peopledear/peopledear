<?php

declare(strict_types=1);

use App\Enums\PeopleDear\RequestStatus;
use App\Enums\PeopleDear\UserRole;
use App\Models\Approval;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\TimeOffRequest;
use App\Models\User;

beforeEach(function (): void {
    /** @var Organization $organization */
    $organization = Organization::factory()->createQuietly();

    /** @var User $user */
    $user = User::factory()->createQuietly();

    /** @var Employee $manager */
    $manager = Employee::factory()
        ->for($organization)
        ->for($user)
        ->createQuietly();

    $user->assignRole(UserRole::PeopleManager);

    $this->organization = $organization;
    $this->user = $user;
    $this->manager = $manager;
});

test('renders the approval queue page', function (): void {
    /** @var Employee $directReport */
    $directReport = Employee::factory()
        ->for($this->organization)
        ->withManager($this->manager)
        ->createQuietly();

    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->for($this->organization)
        ->for($directReport)
        ->createQuietly();

    Approval::factory()
        ->pending()
        ->for($this->organization)
        ->createQuietly([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    $this->actingAs($this->user)
        ->get('/org/approvals')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('org-approvals/index')
            ->has('pendingApprovals', 1)
        );
});

test('approves a request', function (): void {
    /** @var Employee $directReport */
    $directReport = Employee::factory()
        ->for($this->organization)
        ->withManager($this->manager)
        ->createQuietly();

    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->for($this->organization)
        ->for($directReport)
        ->createQuietly();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($this->organization)
        ->createQuietly([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    $this->actingAs($this->user)
        ->post(sprintf('/org/approvals/%d/approve', $approval->id))
        ->assertRedirect();

    $approval->refresh();

    expect($approval->status)
        ->toBe(RequestStatus::Approved)
        ->and($approval->approved_by)
        ->toBe($this->manager->id);
});

test('rejects a request with reason', function (): void {
    /** @var Employee $directReport */
    $directReport = Employee::factory()
        ->for($this->organization)
        ->withManager($this->manager)
        ->createQuietly();

    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->for($this->organization)
        ->for($directReport)
        ->createQuietly();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($this->organization)
        ->createQuietly([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    $this->actingAs($this->user)
        ->post(sprintf('/org/approvals/%d/reject', $approval->id), [
            'rejection_reason' => 'Team is understaffed',
        ])
        ->assertRedirect();

    $approval->refresh();

    expect($approval->status)
        ->toBe(RequestStatus::Rejected)
        ->and($approval->rejection_reason)
        ->toBe('Team is understaffed')
        ->and($approval->approved_by)
        ->toBe($this->manager->id);
});

test('validates rejection reason is required', function (): void {
    /** @var Employee $directReport */
    $directReport = Employee::factory()
        ->for($this->organization)
        ->withManager($this->manager)
        ->createQuietly();

    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->for($this->organization)
        ->for($directReport)
        ->createQuietly();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($this->organization)
        ->createQuietly([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    $this->actingAs($this->user)
        ->post(sprintf('/org/approvals/%d/reject', $approval->id), [])
        ->assertSessionHasErrors(['rejection_reason']);
});

test('validates rejection reason max length', function (): void {
    /** @var Employee $directReport */
    $directReport = Employee::factory()
        ->for($this->organization)
        ->withManager($this->manager)
        ->createQuietly();

    /** @var TimeOffRequest $timeOffRequest */
    $timeOffRequest = TimeOffRequest::factory()
        ->for($this->organization)
        ->for($directReport)
        ->createQuietly();

    /** @var Approval $approval */
    $approval = Approval::factory()
        ->pending()
        ->for($this->organization)
        ->createQuietly([
            'approvable_type' => TimeOffRequest::class,
            'approvable_id' => $timeOffRequest->id,
        ]);

    $this->actingAs($this->user)
        ->post(sprintf('/org/approvals/%d/reject', $approval->id), [
            'rejection_reason' => str_repeat('a', 1001),
        ])
        ->assertSessionHasErrors(['rejection_reason']);
});
