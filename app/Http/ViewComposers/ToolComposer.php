<?php

namespace App\Http\ViewComposers;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\Step;
use Illuminate\View\View;

class ToolComposer
{
    private $cooperation;
    private $currentUser;
    private $commentsByStep;
    private $currentStep;
    private $currentSubStep;
    private $currentBuilding;
    private $buildingOwner;

    public function create(View $view)
    {
        //$view->with('cooperation', app()->make('Cooperation'));
        //$view->with('cooperationStyle', app()->make('CooperationStyle'));

        // since we dont really need to load the vars in this view since its just a alert
        // the alert is also loaded on pages where a user is not authenticated so some vars would fail.
        // which meens we hef to refaktor.
        $excludedViews = ['cooperation.tool.components.alert'];

        if (! in_array($view->getName(), $excludedViews)) {
            $user = Hoomdossier::user();

            $view->with('user', $user);

            if (is_null($this->cooperation)) {
                $this->cooperation = HoomdossierSession::getCooperation(true);
            }

            if (is_null($this->currentUser)) {
                $this->currentUser = Hoomdossier::user();
            }
            if (is_null($this->currentStep)) {
                $toolUrl = explode('/', request()->getRequestUri());

                if ('my-plan' !== $toolUrl[2]) {
                    $currentSubStep = isset($toolUrl[3]) ? Step::where('slug', $toolUrl[3])->first() : null;

                    $this->currentStep = Step::where('slug', $toolUrl[2])
                        ->with(['questionnaires' => function ($query) {
                            $query
                                ->orderBy('order')
                                ->with(['questions' => function ($query) {
                                    $query
                                        ->orderBy('order')
                                        ->with(['questionAnswers' => function ($query) {
                                            $query->where('building_id', \App\Helpers\HoomdossierSession::getBuilding());
                                        }])
                                        ->with('questionAnswersForMe');
                                }]);
                        }])->first();
                    $this->currentSubStep = $currentSubStep;
                }
            }

            $view->with('commentsByStep', $this->commentsByStep);
            //$view->with('inputSources', InputSource::orderBy('order', 'desc')->get());
            $view->with('inputSources', \App\Helpers\Cache\InputSource::getOrdered());
            //$view->with('myUnreadMessagesCount', $this->unreadMessageCount);

            // TODO: Should this stay?
            $view->with('interests', \App\Helpers\Cache\Interest::getOrdered());
            $view->with('currentStep', $this->currentStep);
            $view->with('currentSubStep', $this->currentSubStep);

            if (is_null($this->currentBuilding)) {
                $this->currentBuilding = HoomdossierSession::getBuilding(true);
            }

            if ($this->currentBuilding instanceof Building) {
                if (is_null($this->buildingOwner)) {
                    $this->buildingOwner = $this->currentBuilding->user;
                }

                if (is_null($this->commentsByStep)) {
                    $this->commentsByStep = StepHelper::getAllCommentsByStep($this->currentBuilding);
                }
                $view->with('building', $this->currentBuilding);
                $view->with('buildingOwner', $this->buildingOwner);
            }
        }
    }
}
