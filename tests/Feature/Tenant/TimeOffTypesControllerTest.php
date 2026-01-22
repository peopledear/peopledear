<?php

declare(strict_types=1);

use App\Enums\BalanceType;
use App\Enums\Icon;
use App\Enums\TimeOffUnit;
use App\Models\TimeOffType;
use Sprout\Exceptions\MisconfigurationException;

use function App\tenant_route;

beforeEach(
    /**
     * @throws Throwable
     */
    function (): void {});

test('creates a new time off type',
    /**
     * @throws MisconfigurationException
     */
    function (): void {

        $response = $this->actingAs($this->peopleManager)
            ->post(tenant_route('tenant.settings.time-off-types.store', $this->tenant), [
                'name' => 'Work From Home',
                'icon' => Icon::LucideHome->value,
                'color' => '#34D399',
                'balance_mode' => BalanceType::Recurring->value,
                'allowed_units' => [TimeOffUnit::Day->value],
                'requires_approval' => true,
                'requires_justification' => false,
                'requires_justification_document' => false,

            ]);

        expect($response->status())
            ->toBe(302)
            ->and(
                TimeOffType::query()
                    ->where('name', 'Work From Home')
                    ->exists()
            )
            ->toBeTrue();

    });

test('renders the create page',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        TimeOffType::factory()
            ->for($this->organization)
            ->count(3)
            ->create();

        $response = $this->actingAs($this->peopleManager)
            ->get(tenant_route('tenant.settings.time-off-types.create', $this->tenant));

        $response->assertOk();

    });

test('people manager can view time off types index',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        TimeOffType::factory()
            ->for($this->organization)
            ->count(3)
            ->create();

        $response = $this->actingAs($this->peopleManager)
            ->get(tenant_route('tenant.settings.time-off-types.index', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('org-time-off-types/index')
                ->has('timeOffTypes', 3)
            );
    });

test('unauthenticated user is redirected to login',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->get(tenant_route('tenant.settings.time-off-types.index', $this->tenant));

        $response->assertRedirect(tenant_route('tenant.auth.login', $this->tenant));
    });

test('returns empty collection when no time off types exist',
    /**
     * @throws MisconfigurationException
     */
    function (): void {
        $response = $this->actingAs($this->peopleManager)
            ->get(tenant_route('tenant.settings.time-off-types.index', $this->tenant));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('org-time-off-types/index')
                ->has('timeOffTypes', 0)
            );
    });
