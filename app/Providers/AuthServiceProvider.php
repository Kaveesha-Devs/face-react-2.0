<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
    \App\Models\User::class       => \App\Policies\UserPolicy::class,
    \App\Models\Company::class    => \App\Policies\CompanyPolicy::class,
    \App\Models\Department::class => \App\Policies\DepartmentPolicy::class,
    \App\Models\ReactLog::class   => \App\Policies\ReactLogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
