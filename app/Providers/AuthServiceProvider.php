<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        $permissionAliases = [];
        foreach (config('navigation') as [, , , $submenus]) {
            foreach ($submenus as [, , $actions]) {
                $permissionAliases += $actions;
            }
        }

        Gate::before(function ($user, $ability) use ($permissionAliases) {
            if ($user->hasRole('Super Admin')) {
                return true;
            }

            $permission = $permissionAliases[$ability] ?? null;

            return $permission && $user->checkPermissionTo($permission, 'web') ? true : null;
        });
    }
}
