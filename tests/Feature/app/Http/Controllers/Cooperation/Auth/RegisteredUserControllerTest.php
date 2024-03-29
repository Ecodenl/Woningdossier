<?php

namespace Tests\Feature\app\Http\Controllers\Cooperation\Auth;

use App\Helpers\Arr;
use App\Helpers\ToolQuestionHelper;
use App\Models\Client;
use App\Models\Cooperation;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Dflydev\DotAccessData\Data;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use OwenIt\Auditing\Drivers\Database;
use Tests\MocksLvbag;
use Tests\TestCase;

class RegisteredUserControllerTest extends TestCase
{
    use WithFaker,
        RefreshDatabase,
        MocksLvbag;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;
    private array $formData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formData = [
            "email" => $this->faker->email,
            "first_name" => "Demo",
            "last_name" => "Example",
            'address' => [
                "postal_code" => "3255MC",
                "number" => "13",
                "extension" => "",
                "street" => "Boezemweg",
                "city" => "Oudetonge",
            ],
            "phone_number" => null,
            'allow_access' => true,
            'password' => $this->faker->password('8')
        ];
        $this->mockLvbagClientAdresUitgebreid($this->formData['address'])->createLvbagMock();
    }

    public function test_valid_data_registers_new_account()
    {
        /** @var Cooperation $cooperation */
        $cooperation = Cooperation::factory()->create();

        /** @var Client $client */
        $this->formData['password_confirmation'] = $this->formData['password'];
        $response = $this->post(route('cooperation.register.store', compact('cooperation')), $this->formData);
\Log::debug($response->statusText());
\Log::debug($response->getStatusCode());
        $response->assertRedirect(route('cooperation.auth.verification.notice', compact('cooperation')));

        $this->assertDatabaseHas('accounts', ['email' => $this->formData['email']]);
        $account = DB::table('accounts')->where('email', $this->formData['email'])->first();
        $this->assertDatabaseHas('users', ['account_id' => $account->id, 'allow_access' => 1]);
        $user = DB::table('users')->where('account_id', $account->id)->first();
        $this->assertDatabaseHas('buildings', ['user_id' => $user->id]);
    }
}
