<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

beforeEach(function (): void {
    $this->tenant = $this->organization;
});

it('renders forgot password page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->get(tenant_route('tenant.auth.password.request', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('user-email-reset-notification/create')
                ->has('status'));
    });

it('may send password reset notification',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        Notification::fake();

        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'email' => 'test@example.com',
            ]);

        $response = $this->from(tenant_route('tenant.auth.password.request', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.password.email', $this->tenant), [
                'email' => 'test@example.com',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.password.request', $this->tenant))
            ->assertSessionHas('status', 'A reset link will be sent if the account exists.');

        Notification::assertSentTo($user, ResetPassword::class);
    });

it('returns generic message for non-existent email',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        Notification::fake();

        $response = $this->from(tenant_route('tenant.auth.password.request', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.password.email', $this->tenant), [
                'email' => 'nonexistent@example.com',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.password.request', $this->tenant))
            ->assertSessionHas('status', 'A reset link will be sent if the account exists.');

        Notification::assertNothingSent();
    });

it('requires email',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->from(tenant_route('tenant.auth.password.request', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.password.email', $this->tenant), []);

        $response->assertRedirect(tenant_route('tenant.auth.password.request', $this->tenant))
            ->assertSessionHasErrors('email');
    });

it('requires valid email format',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->from(tenant_route('tenant.auth.password.request', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.password.email', $this->tenant), [
                'email' => 'not-an-email',
            ]);

        $response->assertRedirect(tenant_route('tenant.auth.password.request', $this->tenant))
            ->assertSessionHasErrors('email');
    });

it('redirects authenticated users away from forgot password',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create();

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.org.overview', $this->tenant, [], false))
            ->get(tenant_route('tenant.auth.password.request', $this->tenant));

        $response->assertRedirect(tenant_route('tenant.org.overview', $this->tenant));
    });
