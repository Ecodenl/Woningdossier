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

        (new WallInsulationHelper(Hoomdossier::user(), InputSource::findByShort('resident')))
            ->createValues()
            ->createAdvices();

        dd(
            \DB::table('buildings')
                ->select('buildings.*')
                ->leftJoin('completed_steps', function (JoinClause $query) {
                    $query->on('completed_steps.building_id', '=', 'buildings.id');
                })->whereExists(function (\Illuminate\Database\Query\Builder $query) {
                    $query
                        ->select('*')
                        ->from('user_interests')
                        ->where('buildings.user_id', '=', 'user_interests.user_id')
                        ->whereRaw('user_interests.interested_in_id = completed_steps.step_id')
                        ->whereRaw('user_interests.input_source_id = completed_steps.input_source_id');
                })->count()

//            Building::whereHas('completedSteps', function (Builder $query) {
//                $query
//                    ->whereIn('step_id', [1, 12, 13, 14, 15])

//            })->count()

        );
//        $users = User::with('building')->forAllCooperations()->findMany([12, 1,23, 45 ,434]);
//
//        /** @var User $user */
//        foreach ($users as $user) {
//
//            // get the completed steps for a user.
//            $completedSteps = $user->building
//                ->completedSteps()
//                ->whereHas('step', function (Builder $query) {
//                    $query->whereNotIn('steps.short', ['general-data', 'heat-pump'])
//                        ->whereNull('parent_id');
//                })->with(['inputSource', 'step'])
//                ->forMe()
//                ->get();
//
//            /** @var CompletedStep $completedStep */
//            foreach ($completedSteps as $completedStep) {
//                // check if the user is interested in the step
//                if (StepHelper::hasInterestInStep($user, Step::class, $completedStep->step->id, $completedStep->inputSource)) {
//                    // user is interested, so recreate the advices for each step
//                    $stepClass = 'App\Helpers\Cooperation\Tool\\'.Str::singular(Str::studly($completedStep->step->short)).'Helper';
//                    $stepHelperClass = new $stepClass($user, $completedStep->inputSource);
//                    $stepHelperClass->createValues()->createAdvices();
//                }
//            }
//        }
        return view('cooperation.home.index');
    }
}
