<?php

namespace App\Providers;

use App\Services\Gepg\GepgService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // One instance per request, so the simulator's held callbacks survive until flushed.
        $this->app->singleton(GepgService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Inactivity time-out (eGA). Sanctum calls this before it stamps
        // last_used_at, so the token's last use is still the previous request.
        \Laravel\Sanctum\Sanctum::authenticateAccessTokensUsing(function ($token, bool $isValid) {
            $idle = (int) config('gememis.idle_minutes');
            $last = $token->last_used_at ?? $token->created_at;

            if ($isValid && $idle > 0 && $last && $last->lt(now()->subMinutes($idle))) {
                $user = $token->tokenable;
                \App\Models\AuditLog::create([
                    'user_id' => $user?->id, 'user_name' => $user?->name ?? 'System', 'action' => 'auth.timeout',
                    'reference' => $user?->email, 'entity_type' => 'User', 'entity_id' => $user?->id,
                    'summary' => ($user?->name ?? 'A user')." was signed out after $idle minutes without activity",
                    'ip_address' => request()?->ip(),
                ]);
                $token->delete();

                return false;
            }

            return $isValid;
        });
    }
}
