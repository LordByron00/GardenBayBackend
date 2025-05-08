<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route; // <-- This is the import for the Route facade

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel's authentication capabilities to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When using a controller namespace, it is often helpful to remove the
     * Controller suffix from the controller names in your routes.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers'; // <-- $this->namespace is defined here (often commented out now)

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting(); // <-- This method is defined within this class

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                // ->namespace($this->namespace) // Use $this->namespace if uncommented above
                ->group(base_path('routes/api.php'));

            // Corrected: Completed the web route definition
            Route::middleware('web')
                // ->namespace($this->namespace) // Use $this->namespace if uncommented above
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiting for the application.
     *
     * @return void
     */
    protected function configureRateLimiting() // <-- This method is defined within this class
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Add other rate limiters here if needed
    }
}
