<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Scoped to the layout rather than View::share so console commands and a
     * cold-cache `migrate:fresh` never query site_settings before it exists.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $view->with('portfolioEnabled', self::portfolioEnabled());
        });
    }

    public static function portfolioEnabled(): bool
    {
        if (! Schema::hasTable('site_settings')) {
            return false;
        }

        return SiteSetting::enabled('portfolio_enabled');
    }
}
