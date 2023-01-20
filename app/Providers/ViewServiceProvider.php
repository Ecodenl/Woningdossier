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
    }
}
