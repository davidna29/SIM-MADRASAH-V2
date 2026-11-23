<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\Setting;
use App\Models\StudentEnrollment;
use App\Observers\EmployeeObserver;
use App\Observers\StudentEnrollmentObserver;
use App\Policies\PortofolioPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)
            ->by($request->user() ? 'user:'.$request->user()->id : 'ip:'.$request->ip()));

        RateLimiter::for('api-login', fn (Request $request) => [
            Limit::perMinute(5)->by('login-ip:'.$request->ip()),
            Limit::perMinute(5)->by('login:'.mb_strtolower((string) $request->input('login'))),
        ]);

        RateLimiter::for('api-write', fn (Request $request) => Limit::perMinute(30)
            ->by($request->user() ? 'user:'.$request->user()->id : 'ip:'.$request->ip()));

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
