<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\RetailerWebManagement;
use Illuminate\Support\Facades\Gate;

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
        Gate::define('view-logviewer', function ($user) {
            return $user->email === 'mustafa786@gmail.com'; // Customize as needed
        });
        // to load data in header
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $retailer = RetailerWebManagement::where('retailer_id', Auth::id())->first();
                $view->with('retailer', $retailer);
            }
        });
    }
}
