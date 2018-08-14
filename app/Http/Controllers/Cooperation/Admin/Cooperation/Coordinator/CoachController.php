<?php

namespace App\Http\Controllers\Cooperation\Admin\Cooperation\Coordinator;

use App\Helpers\Str;
use App\Http\Requests\Admin\Cooperation\Coordinator\CoachRequest;
use App\Mail\UserCreatedEmail;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\Messages\MailMessage;
use Spatie\Permission\Models\Role;

class CoachController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $users = $cooperation->users()->where('id', '!=', \Auth::id())->get();
        $roles = Role::all();

        return view('cooperation.admin.cooperation.coordinator.coach.index', compact('roles', 'users'));
    }

    public function create()
    {
        $roles = Role::where('name', 'coach')->orWhere('name', 'resident')->get();

        return view('cooperation.admin.cooperation.coordinator.coach.create', compact('roles'));
    }

    public function store(Cooperation $cooperation, CoachRequest $request)
    {
        $firstName = $request->get('first_name', '');
        $lastName = $request->get('last_name', '');
        $email = $request->get('email', '');
        $password = $request->get('password', Str::randomPassword());

        $user = User::create(
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => bcrypt($password),
            ]
        );

        $roleIds = $request->get('roles', '');
        $roles = [];
        foreach ($roleIds as $roleId) {
            $role = Role::find($roleId);
            array_push($roles, $role->name);
        }

        // attach the new user to the cooperation
        $user->cooperations()->attach($cooperation->id);
        // assign the roles to the user
        $user->assignRole($roles);

        // send a mail to the user
        \Mail::to($email)->sendNow(new UserCreatedEmail($cooperation));

        return redirect()
            ->route('cooperation.admin.cooperation.coordinator.coach.index')
            ->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.coach.store.success'));
    }

    public function destroy(Cooperation $cooperation, $userId)
    {
        $user = $cooperation->users()->findOrFail($userId);

        // only remove the example building id from the building
        if ($user->buildings()->first() instanceof Building) {

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
        }
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

        $user->cooperations()->detach($cooperation->id);

        $user->delete();

        return redirect()->back()->with('success', __('woningdossier.cooperation.admin.cooperation.coordinator.coach.destroy.success'));
    }
}
