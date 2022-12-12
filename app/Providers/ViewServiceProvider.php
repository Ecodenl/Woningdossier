<?php

namespace App\Providers;

use App\Http\ViewComposers\AdminComposer;
use App\Http\ViewComposers\CooperationComposer;
use App\Http\ViewComposers\Frontend\Layouts\Parts\SubNavComposer;
use App\Http\ViewComposers\Frontend\Tool\NavbarComposer;
use App\Http\ViewComposers\Cooperation\Admin\Layouts\NavbarComposer as AdminNavbarComposer;
use App\Http\ViewComposers\Frontend\Tool\SimpleScanComposer;
use App\Http\ViewComposers\LayoutComposer;
use App\Http\ViewComposers\ToolComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
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
        $this->app->singleton(ToolComposer::class);
        $this->app->singleton(CooperationComposer::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::creator('cooperation.tool.*', ToolComposer::class);
        View::creator('cooperation.frontend.tool.expert-scan.index', ToolComposer::class);
        View::creator('cooperation.frontend.tool.expert-scan.questionnaires.index', ToolComposer::class);
        View::creator('cooperation.frontend.layouts.tool', LayoutComposer::class);
        View::creator('cooperation.frontend.layouts.parts.sub-nav', SubNavComposer::class);

        View::creator('*', CooperationComposer::class);
        View::creator('cooperation.admin.*', AdminComposer::class);
        View::creator('cooperation.admin.layouts.navbar', AdminNavbarComposer::class);

        View::creator(
            [
                'cooperation.frontend.tool.simple-scan.index',
                'cooperation.frontend.tool.simple-scan.questionnaires.index',
            ],
            SimpleScanComposer::class
        );
        View::creator('cooperation.frontend.layouts.parts.navbar', NavbarComposer::class);

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
