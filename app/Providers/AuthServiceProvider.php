<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for admin capabilities
        Gate::define('export_leads', function ($user) {
            return $user->hasCapability('export_leads');
        });

        Gate::define('import_leads', function ($user) {
            return $user->hasCapability('import_leads');
        });

        Gate::define('manage_users', function ($user) {
            return $user->hasCapability('manage_users');
        });

        Gate::define('manage_roles', function ($user) {
            return $user->hasCapability('manage_roles');
        });

        Gate::define('manage_settings', function ($user) {
            return $user->hasCapability('manage_settings');
        });
    }
} 