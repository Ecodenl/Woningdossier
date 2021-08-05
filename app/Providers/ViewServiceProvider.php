<?php

namespace App\Providers;

use App\Http\ViewComposers\Frontend\Layouts\Parts\SubNavComposer;
use App\Http\ViewComposers\Frontend\Tool\QuickScanComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
        \View::creator('cooperation.frontend.layouts.parts.sub-nav', SubNavComposer::class);
        \View::creator('cooperation.frontend.tool.quick-scan.index', QuickScanComposer::class);

        // https://stackoverflow.com/questions/38135455/how-to-have-one-time-push-in-laravel-blade
        // TODO: Deprecate this to @once when this is updated to Laravel 7.25
        Blade::directive('pushonce', function ($expression) {
            $domain = explode(':', trim(substr($expression, 1, -1)));
            $push_name = $domain[0];
            $push_sub = $domain[1];
            $isDisplayed = '__pushonce_'.$push_name.'_'.$push_sub;
            return "<?php if(!isset(\$__env->{$isDisplayed})): \$__env->{$isDisplayed} = true; \$__env->startPush('{$push_name}'); ?>";
        });
        Blade::directive('endpushonce', function ($expression) {
            return '<?php $__env->stopPush(); endif; ?>';
        });

    }
}
