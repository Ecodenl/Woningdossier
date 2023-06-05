<?php

namespace App\Services;

use App\Events\UserDeleted;
use App\Events\UserResetHisBuilding;
use App\Helpers\KengetallenCodes;
use App\Helpers\Queue;
use App\Jobs\CheckBuildingAddress;
use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\CompletedQuestionnaire;
use App\Models\Considerable;
use App\Models\Cooperation;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Municipality;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\Econobis\EconobisService;
use App\Services\Kengetallen\KengetallenService;
use App\Services\Lvbag\BagService;
use App\Services\Models\BuildingService;
use App\Services\Models\BuildingStatusService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    public User $user;

    public function forUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function isRelatedWithEconobis(): bool
    {
        $contactId = $this->user->extra['contact_id'] ?? null;
        if ( ! empty($contactId)) {
            return true;
        }
        return false;
    }

    public function toolChanged(): void
    {
        $this->user->update(['tool_last_changed_at' => Carbon::now()]);
    }

    /**
     * Method to eager load most of the relationships the model has.
     * We either expect a user collection or a user model.
     */
    public static function eagerLoadUserData($userObject, InputSource $inputSource)
    {
        return $userObject->load(
            [
                'building' => function ($query) use ($inputSource) {
                    $query->with(
                        [
                            'buildingFeatures' => function ($query) use ($inputSource) {
                                $query->forInputSource($inputSource)
                                    ->with([
                                        'roofType',
                                        'energyLabel',
                                        'damagedPaintwork',
                                        'plasteredSurface',
                                        'contaminatedWallJoints',
                                        'wallJoints',
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
                },
                'energyHabit' => function ($query) use ($inputSource) {
                    $query->forInputSource($inputSource);
                }
            ]
        );
    }

    /**
     * Method to reset a user his file for a specific input source.
     */
    public function resetUser(InputSource $inputSource)
    {
        Log::debug(__METHOD__." ".$this->user->id." for input source ".$inputSource->short);
        // only remove the example building id from the building
        $user = $this->user;
        $building = $this->user->building;
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

        // Remove all mappings related to custom measure applications
        DB::table('mappings')->where('from_model_type', CustomMeasureApplication::class)
            ->whereIn('from_model_id',
                $building->customMeasureApplications()->forInputSource($inputSource)->pluck('id')->toArray())
            ->delete();
        // Remove custom measure applications the user has made
        $building->customMeasureApplications()->forInputSource($inputSource)->delete();

        // remove the action plan advices from the user
        $user->actionPlanAdvices()->withInvisible()->forInputSource($inputSource)->delete();
        // remove the energy habits from a user
        $user->energyHabit()->forInputSource($inputSource)->delete();

        $user->userCosts()->forInputSource($inputSource)->delete();
        // remove the considerables for the user
        Considerable::forUser($user)->forInputSource($inputSource)->delete();
        // remove all the tool question anders for the building
        $building->toolQuestionAnswers()->forInputSource($inputSource)->delete();
        // remove the progress of the completed questionnaires
        CompletedQuestionnaire::forMe($user)->forInputSource($inputSource)->delete();

        if ( ! in_array($inputSource->short, [InputSource::MASTER_SHORT,])) {
            // re-query the bag
            $addressData = app(BagService::class)->addressExpanded(
                $building->postal_code, $building->number, $building->extension
            )->prepareForBuilding();

            if ( ! empty(($addressData['bag_addressid'] ?? null))) {
                $building->update(['bag_addressid' => $addressData['bag_addressid']]);
            }

            $features = new BuildingFeature([
                'surface' => $addressData['surface'] ?? null,
                'build_year' => $addressData['build_year'] ?? null,
                'input_source_id' => $inputSource->id,
            ]);
            $features->building()->associate(
                $building
            )->save();

            app(BuildingService::class)->forBuilding($building)->setBuildingDefinedKengetallen();
        }
        UserResetHisBuilding::dispatch($building);
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
        if (! $account instanceof Account) {
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
        Log::debug('account id for registration: '.$account->id);

        // Create the user for an account
        $user = User::create(
            [
                'extra' => $data['extra'] ?? null,
                'account_id' => $account->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone_number' => $data['phone_number'] ?? '',
            ]
        );

        // filter relevant data from the request
        $data['address']['extension'] ??= null;
        $buildingData = $data['address'];

        // create the building for the user
        $building = $user->building()->save(new Building($buildingData));

        CheckBuildingAddress::dispatchSync($building);
        // check if the connection was successful, if not dispatch it on the regular queue so it retries.
        if ( ! $building->municipality()->first() instanceof Municipality) {
            CheckBuildingAddress::dispatch($building);
        }
        app(BuildingService::class)->forBuilding($building)->setBuildingDefinedKengetallen();
        $user->cooperation()->associate(
            $cooperation
        )->save();

        $user->assignRole($roles);

        app(BuildingStatusService::class)->forBuilding($building)->setStatus('active');

        return $user;
    }


    /**
     * Method to delete a user and its user info.
     *
     * @param  bool  $shouldForceDeleteBuilding
     *
     * @throws \Exception
     */
    public static function deleteUser(User $user, $shouldForceDeleteBuilding = false)
    {
        $accountId = $user->account_id;
        $building = $user->building;
        $cooperation = $user->cooperation;
        $accountRelated = app(EconobisService::class)->forBuilding($building)->resolveAccountRelated();

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
        if (0 == User::withoutGlobalScopes()->where('account_id', $accountId)->count()) {
            // bye !
            $account = Account::find($accountId);
            if ($account instanceof Account) {
                $account->delete();
            }
        }
        UserDeleted::dispatch($cooperation, $accountRelated['account_related']);
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
            'from_user_id' => [
                'private_messages',
            ],
        ];

        if ($user1->hasRole('coach')) {
            $tables['coach_id'] = [
                'building_coach_statuses',
                'building_notes',
            ];
        }

        foreach ($tables as $column => $tablesWithColumn) {
            foreach ($tablesWithColumn as $tableWithColumn) {
                Log::debug('UPDATE '.$tableWithColumn.' SET '.$column.' = '.$user1->id.' WHERE '.$column.' = '.$user2->id.';');
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
                Log::debug('Checking input sources for '.$tableWithColumn);
                $inputSources = DB::table($tableWithColumn)
                    ->where($column, '=', $user1->id)
                    ->select('input_source_id')
                    ->distinct()
                    ->pluck('input_source_id')
                    ->toArray();

                Log::debug('UPDATE '.$tableWithColumn.' SET '.$column.' = '.$user1->id.' WHERE '.$column.' = '.$user2->id.' AND WHERE input_source NOT IN ('.implode(',',
                        $inputSources).');');
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
