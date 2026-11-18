<?php

namespace Tests\Feature\app\Jobs\SmartTwin\Out;

use App\Jobs\SmartTwin\Out\CreateCoachAccount;
use App\Jobs\SmartTwin\Out\CreateUserAccount;
use App\Jobs\SmartTwin\Out\DeleteAccount;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\SmartTwin\Api\Resources\User as UserResource;
use App\Services\SmartTwin\Api\SmartTwinApi;
use App\Services\SmartTwin\Api\UserRole;
use Database\Seeders\InputSourcesTableSeeder;
use Database\Seeders\RoleTableSeeder;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * SmartTwin answers a 404 while its server is down, which can last from seconds to minutes. That is
 * waited out; any other rejection will not change on a retry.
 */
final class CreateSmartTwinAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([InputSourcesTableSeeder::class, RoleTableSeeder::class]);
        config()->set('hoomdossier.services.smarttwin.enabled', true);
    }

    public static function jobProvider(): array
    {
        return [
            'resident' => [CreateUserAccount::class],
            'coach'    => [CreateCoachAccount::class],
        ];
    }

    public static function linkedRoleProvider(): array
    {
        return [
            'resident' => [CreateUserAccount::class, UserRole::Resident],
            'coach'    => [CreateCoachAccount::class, UserRole::Advisor],
        ];
    }

    /**
     * @param  class-string<CreateUserAccount|CreateCoachAccount>  $jobClass
     */
    private function job(string $jobClass, int $attempt = 1): CreateUserAccount|CreateCoachAccount
    {
        $user = User::factory()->withAccount()
            ->create(['cooperation_id' => Cooperation::factory()->create()->id]);

        $job = (new $jobClass($user))->withFakeQueueInteractions();
        $job->job->attempts = $attempt;

        return $job;
    }

    private function apiAnswering(int $status): SmartTwinApi
    {
        $users = Mockery::mock(UserResource::class);
        $users->shouldReceive('create')->andThrow(new ClientException(
            'SmartTwin says no',
            new Request('POST', 'api/account/v1/users'),
            new Response($status),
        ));

        return $this->apiWith($users);
    }

    private function apiWith(UserResource $users): SmartTwinApi
    {
        $api = Mockery::mock(SmartTwinApi::class);
        $api->shouldReceive('user')->andReturn($users);

        return $api;
    }

    #[DataProvider('jobProvider')]
    public function test_a_404_is_waited_out_longer_with_every_attempt(string $jobClass): void
    {
        foreach ([1 => 5, 2 => 15, 3 => 30, 4 => 60, 5 => 120, 6 => 300, 7 => 600] as $attempt => $delay) {
            $job = $this->job($jobClass, $attempt);

            $job->handle($this->apiAnswering(404));

            $job->assertReleased($delay)->assertNotFailed();
        }
    }

    #[DataProvider('jobProvider')]
    public function test_a_404_on_the_last_attempt_fails_the_job(string $jobClass): void
    {
        $job = $this->job($jobClass, 8);

        $job->handle($this->apiAnswering(404));

        $job->assertFailed()->assertNotReleased();
    }

    #[DataProvider('jobProvider')]
    public function test_any_other_rejection_fails_the_job_right_away(string $jobClass): void
    {
        $job = $this->job($jobClass);

        $job->handle($this->apiAnswering(400));

        $job->assertFailed()->assertNotReleased();
    }

    #[DataProvider('linkedRoleProvider')]
    public function test_the_user_is_linked_once_smart_twin_is_back(string $jobClass, UserRole $role): void
    {
        $job = $this->job($jobClass, 3);

        $users = Mockery::mock(UserResource::class);
        $users->shouldReceive('create')->once()->andReturn(['userId' => 'smart-twin-id']);

        $job->handle($this->apiWith($users));

        $job->assertNotReleased()->assertNotFailed();
        $account = $job->user->account->fresh();
        $this->assertSame('smart-twin-id', $account->smartTwinUserId());
        $this->assertSame($role, $account->smartTwinUserRole());
    }

    public function test_deleting_a_smart_twin_user_waits_out_an_outage_the_same_way(): void
    {
        $job = new DeleteAccount('smart-twin-id');
        $create = new CreateUserAccount(new User());

        $this->assertSame($create->tries, $job->tries);
        $this->assertSame($create->backoff, $job->backoff);
    }
}
