<?php

namespace App\Providers;

use App\Models\Project;
use App\Policies\ProjectPolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Project::class, ProjectPolicy::class);
        config(['app.login_route' => 'account.sign-in']);
        config(['app.register_route' => 'account.sign-up']);

        Password::defaults(fn () => 
            Password::min(8)
            ->numbers()
            ->mixedCase()
            ->uncompromised()
        );
    }
}
