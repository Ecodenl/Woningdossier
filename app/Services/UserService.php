<?php

namespace App\Services;

use App\Helpers\HoomdossierSession;
use App\Helpers\PicoHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\Cooperation;
use App\Models\Role;
use App\Models\User;
use App\Scopes\GetValueScope;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Method to register a user.
     *
     * @param Cooperation $cooperation
     * @param array $registerData
     * @param array $roles
     * @return User
     */
    public static function register(Cooperation $cooperation, array $roles, array $registerData)
    {
        $email = $registerData['email'];
        // try to obtain the existing account
        $account = Account::where('email', $email)->first();

        // if its not found we will create a new one.
        if (!$account instanceof Account) {
            $account = AccountService::create($email, $registerData['password']);
        }

        $user = self::create($cooperation, $roles, $account, array_except($registerData, ['email', 'password']));

        // associate it with the user
        $user->account()->associate(
            $account
        )->save();

        return $user;
    }

    /**
     * Method to create a new user with all necessary actions to make the tool work
     *
     * @param Cooperation $cooperation
     * @param $account
     * @param $data
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    public static function create(Cooperation $cooperation, array $roles, $account, $data)
    {

        Log::debug('account id for registration: ' . $account->id);
        // Create the user for an account
        $user = User::create(
            [
                'account_id' => $account->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone_number' => is_null($data['phone_number']) ? '' : $data['phone_number'],
            ]
        );

        // now get the picoaddress data.
        $picoAddressData = PicoHelper::getAddressData(
            $data['postal_code'], $data['number']
        );

        $data['bag_addressid'] = $picoAddressData['id'] ?? $data['addressid'] ?? '';
        $data['extension'] = $data['house_number_extension'] ?? null;

        $features = new BuildingFeature([
            'surface' => empty($picoAddressData['surface']) ? null : $picoAddressData['surface'],
            'build_year' => empty($picoAddressData['build_year']) ? null : $picoAddressData['build_year'],
        ]);

        // create the building for the user
        $building = Building::create($data);

        // associate multiple models with each other
        $building->user()->associate(
            $user
        )->save();

        $features->building()->associate(
            $building
        )->save();

        $user->cooperation()->associate(
            $cooperation
        )->save();

        $user->assignRole($roles);

        // turn on when merged
        $building->setStatus('active');

        return $user;
    }

    /**
     * Method to delete a user and its user info
     *
     * @param User $user
     * @param bool $shouldForceDeleteBuilding
     * @throws \Exception
     */
    public static function deleteUser(User $user, $shouldForceDeleteBuilding = false)
    {
        $accountId = $user->account_id;
        $building = $user->building;

        if ($building instanceof Building) {
            if ($shouldForceDeleteBuilding) {
                BuildingService::deleteBuilding($building);
            } else {
                $building->delete();
                // remove the progress from a user
                $building->completedSteps()->delete();
            }
        }

        // remove the action plan advices from the user
        $user->actionPlanAdvices()->withoutGlobalScopes()->delete();
        // remove the user interests
        $user->userInterests()->withoutGlobalScopes()->delete();
        // remove the energy habits from a user
        $user->energyHabit()->withoutGlobalScopes()->delete();
        // remove the motivations from a user
        $user->motivations()->withoutGlobalScopes()->delete();
        // remove the notification settings
        $user->notificationSettings()->withoutGlobalScopes()->delete();
        // first detach the roles from the user
        $user->roles()->detach($user->roles);

        // remove the user itself.
        $user->delete();

        // remove the user itself.
        // if the account has no users anymore then we delete the account itself too.
        if (0 == User::withoutGlobalScopes()->where('account_id', $accountId)->count()) {
            // bye !
            Account::find($accountId)->delete();
        }
    }
}
