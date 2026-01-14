<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Data\PeopleDear\OrganizationData;
use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Middleware;
use Override;
use Sprout\Attributes\CurrentTenant;

final class HandleInertiaRequests extends Middleware
{
    /**
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    public function __construct(
        #[CurrentTenant] private readonly Organization $organization
    ) {}

    /**
     * @see https://inertiajs.com/asset-versioning
     */
    #[Override]
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function share(Request $request): array
    {

        if ($request->wantsDropdown()) {
            return [];
        }

        /** @var User|null $user */
        $user = $request->user();

        $isOrgUri = $request->route()
            !== null
            && Str::startsWith($request->route()->uri, 'org');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'organization' => $this->organization->identifier ? OrganizationData::from($this->organization)->toArray() : null,
            'show' => [
                'employeeLink' => $isOrgUri,
                'orgLink' => ($user?->hasRole([
                    UserRole::PeopleManager,
                    UserRole::Owner,
                    UserRole::Manager,
                ]) ?? false) && ! $isOrgUri,
            ],
            'previousPath' => URL::previousPath(),
        ];

    }
}
