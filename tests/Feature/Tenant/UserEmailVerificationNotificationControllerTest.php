<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

beforeEach(function (): void {
    $this->tenant = $this->organization;
});

it('renders verify email page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'email_verified_at' => null,
            ]);

        $response = $this->actingAs($user)
            ->from(route('home', [], false))
            ->get(tenant_route('tenant.auth.verification.notice', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('user-email-verification-notification/create')
                ->has('status'));
    });

it('redirects verified users to dashboard',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'email_verified_at' => now(),
            ]);

        $response = $this->actingAs($user)
            ->from(route('home', [], false))
            ->get(tenant_route('tenant.auth.verification.notice', $this->tenant));

        $response->assertRedirect(tenant_route('tenant.org.overview', $this->tenant));
    });

it('may send verification notification',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        Notification::fake();

        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'email_verified_at' => null,
            ]);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.auth.verification.notice', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.verification.send', $this->tenant));

        $response->assertRedirect(tenant_route('tenant.auth.verification.notice', $this->tenant))
            ->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, VerifyEmail::class);
    });

it('redirects verified users when sending notification',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        Notification::fake();

        $user = User::factory()
            ->for($this->tenant, 'organization')
            ->create([
                'email_verified_at' => now(),
            ]);

        $response = $this->actingAs($user)
            ->from(tenant_route('tenant.auth.verification.notice', $this->tenant, [], false))
            ->post(tenant_route('tenant.auth.verification.send', $this->tenant));

        $response->assertRedirect(tenant_route('tenant.org.overview', $this->tenant));

        Notification::assertNothingSent();
    });
