<?php

namespace App\Http\ViewComposers;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\PrivateMessageView;
use App\Models\Step;
use App\Models\ToolSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ToolComposer
{
    public function create(View $view)
    {
        $cooperation = HoomdossierSession::getCooperation(true);

        $view->with('cooperation', app()->make('Cooperation'));
        $view->with('cooperationStyle', app()->make('CooperationStyle'));

        // since we dont really need to load the vars in this view since its just a alert
        // the alert is also loaded on pages where a user is not authenticated so some vars would fail.
        $excludedViews = ['cooperation.tool.components.alert'];

        if (! in_array($view->getName(), $excludedViews)) {
            $toolUrl = explode('/', request()->getRequestUri());
            $currentSubStep = isset($toolUrl[3]) ? Step::where('slug', $toolUrl[3])->first() : null;
            $currentBuilding = HoomdossierSession::getBuilding(true);
            $user = Hoomdossier::user();

            $view->with('user', $user);

            $view->with('currentStep', Step::where('slug', $toolUrl[2])->first());
            $view->with('currentSubStep', $currentSubStep);

            Log::debug(__METHOD__);

            $view->with('commentsByStep', StepHelper::getAllCommentsByStep($user));
            $view->with('inputSources', InputSource::orderBy('order', 'desc')->get());
            $view->with('myUnreadMessagesCount', PrivateMessageView::getTotalUnreadMessagesForCurrentRole());


            $view->with('steps', $cooperation->steps()->activeOrderedSteps()->withoutSubSteps()->get());
            $view->with('interests', Interest::orderBy('order')->get());


            if ($currentBuilding instanceof Building) {
                $view->with('building', $currentBuilding);
                $view->with('buildingOwner', $currentBuilding->user);
            }

            $buildingId = HoomdossierSession::getBuilding();
            $changedSettings = collect([]);
            if (! is_null($buildingId)) {
                $changedSettings = ToolSetting::getChangedSettings($buildingId);
            }
            $view->with('toolSettings', $changedSettings);
        }
    }
}
