<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\ActivityHelper;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the getActivityIcon and getActivityTitle functions as Blade directives
        Blade::directive('activityIcon', function ($expression) {
            return "<?php echo \App\Helpers\ActivityHelper::getActivityIcon($expression); ?>";
        });

        Blade::directive('activityTitle', function ($expression) {
            return "<?php echo \App\Helpers\ActivityHelper::getActivityTitle($expression); ?>";
        });
    }
}
