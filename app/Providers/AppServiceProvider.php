<?php

namespace App\Providers;

use App\Models\Project;
use App\Policies\ProjectPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('title', 'No title');

        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::define('manage-cv', fn ($user) => $user->hasRole('admin'));
        config(['app.login_route' => 'account.sign-in']);
        config(['app.register_route' => 'account.sign-up']);

        // Contact form: a person rarely sends more than one or two messages.
        RateLimiter::for('contact', fn (Request $request) => [
            Limit::perMinutes(10, 3)->by('10m:'.$request->ip()),
            Limit::perDay(10)->by('day:'.$request->ip()),
        ]);

        Password::defaults(fn () => 
            Password::min(8)
            ->numbers()
            ->mixedCase()
            ->uncompromised()
        );

    }
}
