<?php

namespace App\Console\Commands;

use App\Helpers\Arr;
use App\Helpers\Models\CooperationHelper;
use App\Helpers\Str;
use App\Models\Cooperation;
use App\Models\CooperationRedirect;
use App\Models\CooperationScan;
use App\Models\ExampleBuilding;
use App\Models\PrivateMessage;
use App\Models\Questionnaire;
use App\Models\Scan;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\WoonplanService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MergeCooperations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hoomdossier:merge-cooperations 
                            {target : The target cooperation (name!).}
                            {cooperations* : The cooperations (slug!) to merge together.}
                            {--force : To force the operation without confirmation.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = (bool) $this->option('force');
        // The target name (a.k.a. new cooperation) for the merging > required argument, so if
        // not provided, will throw an error.
        $target = $this->argument('target');
        // One or more cooperation slugs to merge > also required argument.
        $cooperationSlugs = $this->argument('cooperations');

        $targetSlug = Str::slug($target);
        if (Cooperation::where('slug', $targetSlug)->exists()) {
            $this->error("Cooperation '{$target}' already exists!");
            return self::FAILURE;
        }

        $cooperations = [];
        foreach ($cooperationSlugs as $cooperationSlug) {
            /** @var null|Cooperation $cooperation */
            $cooperation = Cooperation::where('slug', $cooperationSlug)->first();
            if ($cooperation instanceof Cooperation) {
                $cooperations[] = $cooperation;
            } else {
                $this->error("Cooperation '{$cooperationSlug}' does not exist.");
                return self::FAILURE;
            }
        }

        // If a cooperation wasn't found, it would have thrown an error so at this point we 100% know
        // a cooperation is set.

        // If there's only one cooperation, we can simply rename it and create a redirect!
        if (count($cooperations) === 1) {
            /** @var Cooperation $cooperation */
            $cooperation = Arr::first($cooperations);
            if ($force || $this->confirm("Do you want to rename '{$cooperation->name}' to '{$target}'?")) {
                // Make redirect.
                CooperationRedirect::create([
                    'from_slug' => $cooperation->slug,
                    'cooperation_id' => $cooperation->id,
                ]);

                // Update the cooperation.
                $cooperation->update([
                    'name' => $target,
                    'slug' => $targetSlug,
                ]);
            } else {
                $this->info('Not renaming cooperation.');
            }

            return self::SUCCESS;
        }

        // More than one cooperation to merge, so we will create a new source and merge the users, private messages,
        // questionnaires and example buildings to this new cooperation.
        if ($force || $this->confirm("Do you want to merge '" . implode(', ', $cooperationSlugs) . "' to '{$target}'?")) {
            /** @var Cooperation $newCooperation */
            $newCooperation = Cooperation::create([
                'name' => $target,
                'slug' => $targetSlug,
            ]);

            $cooperations = collect($cooperations)->keyBy('id');
            $cooperationIds = $cooperations->keys()->all();

            $availableScans = CooperationScan::select('scan_id')
                ->distinct()
                ->whereIn('cooperation_id', $cooperationIds)
                ->pluck('scan_id')
                ->all();

            // Attach all the available scans to the new cooperation.
            $newCooperation->scans()->sync($availableScans);

            // First we will do the simple cases: simply moving users between the cooperations. To do this, all we need
            // to do is query on the accounts that have a single user (for the given cooperations).
            // Sadly, MySQL does NOT support update statements with a single sub query, so we will
            // fetch the account IDs first.
            $accountIds = User::forAllCooperations()
                ->select("account_id")
                ->from("users")
                ->whereIn("cooperation_id", $cooperationIds)
                ->groupBy("account_id")
                ->havingRaw("COUNT(*) = 1")
                ->pluck('account_id')
                ->all();

            User::forAllCooperations()
                ->whereIn("cooperation_id", $cooperationIds)
                ->whereIn("account_id", $accountIds)
                ->update(['cooperation_id' => $newCooperation->id]);

            // Now we need to update the users that have more than one user spread over the cooperations we're merging.
            User::forAllCooperations()
                ->select("account_id")
                ->whereIn("cooperation_id", $cooperationIds)
                ->groupBy("account_id")
                ->havingRaw("COUNT(*) > 1")
                ->eachById(function (User $user) use ($cooperationIds, $newCooperation, $cooperations) {
                    $usersForAccount = User::forAllCooperations()->where('account_id', $user->account_id)
                        ->whereIn('cooperation_id', $cooperationIds)
                        ->withWhereHas('building')
                        ->get()
                        ->keyBy('id');

                    $canAccessWoonplan = [];
                    foreach ($usersForAccount as $accountUser) {
                        // To know which user we want to keep from the multiple cooperations, we want to know if
                        // the action plan is accessible. Since cooperations can utilize different scans, we will
                        // simple it down to see if the action plan is accessible in _any_ of the available scans
                        // of that cooperation. That way, we get a black and white comparison, even though under
                        // water it isn't. This is deliberate, since in the case cooperation A has the lite scan,
                        // B has quick and lite, and C only has the quick scan, there is no way to actively
                        // compare.
                        /** @var Scan $scan */
                        foreach ($cooperations[$accountUser->cooperation_id]->scans as $scan) {
                            // Expert scan does not count for the woonplan, but it's more efficient to just keep
                            // it eager loaded.
                            if ($scan->isExpertScan()) {
                                continue;
                            }

                            if (WoonplanService::init($accountUser->building)->scan($scan)->canAccessWoonplan()) {
                                $canAccessWoonplan[] = $accountUser;
                                break;
                            }
                        }
                    }

                    $total = count($canAccessWoonplan);
                    if ($total === 0) {
                        // Okay, so no user can access the action plan. We will base the user ID based on the
                        // last activity on the users table.
                        $userToKeep = User::forAllCooperations()->whereIn('id', $usersForAccount->keys()->all())
                            ->whereNotNull('tool_last_changed_at')
                            ->orderByDesc('tool_last_changed_at')
                            ->first();

                        // In the case that both users have not done anything in the Hoomdossier yet,
                        // (which is highly unlikely, but not impossible) we will just get the one with the latest
                        // updated_at.
                        if (! $userToKeep instanceof User) {
                            $userToKeep = User::forAllCooperations()->whereIn('id', $usersForAccount->keys()->all())
                                ->orderByDesc('updated_at')
                                ->first();
                        }
                    } elseif ($total === 1) {
                        // Easy case. Only one can access the action plan, so we keep that one.
                        $userToKeep = Arr::first($canAccessWoonplan);
                    } else {
                        // Both users can access the action plan, so we decide based on which user has the most recent
                        // activity on the action plan.
                        $mostRecentAdvice = UserActionPlanAdvice::allInputSources()
                            ->whereIn('user_id', $usersForAccount->keys()->all())
                            ->orderByDesc('updated_at')
                            ->first();

                        $userToKeep = $usersForAccount[$mostRecentAdvice->user_id];
                    }

                    $userToKeep->update(['cooperation_id' => $newCooperation->id]);

                    // Since we're merging, we're allowing users to keep all their collected roles.
                    $roles = DB::table('model_has_roles')->select('role_id')
                        ->distinct()
                        ->where('model_type', User::class)
                        ->whereIn('model_id', $usersForAccount->keys()->all())
                        ->pluck('role_id')
                        ->all();
                    $userToKeep->roles()->sync($roles);
                }, 100, 'account_id');

            // Update private messages.
            PrivateMessage::whereIn('from_cooperation_id', $cooperationIds)
                ->update(['from_cooperation_id' => $newCooperation->id]);
            PrivateMessage::whereIn('to_cooperation_id', $cooperationIds)
                ->update(['to_cooperation_id' => $newCooperation->id]);

            // Update questionnaires.
            Questionnaire::forAllCooperations()->whereIn('cooperation_id', $cooperationIds)
                ->update(['cooperation_id' => $newCooperation->id]);

            // Update example buildings.
            ExampleBuilding::whereIn('cooperation_id', $cooperationIds)
                ->update(['cooperation_id' => $newCooperation->id]);

            // Now we're done, so we'll do some finishing work on the cooperations and then delete them.
            foreach ($cooperations as $cooperation) {
                // Make redirects.
                CooperationRedirect::create([
                    'from_slug' => $cooperation->slug,
                    'cooperation_id' => $newCooperation->id,
                ]);

                // Delete cooperation.
                CooperationHelper::destroyCooperation($cooperation);
            }
        } else {
            $this->info('Not merging cooperations.');
        }

        return self::SUCCESS;
    }
}
