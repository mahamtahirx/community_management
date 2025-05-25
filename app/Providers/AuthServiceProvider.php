<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Community;
use App\Policies\CommunityPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Community::class => CommunityPolicy::class,
        // Add other model-policy mappings here as needed
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define additional gates if needed
        Gate::define('manage-community', function ($user, Community $community) {
            return $user->communities()
                ->where('community_id', $community->id)
                ->whereIn('role', ['admin'])
                ->exists();
        });

        Gate::define('organize-events', function ($user, Community $community) {
            return $user->communities()
                ->where('community_id', $community->id)
                ->whereIn('role', ['admin', 'organizer'])
                ->exists();
        });
    }
}