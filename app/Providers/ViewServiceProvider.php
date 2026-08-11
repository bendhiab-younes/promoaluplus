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
     * Scoped to the public layout and page views rather than View::share so
     * console commands and a cold-cache `migrate:fresh` never query
     * site_settings before it exists.
     *
     * `pages.*` is listed explicitly because `@extends('layouts.app')` renders
     * the child view (and its sections) *before* the layout, so a composer
     * bound only to `layouts.app` fires too late for anything inside
     * `resources/views/pages`.
     */
    public function boot(): void
    {
        View::composer(['layouts.app', 'pages.*'], function ($view): void {
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
