<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\Setting;
use App\Models\StudentEnrollment;
use App\Observers\EmployeeObserver;
use App\Observers\StudentEnrollmentObserver;
use App\Policies\PortofolioPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('portfolio.viewAny', [PortofolioPolicy::class, 'viewAny']);
        Gate::define('portfolio.view', [PortofolioPolicy::class, 'view']);

        // Sinkronisasi akun dengan data master (provisioning & deaktivasi otomatis).
        Employee::observe(EmployeeObserver::class);
        StudentEnrollment::observe(StudentEnrollmentObserver::class);

        View::composer(['components.layouts.publik', 'pages.publik.*'], function (\Illuminate\View\View $view): void {
            $settings = $view->getData()['siteSettings'] ?? Setting::getAll();

            $view->with('siteSettings', $settings)
                ->with('siteLogoUrl', Setting::logoUrl($settings));
        });
    }
}
