<?php

declare(strict_types=1);

use App\Mail\UserInvitationMail;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
});

test('admin can resend invitation', function (): void {
    Mail::fake();

    $role = Role::query()->where('name', 'employee')->first();

    $invitation = Invitation::factory()->create([
        'email' => 'resend@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(2),
    ]);

    $originalExpiresAt = $invitation->expires_at;

    $response = actingAs($this->admin)
        ->post(route('admin.invitations.resend', $invitation));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', __('Invitation resent successfully'));

    $invitation->refresh();
    expect($invitation->expires_at->isAfter($originalExpiresAt))->toBeTrue();
});

test('invitation expiration is updated to 7 days on resend', function (): void {
    Mail::fake();

    $role = Role::query()->where('name', 'employee')->first();

    $invitation = Invitation::factory()->create([
        'email' => 'resend@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(1),
    ]);

    actingAs($this->admin)
        ->post(route('admin.invitations.resend', $invitation));

    $invitation->refresh();

    $expectedExpiry = now()->addDays(7);
    $diffInSeconds = abs($invitation->expires_at->diffInSeconds($expectedExpiry));

    expect($diffInSeconds)->toBeLessThan(2);
});

test('email is sent on resend', function (): void {
    Mail::fake();

    $role = Role::query()->where('name', 'employee')->first();

    $invitation = Invitation::factory()->create([
        'email' => 'resend@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(2),
    ]);

    actingAs($this->admin)
        ->post(route('admin.invitations.resend', $invitation));

    Mail::assertSent(UserInvitationMail::class, function (UserInvitationMail $mail) use ($invitation) {
        return $mail->hasTo($invitation->email)
            && $mail->invitation->id === $invitation->id;
    });
});

test('cannot resend accepted invitation', function (): void {
    Mail::fake();

    $role = Role::query()->where('name', 'employee')->first();

    $invitation = Invitation::factory()->create([
        'email' => 'accepted@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => now(),
        'expires_at' => now()->addDays(7),
    ]);

    $response = actingAs($this->admin)
        ->post(route('admin.invitations.resend', $invitation));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHasErrors(['invitation' => 'This invitation has already been accepted.']);

    Mail::assertNotSent(UserInvitationMail::class);
});

test('can resend expired invitation', function (): void {
    Mail::fake();

    $role = Role::query()->where('name', 'employee')->first();

    $invitation = Invitation::factory()->create([
        'email' => 'expired@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => null,
        'expires_at' => now()->subDay(),
    ]);

    $response = actingAs($this->admin)
        ->post(route('admin.invitations.resend', $invitation));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', __('Invitation resent successfully'));

    $invitation->refresh();
    expect($invitation->expires_at->isFuture())->toBeTrue();

    Mail::assertSent(UserInvitationMail::class);
});

test('non-admin cannot resend invitation', function (): void {
    Mail::fake();

    $employee = User::factory()->employee()->create();
    $role = Role::query()->where('name', 'employee')->first();

    $invitation = Invitation::factory()->create([
        'email' => 'resend@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(2),
    ]);

    $response = actingAs($employee)
        ->post(route('admin.invitations.resend', $invitation));

    $response->assertForbidden();

    Mail::assertNotSent(UserInvitationMail::class);
});

test('manager cannot resend invitation', function (): void {
    Mail::fake();

    $manager = User::factory()->manager()->create();
    $role = Role::query()->where('name', 'employee')->first();

    $invitation = Invitation::factory()->create([
        'email' => 'resend@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(2),
    ]);

    $response = actingAs($manager)
        ->post(route('admin.invitations.resend', $invitation));

    $response->assertForbidden();

    Mail::assertNotSent(UserInvitationMail::class);
});

test('requires authentication to resend invitation', function (): void {
    Mail::fake();

    $role = Role::query()->where('name', 'employee')->first();

    $invitation = Invitation::factory()->create([
        'email' => 'resend@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(2),
    ]);

    $response = post(route('admin.invitations.resend', $invitation));

    $response->assertRedirect(route('auth.login.index'));

    Mail::assertNotSent(UserInvitationMail::class);
});

test('handles resending non-existent invitation', function (): void {
    Mail::fake();

    $response = actingAs($this->admin)
        ->post(route('admin.invitations.resend', 99999));

    $response->assertNotFound();

    Mail::assertNotSent(UserInvitationMail::class);
});

test('inactive admin cannot resend invitation', function (): void {
    Mail::fake();

    $inactiveAdmin = User::factory()->admin()->inactive()->create();
    $role = Role::query()->where('name', 'employee')->first();

    $invitation = Invitation::factory()->create([
        'email' => 'resend@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => null,
        'expires_at' => now()->addDays(2),
    ]);

    $response = actingAs($inactiveAdmin)
        ->post(route('admin.invitations.resend', $invitation));

    $response->assertForbidden();

    Mail::assertNotSent(UserInvitationMail::class);
});
