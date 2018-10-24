<?php

namespace App\Http\Controllers\Cooperation\MyAccount;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyAccountSettingsFormRequest;
use App\Models\Building;

class SettingsController extends Controller
{
    public function index()
    {
        $user = \Auth::user();

        return view('cooperation.my-account.settings.index', compact('user'));
    }

    // Update account
    public function store(MyAccountSettingsFormRequest $request)
    {
        $user = \Auth::user();

        $attributes = $request->all();
        $attributes['phone_number'] = is_null($attributes['phone_number']) ? '' : $attributes['phone_number'];

        if (! isset($attributes['password']) || empty($attributes['password'])) {
            unset($attributes['password']);
            unset($attributes['password_confirmation']);
            unset($attributes['current_password']);
        } else {
            $current_password = \Auth::User()->password;
            if (! \Hash::check($request->get('current_password', ''), $current_password)) {
                return redirect()->back()->withErrors(['current_password' => __('validation.current_password')]);
            }
            $attributes['password'] = \Hash::make($attributes['password']);
        }

        $user->update($attributes);

        return redirect()->route('cooperation.my-account.settings.index', ['cooperation' => \App::make('Cooperation')])->with('success', trans('woningdossier.cooperation.my-account.settings.form.store.success'));
    }

    /**
     * Reset the user his plan / file / dossier.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetFile()
    {
        $user = \Auth::user();

        // only remove the example building id from the building
        $building = $user->buildings()->first();
        $building->example_building_id = null;
        $building->save();

        // delete the services from a building
        $building->buildingServices()->delete();
        // delete the elements from a building
        $building->buildingElements()->delete();
        // remove the features from a building
        $building->buildingFeatures()->delete();
        // remove the roof types from a building
        $building->roofTypes()->delete();
        // remove the heater from a building
        $building->heater()->delete();
        // remove the solar panels from a building
        $building->pvPanels()->delete();
        // remove the insulated glazings from a building
        $building->currentInsulatedGlazing()->delete();
        // remove the paintwork from a building
        $building->currentPaintworkStatus()->delete();
        // remove the user usage from a building
        $building->userUsage()->delete();

        // remove the building usages from the user
        $user->buildingUsage()->delete();
        // remove the action plan advices from the user
        $user->actionPlanAdvices()->delete();
        // remove the user interests
        $user->interests()->delete();
        // remove the energy habits from a user
        $user->energyHabit()->delete();
        // remove the motivations from a user
        $user->motivations()->delete();
        // remove the progress from a user
        $user->progress()->delete();

        return redirect()->back()->with('success', __('woningdossier.cooperation.my-account.settings.form.reset-file.success'));
    }

    // Delete account
    public function destroy()
    {
        $user = \Auth::user();

        $building = $user->buildings()->first();

        $building->delete();

        // remove the building usages from the user
        $user->buildingUsage()->delete();
        // remove the action plan advices from the user
        $user->actionPlanAdvices()->delete();
        // remove the user interests
        $user->interests()->delete();
        // remove the energy habits from a user
        $user->energyHabit()->delete();
        // remove the motivations from a user
        $user->motivations()->delete();
        // remove the progress from a user
        $user->progress()->delete();
        // delete the cooperation from the user, belongsToMany so no deleting here.
        $user->cooperations()->detach();

        // finally remove the user itself :(
        $user->delete();

        return redirect(url(''));
    }
}
