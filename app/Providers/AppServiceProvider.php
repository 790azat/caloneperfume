<?php

namespace App\Providers;

use App\Models\User;
use Database\Seeders\InstagramSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // <x-layouts::app> / <x-layouts::admin>
        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->prepareVercelDatabase();
    }

    /**
     * Vercel has no deploy hook for PHP, so the first request of each deployment runs pending
     * migrations (and seeds the admin + starter content into an empty database).
     * Without DB_CONNECTION the site runs on a throwaway SQLite demo database in /tmp.
     */
    private function prepareVercelDatabase(): void
    {
        if (! env('VERCEL') || $this->app->runningInConsole()) {
            return;
        }

        $connection = config('database.default');
        $marker = '/tmp/migrated-'.(env('VERCEL_DEPLOYMENT_ID') ?: 'deployment').'-'.$connection;
        if (file_exists($marker)) {
            return;
        }

        if ($connection === 'sqlite') {
            $path = config('database.connections.sqlite.database');
            if (! str_starts_with($path, '/tmp/')) {
                return;
            }
            touch($path);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            if (! User::where('is_admin', true)->exists()) {
                Artisan::call('db:seed', ['--force' => true]);
            } else {
                // Posts added to the starter manifest since the last deployment.
                Artisan::call('db:seed', ['--class' => InstagramSeeder::class, '--force' => true]);
            }
            touch($marker);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
