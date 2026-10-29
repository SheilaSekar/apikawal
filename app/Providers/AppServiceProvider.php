<?php

namespace App\Providers;

use App\Models\Report;
use App\Models\User;
use App\Policies\ReportPolicy;
use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Services bind automatically; explicit policy registration keeps authorization easy to follow.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Report::class, ReportPolicy::class);
        RateLimiter::for('create-report', fn (Request $request) => Limit::perHour(5)->by((string) $request->user()?->id ?: $request->ip()));
        RateLimiter::for('public-api', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
    }
}
