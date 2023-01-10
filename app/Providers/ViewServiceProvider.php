<?php

namespace App\Providers;

use App\Http\ViewComposers\Frontend\Layouts\Parts\SubNavComposer;
use App\Http\ViewComposers\Frontend\Tool\QuickScanComposer;
use App\Http\ViewComposers\Frontend\Tool\NavbarComposer;
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
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::creator('cooperation.frontend.layouts.parts.sub-nav', SubNavComposer::class);
        View::creator(
            [
                'cooperation.frontend.tool.quick-scan.index',
                'cooperation.frontend.tool.quick-scan.questionnaires.index',
            ],
            QuickScanComposer::class
        );
        View::creator('cooperation.frontend.layouts.parts.navbar', NavbarComposer::class);
    }
}
