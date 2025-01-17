<?php

namespace Tests\Unit\app\Console\Commands;

//use PHPUnit\Framework\Attributes\DataProvider;
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

    public static function missingArgumentsAbortsCommandTestProvider(): array
    {
        return [
            [[]],
            [['target' => 'Cooperation']],
            [['cooperations' => []]],
            [['cooperations' => ['cooperation-to-merge']]],
        ];
    }

    //#[DataProvider('missingArgumentsAbortsCommandTestProvider')]
    /** @dataProvider missingArgumentsAbortsCommandTestProvider */
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
        $this->assertDatabaseCount('cooperation_redirects', 0);
        $this->assertDatabaseCount('cooperations', 1);
        $this->assertDatabaseCount('users', 15);
        $this->assertSame(15, DB::table('users')->where('cooperation_id', $cooperation->id)->count());

        // Call a deny run.
        $this->artisan(MergeCooperations::class, $arguments)
            ->expectsConfirmation("Do you want to rename 'Cooperation to merge' to 'Cooperation'?", 'no')
            ->expectsOutput('Not renaming cooperation.')
            ->assertExitCode(Command::SUCCESS);

        // Assert not renamed.
        $this->assertDatabaseCount('cooperation_redirects', 0);
        $this->assertDatabaseHas('cooperations', [
            'name' => 'Cooperation to merge', 'slug' => 'cooperation-to-merge'
        ]);

        // Actually do the renaming.
        $this->artisan(MergeCooperations::class, $arguments)
            ->expectsConfirmation("Do you want to rename 'Cooperation to merge' to 'Cooperation'?", 'yes')
            ->doesntExpectOutput('Not renaming cooperation.')
            ->assertExitCode(Command::SUCCESS);

        // Assert renamed.
        $this->assertDatabaseCount('cooperation_redirects', 1);
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
        $this->markTestIncomplete();
    }
}