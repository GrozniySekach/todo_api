<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;

use App\Models\Profile;
use App\Models\Project;
use App\Models\Task;
use App\Policies\ProfilePolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Profile::class => ProfilePolicy::class,
        Project::class => ProjectPolicy::class,
        Task::class => TaskPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
