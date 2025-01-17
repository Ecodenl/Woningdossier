<?php

namespace App\Console\Commands;

use App\Helpers\Arr;
use App\Helpers\Models\CooperationHelper;
use App\Helpers\Str;
use App\Models\Cooperation;
use App\Models\CooperationRedirect;
use App\Models\CooperationScan;
use App\Models\PrivateMessage;
use App\Models\Questionnaire;
use App\Models\User;
use App\Services\WoonplanService;
use Illuminate\Console\Command;

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

        if (count($cooperations) === 1) {
            /** @var Cooperation $cooperation */
            $cooperation = Arr::first($cooperations);
            if ($force || $this->confirm("Do you want to rename {$cooperation->name} to {$target}?")) {
                $cooperation->update([
                    'name' => $target,
                    'slug' => $targetSlug,
                ]);
            } else {
                $this->info('Not renaming cooperation.');
            }
            return self::SUCCESS;
        }

        if ($force || $this->confirm("Do you want to merge " . implode(', ', $cooperationSlugs) . " to {$target}?")) {
            /** @var Cooperation $newCooperation */
            $newCooperation = Cooperation::create([
                'name' => $target,
                'slug' => $targetSlug,
            ]);

            $cooperations = collect($cooperations);
            $cooperationIds = $cooperations->pluck('id')->all();

            // Attach the available scans to the new cooperation.
            $newCooperation->scans()->sync(
                CooperationScan::select('scan_id')
                    ->distinct()
                    ->whereIn('cooperation_id', $cooperationIds)
                    ->pluck('scan_id')
                    ->all()
            );

            // First we will do the simple cases: simply moving users between the cooperations. To do this, all we need
            // to do is query on the accounts that have a single user.
            User::forAllCooperations()
                ->whereIn("cooperation_id", $cooperationIds)
                ->whereIn("account_id", function ($query) use ($cooperationIds) {
                    $query
                        ->select("account_id")
                        ->from("users")
                        ->whereIn("cooperation_id", $cooperationIds)
                        ->groupBy("account_id")
                        ->havingRaw("COUNT(*) = 1");
                })->update(['cooperation_id' => $newCooperation->id]);

            // Now we need to update the users that have more than one user spread over the cooperations we're merging.
            User::forAllCooperations()
                ->select("account_id")
                ->whereIn("cooperation_id", $cooperationIds)
                ->groupBy("account_id")
                ->havingRaw("COUNT(*) > 1")
                ->eachById(function (User $user) {
                    $usersForAccount = User::forAllCooperations()->where('account_id', $user->account_id)
                        ->withWhereHas('building')
                        ->get();

                    $canAccessWoonplan = [];
                    foreach ($usersForAccount as $accountUser) {
                        //WoonplanService::init($accountUser->building)

                    }


// TODO: Merge roles


                }, 100, 'account_id');

            // Update private messages
            PrivateMessage::whereIn('from_cooperation_id', $cooperationIds)
                ->update(['from_cooperation_id' => $newCooperation->id]);
            PrivateMessage::whereIn('to_cooperation_id', $cooperationIds)
                ->update(['to_cooperation_id' => $newCooperation->id]);

            // Update questionnaires
            Questionnaire::forAllCooperations()->whereIn('cooperation_id', $cooperationIds)
                ->update(['cooperation_id' => $newCooperation->id]);

            // Now we're done, so we'll do some finishing work on the cooperations before deleting them.
            foreach ($cooperations as $cooperation) {
                // Make redirects.
                CooperationRedirect::create([
                    'from_slug' => $cooperation->slug,
                    'cooperation_id', $newCooperation->id,
                ]);

                // Update example buildings.
                $cooperation->exampleBuildings()->update(['cooperation_id' => $newCooperation->id]);

                // Delete cooperation.
                CooperationHelper::destroyCooperation($cooperation);
            }
        } else {
            $this->info('Not merging cooperations.');
        }

        return self::SUCCESS;
    }
}
