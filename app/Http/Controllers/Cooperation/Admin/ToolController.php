<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Events\FillingToolForUserEvent;
use App\Events\ObservingToolForUserEvent;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ToolController extends Controller
{

    /**
     * Set the sessions and after that redirect them to the tool.
     *
     * @param Cooperation $cooperation
     * @param $buildingId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fillForUser(Cooperation $cooperation, $buildingId)
    {

        // The building the coach wants to edit
        $building = Building::find($buildingId);
        // get the owner of the building
        $user = $cooperation->users()->findOrFail($building->user_id);

        // we cant query on the Spatie\Role model so we first get the result on the "original model"
        //        $role = Role::findByName($user->roles->first()->name);
        $role = Role::findByName(HoomdossierSession::currentRole());
        // set the input source value to the coach itself
        $inputSourceValue = InputSource::find(HoomdossierSession::getInputSource());

        $inputSource = InputSource::find(HoomdossierSession::getInputSource());

        // if the role has no inputsource redirect back with "probeer t later ff nog een keer"
        // or if the role is not a resident, we gonna throw them back.
        if (! $inputSourceValue instanceof InputSource || ! $inputSource instanceof InputSource && $inputSource->isResident()) {
            return redirect()->back()->with('warning', __('woningdossier.cooperation.admin.coach.buildings.fill-for-user.warning'));
        }

        // We set the building to the building the coach wants to "edit"
        // The inputSource is just the coach one
        // But the input source value is from the building owner so the coach can see the input, the coach can switch this in the tool itself.
        HoomdossierSession::setHoomdossierSessions($building, $inputSource, $inputSourceValue, $role);

        \Event::dispatch(new FillingToolForUserEvent($building, $user, \Auth::user()));
        return redirect()->route('cooperation.tool.index');
    }

    /**
     * Sessions that need to be set so we can let a user observe a building / tool
     *
     * @param Cooperation $cooperation
     * @param $buildingId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function observeToolForUser(Cooperation $cooperation, $buildingId)
    {
        // The building the coach wants to edit
        $building = Building::find($buildingId);
        // get the owner of the building
        $user = $cooperation->users()->findOrFail($building->user_id);
        // we cant query on the Spatie\Role model so we first get the result on the "original model"

        $role = Role::findByName(HoomdossierSession::currentRole());
        // set the input source value to the coach itself
        $inputSourceValue = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        $inputSource = InputSource::find(HoomdossierSession::getInputSource());

        // if the role has no inputsource redirect back with "probeer t later ff nog een keer"
        // or if the role is not a resident, we gonna throw them back.
        if (! $inputSourceValue instanceof InputSource || ! $inputSource instanceof InputSource && $inputSource->isResident()) {
            return redirect()->back()->with('warning', __('woningdossier.cooperation.admin.coach.buildings.fill-for-user.warning'));
        }

        // We set the building to the building the coach wants to "edit"
        // The inputSource is just the coach one
        // But the input source value is from the building owner so the coach can see the input, the coach can switch this in the tool itself.
        HoomdossierSession::setHoomdossierSessions($building, $inputSource, $inputSourceValue, $role);

        // so the user isnt able to save anything
        HoomdossierSession::setIsObserving(true);

        \Event::dispatch(new ObservingToolForUserEvent($building, $user, \Auth::user()));

        return redirect()->route('cooperation.tool.index');
    }
}
