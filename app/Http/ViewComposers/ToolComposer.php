<?php

namespace App\Http\ViewComposers;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\PrivateMessage;
use App\Models\Step;
use Illuminate\View\View;

class ToolComposer
{
    public function create(View $view)
    {
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

        $view->with('cooperation', app()->make('Cooperation'));
        $view->with('cooperationStyle', app()->make('CooperationStyle'));

        $view->with('inputSources', InputSource::orderBy('order', 'desc')->get());
        $view->with('myUnreadMessages', PrivateMessage::unreadMessages()->get());

        $view->with('steps', $cooperation->getActiveOrderedSteps());
        $view->with('interests', Interest::orderBy('order')->get());

        $view->with('currentStep', Step::where('slug', str_replace(['tool', '/'], '', request()->getRequestUri()))->first());

        $currentBuilding = HoomdossierSession::getBuilding();
        if (!is_null($currentBuilding)) {
        	$building = Building::find($currentBuilding);
        	if ($building instanceof Building) {
		        $view->with( 'buildingOwner', $building->user );
	        }
        }
    }
}
