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

    private $cooperation;
    private $currentUser;
    private $commentsByStep;
    //private $unreadMessageCount;
    private $currentStep;
    private $currentBuilding;
    private $buildingOwner;
    private $changedSettings;


    public function create(View $view)
    {
        //$view->with('cooperation', app()->make('Cooperation'));
        //$view->with('cooperationStyle', app()->make('CooperationStyle'));

        // since we dont really need to load the vars in this view since its just a alert
        // the alert is also loaded on pages where a user is not authenticated so some vars would fail.
        $excludedViews = ['cooperation.tool.components.alert'];

        if ( ! in_array($view->getName(), $excludedViews)) {


            $toolUrl = explode('/', request()->getRequestUri());
            $currentSubStep = isset($toolUrl[3]) ? Step::where('slug', $toolUrl[3])->first() : null;
            $currentBuilding = HoomdossierSession::getBuilding(true);
            $user = Hoomdossier::user();

            $view->with('user', $user);

            $view->with('currentStep', Step::where('slug', $toolUrl[2])->first());
            $view->with('currentSubStep', $currentSubStep);

            if (is_null($this->cooperation)) {
                $this->cooperation = HoomdossierSession::getCooperation(true);
            }

            if (is_null($this->currentUser)) {
                $this->currentUser = Hoomdossier::user();
            }

            if (is_null($this->commentsByStep)) {
                $this->commentsByStep = StepHelper::getAllCommentsByStep($this->currentUser);
            }

//            if (is_null($this->unreadMessageCount)) {
//                $this->unreadMessageCount = PrivateMessageView::getTotalUnreadMessagesForCurrentRole();
//            }

            if (is_null($this->currentStep)){
                $this->currentStep = Step::where('slug',
                    str_replace(['tool', '/'], '',
                        request()->getRequestUri()))->first();
            }


            $view->with('commentsByStep', $this->commentsByStep);
            //$view->with('inputSources', InputSource::orderBy('order', 'desc')->get());
            $view->with('inputSources',
                \App\Helpers\Cache\InputSource::getOrdered());
            //$view->with('myUnreadMessagesCount', $this->unreadMessageCount);

            $view->with('steps', $this->cooperation->getActiveOrderedSteps());
            $view->with('interests', \App\Helpers\Cache\Interest::getOrdered());
            $view->with('currentStep', $this->currentStep);

            if (is_null($this->currentBuilding)) {
                $this->currentBuilding = HoomdossierSession::getBuilding(true);
            }

            $changedSettings = collect([]);
            if ($this->currentBuilding instanceof Building) {
                if (is_null($this->buildingOwner)){
                    $this->buildingOwner = $this->currentBuilding->user;
                }
                $view->with('buildingOwner', $this->buildingOwner);
                if (is_null($this->changedSettings)) {
                    $this->changedSettings = ToolSetting::getChangedSettings($this->currentBuilding->id);
                }
                $changedSettings = $this->changedSettings;
            }
            $view->with('toolSettings', $changedSettings);

        }
    }
}
