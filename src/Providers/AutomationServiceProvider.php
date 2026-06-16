<?php

namespace Dev3bdulrahman\Automations\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Dev3bdulrahman\Automations\Events\WorkflowExecuted;
use Dev3bdulrahman\Automations\Models\Workflow;
use Dev3bdulrahman\Automations\Policies\WorkflowPolicy;
use Livewire\Livewire;

class AutomationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Views', 'automations');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../Translations', 'automations');

        // Register Policies
        Gate::policy(Workflow::class, WorkflowPolicy::class);

        // Register Events
        Event::listen(WorkflowExecuted::class, []);

        // Register Livewire Components
        if (class_exists(Livewire::class)) {
            Livewire::component('automations-workflows', \Dev3bdulrahman\Automations\Http\Controllers\Web\Admin\Automations\Workflows::class);
            Livewire::component('automations-webhooks', \Dev3bdulrahman\Automations\Http\Controllers\Web\Admin\Automations\Webhooks::class);
        }
    }
}
