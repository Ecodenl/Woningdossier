<?php

namespace Tests\Unit\app\Console\Commands;

use App\Helpers\Arr;
use App\Helpers\RoleHelper;
use App\Models\CooperationScan;
use App\Models\ExampleBuilding;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\PrivateMessage;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Role;
use App\Models\Scan;
use App\Models\Step;
use App\Services\CooperationScanService;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Console\Commands\MergeCooperations;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MergeCooperationsTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;

    public static function missingArgumentsAbortsCommandTestProvider(): array
    {
        return [
            [[]],
            [['target' => 'Cooperation']],
            [['cooperations' => []]],
            [['cooperations' => ['cooperation-to-merge']]],
        ];
    }

    #[DataProvider('missingArgumentsAbortsCommandTestProvider')]
    public function testMissingArgumentsAbortsCommand(array $arguments): void
    {
        // Note: we use a provider since this seems to only catch the first instance of an exception being thrown.
        // All code afterwards is not executed.
        $this->expectException(RuntimeException::class);

        // Missing arguments should throw a Symfony runtime exception.
        $this->artisan(MergeCooperations::class, $arguments);
    }

    public function testErrorsThrownOnIncorrectArguments(): void
    {
        $arguments = [
            'target' => 'Cooperation',
            'cooperations' => ['cooperation-to-merge'],
        ];

        // Target does not exist, so it will continue to the checks for the cooperations.
        $this->artisan(MergeCooperations::class, $arguments)
            ->expectsOutput("Cooperation 'cooperation-to-merge' does not exist.")
            ->assertExitCode(Command::FAILURE);

        // First cooperation still doesn't exist, so it will throw the same error.
        $arguments['cooperations'][] = 'another-cooperation';
        $this->artisan(MergeCooperations::class, $arguments)
            ->expectsOutput("Cooperation 'cooperation-to-merge' does not exist.")
            ->assertExitCode(Command::FAILURE);

        Cooperation::factory()->create(['name' => 'Cooperation to merge', 'slug' => 'cooperation-to-merge']);

        // Now it exists, so it will error about the second cooperation.
        $this->artisan(MergeCooperations::class, $arguments)
            ->expectsOutput("Cooperation 'another-cooperation' does not exist.")
            ->assertExitCode(Command::FAILURE);

        Cooperation::factory()->create(['name' => 'Cooperation', 'slug' => 'cooperation']);

        // Now the target exists, so it will error about the target already existing.
        $this->artisan(MergeCooperations::class, $arguments)
            ->expectsOutput("Cooperation 'Cooperation' already exists!")
            ->assertExitCode(Command::FAILURE);
    }

    public function testSingleCooperationRenamesOriginal(): void
    {
        $defaultCooperationCount = DB::table('cooperations')->count();
        $defaultCooperationRedirectCount = DB::table('cooperation_redirects')->count();
        $defaultUserCount = DB::table('users')->count();

        $arguments = [
            'target' => 'Cooperation',
            'cooperations' => ['cooperation-to-merge'],
        ];

        $cooperation = Cooperation::factory()->create([
            'name' => 'Cooperation to merge', 'slug' => 'cooperation-to-merge'
        ]);

        User::factory()->count(15)->create([
            'cooperation_id' => $cooperation->id,
        ]);

        // Assert factory data in table.
        $this->assertDatabaseCount('cooperation_redirects', $defaultCooperationRedirectCount);
        $this->assertDatabaseCount('cooperations', $defaultCooperationCount + 1);
        $this->assertDatabaseCount('users', $defaultUserCount + 15);
        $this->assertSame(15, DB::table('users')->where('cooperation_id', $cooperation->id)->count());

        // Call a deny run.
        $this->artisan(MergeCooperations::class, $arguments)
            ->expectsConfirmation("Do you want to rename 'Cooperation to merge' to 'Cooperation'?", 'no')
            ->expectsOutput('Not renaming cooperation.')
            ->assertExitCode(Command::SUCCESS);

        // Assert not renamed.
        $this->assertDatabaseCount('cooperation_redirects', $defaultCooperationRedirectCount);
        $this->assertDatabaseHas('cooperations', [
            'name' => 'Cooperation to merge', 'slug' => 'cooperation-to-merge'
        ]);

        // Actually do the renaming.
        $this->artisan(MergeCooperations::class, $arguments)
            ->expectsConfirmation("Do you want to rename 'Cooperation to merge' to 'Cooperation'?", 'yes')
            ->doesntExpectOutput('Not renaming cooperation.')
            ->assertExitCode(Command::SUCCESS);

        // Assert renamed.
        $this->assertDatabaseCount('cooperation_redirects', $defaultCooperationRedirectCount + 1);
        $this->assertDatabaseHas('cooperation_redirects', [
            'from_slug' => 'cooperation-to-merge', 'cooperation_id' => $cooperation->id,
        ]);
        $this->assertDatabaseMissing('cooperations', [
            'name' => 'Cooperation to merge', 'slug' => 'cooperation-to-merge'
        ]);
        $this->assertDatabaseHas('cooperations', [
            'name' => 'Cooperation', 'slug' => 'cooperation'
        ]);
    }

    public function testMultipleCooperationsGetMerged(): void
    {
        $defaultCooperationCount = DB::table('cooperations')->count();
        $defaultCooperationRedirectCount = DB::table('cooperation_redirects')->count();
        $defaultUserCount = DB::table('users')->count();

        $roles = [];
        foreach ([RoleHelper::ROLE_RESIDENT, RoleHelper::ROLE_COACH, RoleHelper::ROLE_COOPERATION_ADMIN] as $roleName) {
            $roles[] = Role::findByName($roleName);
        }

        // First, we must make some "valid" cooperations > can't properly test if they don't have some data.
        foreach (['a', 'b', 'c', 'd', 'e'] as $label) {
            $cooperationVar = 'cooperation' . strtoupper($label);

            /**
             * @var Cooperation $cooperationA
             * @var Cooperation $cooperationB
             * @var Cooperation $cooperationC
             * @var Cooperation $cooperationD
             * @var Cooperation $cooperationE
             */
            $$cooperationVar = Cooperation::factory()
                ->has(
                    User::factory()->count(50)
                        ->withAccount()
                        ->withBuilding()
                        ->afterCreating(function (User $user) use ($roles) {
                            $user->roles()->sync(Arr::random($roles, mt_rand(1, 3)));
                            PrivateMessage::createQuietly([
                                'is_public' => true,
                                'to_cooperation_id' => $user->cooperation_id,
                                'from_user' => $user->getFullName(),
                                'from_user_id' => $user->id,
                                'message' => 'Welkom bij de cooperatie ' . $user->cooperation->name,
                                'building_id' => $user->building->id,
                            ]);

                            // Create a from cooperation message.
                            if (mt_rand(0, 10) === 7) {
                                PrivateMessage::createQuietly([
                                    'is_public' => true,
                                    'to_cooperation_id' => $user->cooperation_id,
                                    'from_cooperation_id' => $user->cooperation_id,
                                    'from_user' => $user->cooperation->name,
                                    'message' => 'Uw aanmelding is goed ontvangen!',
                                    'building_id' => $user->building->id,
                                ]);
                            }
                        })
                )
                ->create();

            ExampleBuilding::factory()->count(mt_rand(1, 3))->withContents()->create([
                'cooperation_id' => $$cooperationVar->id,
            ]);

            Questionnaire::factory()
                ->has(
                    Question::factory()->count(5)
                )
                ->create(['cooperation_id' => $$cooperationVar->id]);

            // Sync some scans.
            CooperationScanService::init($$cooperationVar)->syncScan(Arr::random([Scan::QUICK, Scan::LITE, 'both-scans']));
        }

        // Ensure correctly seeded.
        $this->assertDatabaseCount('cooperation_redirects', $defaultCooperationRedirectCount);
        $this->assertDatabaseCount('cooperations', $defaultCooperationCount + 5);
        $this->assertDatabaseCount('users', $defaultUserCount + 250);

        $cooperations = [$cooperationB, $cooperationC, $cooperationD];
        $cooperationSlugs = [$cooperationB->slug, $cooperationC->slug, $cooperationD->slug];
        $cooperationIds = [$cooperationB->id, $cooperationC->id, $cooperationD->id];

        foreach ($cooperations as $cooperation) {
            $this->assertDatabaseHas('questionnaires', [
                'cooperation_id' => $cooperation->id,
            ]);
            $this->assertDatabaseHas('example_buildings', [
                'cooperation_id' => $cooperation->id,
            ]);
            $this->assertDatabaseHas('private_messages', [
                'to_cooperation_id' => $cooperation->id,
            ]);
        }

        // Before we move on, get some data so we can compare with the after situation.
        $totalQuestionnaires = DB::table('questionnaires')->whereIn('cooperation_id', $cooperationIds)->count();
        $totalEBs = DB::table('example_buildings')->whereIn('cooperation_id', $cooperationIds)->count();
        $totalPMs = DB::table('private_messages')->where(
            fn ($q) => $q->whereIn('to_cooperation_id', $cooperationIds)
                ->orWhereIn('from_cooperation_id', $cooperationIds)
        )->count();
        $scans = CooperationScan::whereIn('cooperation_id', $cooperationIds)
            ->select('scan_id')
            ->distinct()
            ->orderBy('scan_id')
            ->pluck('scan_id')
            ->all();

        $loop = 0;
        $userData = [];
        $roleData = [];
        // Finally, we will move a couple users to another cooperation.
        $cooperationB->users()->inRandomOrder()->eachById(function (User $user) use ($cooperationC, &$loop, &$userData, &$roleData, $roles, &$totalPMs) {
            $dupe = $user->replicate();
            $dupe->cooperation_id = $cooperationC->id;
            $dupe->save();

            $dupeBuilding = $user->building->replicate();
            $dupeBuilding->user_id = $dupe->id;
            $dupeBuilding->save();

            // To know some data for later (after the merge) we simply store account ID with the user ID that will be
            // kept. By default, the dupe will be created later and thus fall into the category of having a later
            // updated_at.
            $userIdToKeep = $dupe->id;

            // Store a few custom scenarios
            if ($loop > 2 && $loop < 6) {
                // In this scenario, neither users can access the Woonplan but this user has updated the tool later
                // and therefore is more relevant.
                $user->update(['tool_last_changed_at' => Carbon::now()]);
                $userIdToKeep = $user->id;

                // We can safely fetch from the role array by index due to the foreach order.
                // Only resident role.
                $user->roles()->sync($roles[0]);
                $dupe->roles()->sync($roles[0]);
                $roleData[$userIdToKeep] = [$roles[0]->id];
            } elseif ($loop > 6 && $loop < 13) {
                // Only one of the users can access the action plan (by having complete steps).
                foreach (Step::all() as $step) {
                    $user->building->completedSteps()->createQuietly([
                        'input_source_id' => InputSource::master()->id,
                        'step_id' => $step->id,
                    ]);
                }
                $userIdToKeep = $user->id;

                // Both resident and coach role
                $user->roles()->sync($roles[0]);
                $dupe->roles()->sync($roles[1]);
                $roleData[$userIdToKeep] = [$roles[0]->id, $roles[1]->id];
            } elseif ($loop > 15 && $loop < 20) {
                // Finally, we will give both access to the action plan, and provide a user action plan advice
                // to be the differentiating factor.
                foreach (Step::all() as $step) {
                    $dupe->building->completedSteps()->createQuietly([
                        'input_source_id' => InputSource::master()->id,
                        'step_id' => $step->id,
                    ]);
                    $user->building->completedSteps()->createQuietly([
                        'input_source_id' => InputSource::master()->id,
                        'step_id' => $step->id,
                    ]);
                }

                $bottomInsulation = MeasureApplication::findByShort('bottom-insulation');
                $dupe->userActionPlanAdvices()->create([
                    'input_source_id' => InputSource::master()->id,
                    'user_action_plan_advisable_type' => get_class($bottomInsulation),
                    'user_action_plan_advisable_id' => $bottomInsulation->id,
                ]);
                sleep(1);
                $user->userActionPlanAdvices()->create([
                    'input_source_id' => InputSource::master()->id,
                    'user_action_plan_advisable_type' => get_class($bottomInsulation),
                    'user_action_plan_advisable_id' => $bottomInsulation->id,
                ]);

                $userIdToKeep = $user->id;

                // All roles.
                $user->roles()->sync([$roles[0], $roles[1], $roles[2]]);
                $dupe->roles()->sync([$roles[0], $roles[1]]);
                $roleData[$userIdToKeep] = [$roles[0]->id, $roles[1]->id, $roles[2]->id];
            }

            // We MUST bring down the total PMs since these factoried users have attached PMs and will influence the
            // final count.
            if ($userIdToKeep === $dupe->id) {
                // We can safely query on building ID since building is attached to user which is attached
                // to a cooperation.
                $totalPMs -= PrivateMessage::where('building_id', $user->building->id)->count();
            }

            $userData[$user->account_id] = $userIdToKeep;
            ++$loop;
            if ($loop === 25) {
                // Limit is currently not supported, so we just bail.
                return false;
            }
        });

        // Assert we have 25 extra
        $this->assertDatabaseCount('users', $defaultUserCount + 275);

        $arguments = [
            'target' => 'Nieuwe Cooperatie',
            'cooperations' => $cooperationSlugs,
        ];
        $this->artisan(MergeCooperations::class, $arguments)
            ->expectsConfirmation("Do you want to merge '" . implode(', ', $cooperationSlugs) . "' to 'Nieuwe Cooperatie'?", 'no')
            ->expectsOutput('Not merging cooperations.')
            ->assertExitCode(Command::SUCCESS);

        // Not confirmed, so assert no change.
        $this->assertDatabaseCount('cooperations', $defaultCooperationCount + 5);

        $arguments['--force'] = true;
        $this->artisan(MergeCooperations::class, $arguments)
            ->doesntExpectOutput('Not merging cooperations.')
            ->assertExitCode(Command::SUCCESS);

        // We're merging 3 cooperations into one, so the total count should be lowered by 2
        $this->assertDatabaseCount('cooperation_redirects', $defaultCooperationRedirectCount + 3);
        $this->assertDatabaseCount('cooperations', $defaultCooperationCount + 3);

        // Assert merged cooperations gone
        $this->assertDatabaseHas('cooperations', [
            'slug' => 'nieuwe-cooperatie',
        ]);
        foreach ($cooperationSlugs as $slug) {
            $this->assertDatabaseMissing('cooperations', [
                'slug' => $slug,
            ]);
        }

        // Assert redirects
        $newCooperation = Cooperation::where('slug', 'nieuwe-cooperatie')->first();
        foreach ($cooperationSlugs as $slug) {
            $this->assertDatabaseHas('cooperation_redirects', [
                'from_slug' => $slug,
                'cooperation_id' => $newCooperation->id,
            ]);
        }

        // Assert dupe users have been removed, no users for old cooperations exist.
        $this->assertDatabaseCount('users', $defaultUserCount + 250);
        foreach ($cooperations as $cooperation) {
            $this->assertDatabaseMissing('users', [
                'cooperation_id' => $cooperation->id,
            ]);
        }
        // Since we merged 3 cooperations with 50 users each, there should be 150 users for the new cooperation.
        $this->assertSame(150, DB::table('users')->where('cooperation_id', $newCooperation->id)->count());

        // Assert each of these still exist as chosen user.
        foreach ($userData as $accountId => $userId) {
            $this->assertDatabaseHas('users', [
                'id' => $userId,
                'account_id' => $accountId,
                'cooperation_id' => $newCooperation->id,
            ]);
        }

        // Assert merged roles
        foreach ($roleData as $userId => $roleIds) {
            $roleIds = array_values($roleIds);
            sort($roleIds);
            $this->assertSame(
                count($roleIds),
                DB::table('model_has_roles')
                    ->where('model_id', $userId)
                    ->where('model_type', User::class)
                    ->count()
            );

            $this->assertSame(
                $roleIds,
                DB::table('model_has_roles')
                    ->where('model_id', $userId)
                    ->where('model_type', User::class)
                    ->orderBy('role_id')
                    ->pluck('role_id')
                    ->all()
            );
        }

        // Same for example buildings and questionnaires, assert missing for the old cooperation and exists for the new
        foreach ($cooperations as $cooperation) {
            $this->assertDatabaseMissing('questionnaires', [
                'cooperation_id' => $cooperation->id,
            ]);
            $this->assertDatabaseMissing('example_buildings', [
                'cooperation_id' => $cooperation->id,
            ]);
            $this->assertDatabaseMissing('private_messages', [
                'to_cooperation_id' => $cooperation->id,
            ]);
            $this->assertDatabaseMissing('private_messages', [
                'from_cooperation_id' => $cooperation->id,
            ]);
        }

        // Assert all properly moved
        $this->assertSame(
            $totalQuestionnaires,
            DB::table('questionnaires')->where('cooperation_id', $newCooperation->id)->count()
        );
        $this->assertSame(
            $totalEBs,
            DB::table('example_buildings')->where('cooperation_id', $newCooperation->id)->count()
        );
        $this->assertSame(
            $totalPMs,
            DB::table('private_messages')->where(
                fn ($q) => $q->where('to_cooperation_id', $newCooperation->id)
                    ->orWhere('from_cooperation_id', $newCooperation->id)
            )->count()
        );

        // Assert scans properly merged
        $newScans = $newCooperation->scans()->orderBy('scans.id')->pluck('scans.id')->all();
        $this->assertSame(count($scans), count($newScans));
        $this->assertSame($scans, $newScans);
    }
}