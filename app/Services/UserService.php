<?php

namespace App\Services;

use App\Helpers\PicoHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\CompletedQuestionnaire;
use App\Models\Considerable;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Method to eager load most of the relationships the model has.
     * We either expect a user collection or a user model.
     */
    // public static function eagerLoadCollection(Collection $userCollection, InputSource $inputSource): Collection
    public static function eagerLoadUserData($userObject, InputSource $inputSource)
    {
        return $userObject->load(
            ['building' => function ($query) use ($inputSource) {
                $query->with(
                    [
                        'buildingFeatures' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource)
                                ->with([
                                    'roofType', 'energyLabel', 'damagedPaintwork', 'plasteredSurface',
                                    'contaminatedWallJoints', 'wallJoints',
                                ]);
                        },
                        'buildingVentilations' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'currentPaintworkStatus' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'heater' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'pvPanels' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'buildingServices' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'roofTypes' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'buildingElements' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'currentInsulatedGlazing' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                    ]
                );
            }, 'energyHabit' => function ($query) use ($inputSource) {
                $query->forInputSource($inputSource);
            }]
        );
    }

    /**
     * Method to reset a user his file for a specific input source.
     */
    public static function resetUser(User $user, InputSource $inputSource)
    {
        Log::debug(__METHOD__ . " " . $user->id . " for input source " . $inputSource->short);
        // only remove the example building id from the building
        $building = $user->building;
        $building->buildingFeatures()->forInputSource($inputSource)->update([
            'example_building_id' => null,
        ]);

        // delete the services from a building
        $building->buildingServices()->forInputSource($inputSource)->delete();
        // delete the elements from a building
        $building->buildingElements()->forInputSource($inputSource)->delete();
        // remove the features from a building
        $building->buildingFeatures()->forInputSource($inputSource)->delete();
        // remove the roof types from a building
        $building->roofTypes()->forInputSource($inputSource)->delete();
        // remove the heater from a building
        $building->heater()->forInputSource($inputSource)->delete();
        // remove the solar panels from a building
        $building->pvPanels()->forInputSource($inputSource)->delete();
        // remove the insulated glazings from a building
        $building->currentInsulatedGlazing()->forInputSource($inputSource)->delete();
        // remove the paintwork from a building
        $building->currentPaintworkStatus()->forInputSource($inputSource)->delete();
        // remove all progress made in the tool
        $building->completedSteps()->forInputSource($inputSource)->delete();
        $building->completedSubSteps()->forInputSource($inputSource)->delete();
        // remove the step comments
        $building->stepComments()->forInputSource($inputSource)->delete();
        // remove the answers on the custom questionnaires
        $building->questionAnswers()->forInputSource($inputSource)->delete();
        // remove Custom Measure Applications the user has made
        $building->customMeasureApplications()->forInputSource($inputSource)->delete();

        // remove the action plan advices from the user
        $user->actionPlanAdvices()->forInputSource($inputSource)->delete();
        // remove the energy habits from a user
        $user->energyHabit()->forInputSource($inputSource)->delete();
        // remove the considerables for the user
        Considerable::forUser($user)->forInputSource($inputSource)->delete();
        // remove all the tool question anders for the building
        $building->toolQuestionAnswers()->forInputSource($inputSource)->delete();
        // remove the progress of the completed questionnaires
        CompletedQuestionnaire::forMe($user)->forInputSource($inputSource)->delete();

        if (!in_array($inputSource->short, [InputSource::MASTER_SHORT,])) {
            // re-query pico
            $picoAddressData = PicoHelper::getAddressData(
                $building->postal_code,
                $building->number
            );

            if ( ! empty(($picoAddressData['id'] ?? null))) {
                $building->update(['bag_addressid' => $picoAddressData['id']]);
            }

            $features = new BuildingFeature([
                'surface'         => $picoAddressData['surface'] ?? null,
                'build_year'      => $picoAddressData['build_year'] ?? null,
                'input_source_id' => $inputSource->id,
            ]);
            $features->building()->associate(
                $building
            )->save();
        }
    }

    /**
     * Method to register a user.
     *
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

        $user = self::create($cooperation, $roles, $account, Arr::except($registerData, ['email', 'password']));

        // associate it with the user
        $user->account()->associate(
            $account
        )->save();

        return $user;
    }

    /**
     * Method to create a new user with all necessary actions to make the tool work.
     *
     * @param $account
     * @param $data
     *
     * @return User|\Illuminate\Database\Eloquent\Model
     */
    public static function create(Cooperation $cooperation, array $roles, $account, $data)
    {
        Log::debug('account id for registration: ' . $account->id);
        // Create the user for an account
        $user = User::create(
            [
                'extra' => $data['extra'] ?? null,
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
            'surface' => $picoAddressData['surface'] ?? null,
            'build_year' => $picoAddressData['build_year'] ?? null,
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
     * Method to delete a user and its user info.
     *
     * @param bool $shouldForceDeleteBuilding
     *
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
        // we keep the user interests table until we are 100% sure it can be removed
        // but because of gdpr we have to keep this until the table is removed
        $user->userInterests()->withoutGlobalScopes()->delete();
        // we cant use the relationship because we just want to delete everything
        Considerable::forUser($user)->allInputSources()->delete();
        // remove the energy habits from a user
        $user->energyHabit()->withoutGlobalScopes()->delete();
        // remove the notification settings
        $user->notificationSettings()->withoutGlobalScopes()->delete();
        // first detach the roles from the user
        $user->roles()->detach($user->roles);
        // remove the user his motivations
        $user->motivations()->delete();

        // remove the user itself.
        $user->delete();


        $building->toolQuestionAnswers()->withoutGlobalScopes()->delete();
        // remove the user itself.
        // if the account has no users anymore then we delete the account itself too.
        if (0 == User::withoutGlobalScopes()->where('account_id',
                $accountId)->count()) {
            // bye !
            Account::find($accountId)->delete();
        }
    }

    /**
     * Merges two users. Some data will be just updated because it's only user-related.
     * When building(data) related: Where possible, non conflicting input from different
     * input sources will be combined. If not possible, the data of $user1 will be
     * leading and the data of user2 will be deleted.
     *
     * @return User
     * @throws \Exception
     *
     */
    public static function merge(User $user1, User $user2)
    {
        // The simple cases: where we can just update the user_id or coach_id
        $tables = [
            'user_id' => [
                'building_permissions',
                //'buildings', will be deleted
                'logs',
                //'notification_settings', will be deleted
                'private_message_views',
                //'user_motivations', will be deleted
            ],
            'for_user_id' => [
                'logs',
            ],
            'from_user_id' => [
                'private_messages',
            ],
        ];

        if ($user1->hasRole('coach')) {
            $tables['coach_id'] = [
                'building_coach_statuses', 'building_notes',
            ];
        }

        foreach ($tables as $column => $tablesWithColumn) {
            foreach ($tablesWithColumn as $tableWithColumn) {
                Log::debug('UPDATE ' . $tableWithColumn . ' SET ' . $column . ' = ' . $user1->id . ' WHERE ' . $column . ' = ' . $user2->id . ';');
                DB::table($tableWithColumn)
                    ->where($column, '=', $user2->id)
                    ->update([$column => $user1->id]);
            }
        }

        // The more complex cases: where we *only* want to copy data for
        // input sources which were present for user2 and not user1.

        $tables = [
            'user_id' => [
                'completed_questionnaires',
                'user_action_plan_advice_comments',
                'user_action_plan_advices',
                'user_energy_habits',
                'user_interests',
            ],
        ];

        foreach ($tables as $column => $tablesWithColumn) {
            foreach ($tablesWithColumn as $tableWithColumn) {
                Log::debug('Checking input sources for ' . $tableWithColumn);
                $inputSources = DB::table($tableWithColumn)
                    ->where($column, '=', $user1->id)
                    ->select('input_source_id')
                    ->distinct()
                    ->pluck('input_source_id')
                    ->toArray();

                Log::debug('UPDATE ' . $tableWithColumn . ' SET ' . $column . ' = ' . $user1->id . ' WHERE ' . $column . ' = ' . $user2->id . ' AND WHERE input_source NOT IN (' . implode(',', $inputSources) . ');');
                DB::table($tableWithColumn)
                    ->where($column, '=', $user2->id)
                    ->whereNotIn('input_source_id', $inputSources)
                    ->update([$column => $user1->id]);

                // the rest will stay and will be deleted
            }
        }

        // Now delete $user2
        static::deleteUser($user2);

        // and return the resulting user
        return $user1;
    }
}
