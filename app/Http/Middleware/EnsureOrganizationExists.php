<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Actions\SetCurrentOrganization;
use App\Enums\OrganizationExcludedRoute;
use App\Enums\SessionKey;
use App\Models\Organization;
use App\Queries\OrganizationQuery;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureOrganizationExists
{
    public function __construct(
        private OrganizationQuery $organizationQuery,
        private SetCurrentOrganization $setCurrentOrganization,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        if ($request->route() && $request->routeIs(...OrganizationExcludedRoute::values())) {
            return $next($request);
        }

        $currentOrganization = session(SessionKey::CurrentOrganization->value);

        if ($currentOrganization === null) {
            /** @var Organization|null $organization */
            $organization = $this->organizationQuery->builder()->first();

            if ($organization) {
                $this->setCurrentOrganization->handle($organization);

                return $next($request);
            }
        } else {
            return $next($request);
        }

        if ($request->user()->hasRole(['owner', 'people_manager'])) {
            return to_route('org.create')
                ->with('info', 'Please create your organization to continue.');
        }

        return to_route('organization-required')
            ->with('info', "Your organization hasn't been set up yet.");
    }
}
