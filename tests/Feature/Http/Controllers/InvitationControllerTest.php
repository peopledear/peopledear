<?php

declare(strict_types=1);

use App\Mail\UserInvitationMail;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    $this->admin = User::factory()
        ->create(['role_id' => Role::query()->where('name', 'admin')->first()->id]);

    $this->actingAs($this->admin);
});

test('admin can create invitation with valid data', function (): void {
    Mail::fake();

    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $response = $this->post(route('admin.invitations.store'), [
        'email' => 'newuser@example.com',
        'role_id' => $role->id,
    ]);

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', __('Invitation sent successfully'));

    $invitation = Invitation::query()
        ->where('email', 'newuser@example.com')
        ->first();

    expect($invitation)->not->toBeNull()
        ->and($invitation->email)->toBe('newuser@example.com')
        ->and($invitation->role_id)->toBe($role->id)
        ->and($invitation->invited_by)->toBe($this->admin->id)
        ->and($invitation->token)->not->toBeNull()
        ->and($invitation->expires_at)->not->toBeNull()
        ->and($invitation->accepted_at)->toBeNull();
});

test('invitation email is sent when creating invitation', function (): void {
    Mail::fake();

    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $this->post(route('admin.invitations.store'), [
        'email' => 'newuser@example.com',
        'role_id' => $role->id,
    ]);

    Mail::assertSent(UserInvitationMail::class, function (UserInvitationMail $mail) {
        return $mail->hasTo('newuser@example.com');
    });
});

test('cannot send invitation to existing user email', function (): void {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $role = Role::query()->where('name', 'employee')->first();

    $response = $this->post(route('admin.invitations.store'), [
        'email' => 'existing@example.com',
        'role_id' => $role->id,
    ]);

    $response->assertInvalid(['email']);
});

test('cannot send duplicate pending invitation', function (): void {
    $role = Role::query()->where('name', 'employee')->first();

    Invitation::factory()->create([
        'email' => 'pending@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
        'accepted_at' => null,
    ]);

    $response = $this->post(route('admin.invitations.store'), [
        'email' => 'pending@example.com',
        'role_id' => $role->id,
    ]);

    $response->assertInvalid(['email']);
});

test('validates required email when creating invitation', function (): void {
    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $response = $this->post(route('admin.invitations.store'), [
        'role_id' => $role->id,
    ]);

    $response->assertInvalid(['email']);
});

test('validates email format when creating invitation', function (): void {
    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $response = $this->post(route('admin.invitations.store'), [
        'email' => 'invalid-email',
        'role_id' => $role->id,
    ]);

    $response->assertInvalid(['email']);
});

test('validates email max length when creating invitation', function (): void {
    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $response = $this->post(route('admin.invitations.store'), [
        'email' => str_repeat('a', 256).'@example.com',
        'role_id' => $role->id,
    ]);

    $response->assertInvalid(['email']);
});

test('validates required role_id when creating invitation', function (): void {
    $response = $this->post(route('admin.invitations.store'), [
        'email' => 'test@example.com',
    ]);

    $response->assertInvalid(['role_id']);
});

test('validates role_id exists when creating invitation', function (): void {
    $response = $this->post(route('admin.invitations.store'), [
        'email' => 'test@example.com',
        'role_id' => 99999,
    ]);

    $response->assertInvalid(['role_id']);
});

test('admin can delete invitation', function (): void {
    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $invitation = Invitation::factory()->create([
        'email' => 'test@example.com',
        'role_id' => $role->id,
        'invited_by' => $this->admin->id,
    ]);

    $response = $this->delete(route('admin.invitations.destroy', $invitation));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', __('Invitation deleted successfully'));

    expect(Invitation::query()->find($invitation->id))->toBeNull();
});

test('non-admin cannot create invitation', function (): void {
    auth()->logout();

    $employee = User::factory()->employee()->create();
    actingAs($employee);

    $role = Role::query()->where('name', 'employee')->first();

    $response = $this->post(route('admin.invitations.store'), [
        'email' => 'test@example.com',
        'role_id' => $role->id,
    ]);

    $response->assertForbidden();
});

test('non-admin cannot delete invitation', function (): void {
    auth()->logout();

    $employee = User::factory()->employee()->create();
    actingAs($employee);

    $invitation = Invitation::factory()->create();

    $response = $this->delete(route('admin.invitations.destroy', $invitation));

    $response->assertForbidden();
});

test('requires authentication to create invitation', function (): void {
    auth()->logout();

    $role = Role::query()
        ->where('name', 'employee')
        ->first();

    $response = $this->post(route('admin.invitations.store'), [
        'email' => 'test@example.com',
        'role_id' => $role->id,
    ]);

    $response->assertRedirect(route('auth.login.index'));
});

test('requires authentication to delete invitation', function (): void {
    auth()->logout();

    $invitation = Invitation::factory()->create();

    $response = $this->delete(route('admin.invitations.destroy', $invitation));

    $response->assertRedirect(route('auth.login.index'));
});

test('handles deleting non-existent invitation', function (): void {
    $response = $this->delete(route('admin.invitations.destroy', 99999));

    $response->assertNotFound();
});
