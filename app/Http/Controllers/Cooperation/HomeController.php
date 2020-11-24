<?php

namespace App\Http\Controllers\Cooperation;

use App\Helpers\Cooperation\Tool\HeaterHelper;
use App\Helpers\Cooperation\Tool\WallInsulationHelper;
use App\Helpers\Hoomdossier;
use App\Helpers\StepHelper;
use App\Helpers\ToolHelper;
use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\CompletedStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;
use Spatie\TranslationLoader\TranslationLoaders\Db;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Cooperation $cooperation)
    {
        return view('cooperation.home.index');
    }
}
