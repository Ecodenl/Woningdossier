<?php

namespace Tests\Feature\app\Livewire\Cooperation\Frontend\Layouts\Parts\SmartTwin;

use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Helpers\RoleHelper;
use App\Livewire\Cooperation\Frontend\Layouts\Parts\SmartTwin\NavCounters;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Plank\Mediable\Facades\MediaUploader;
use Tests\TestCase;

/**
 * The shared files item counts what the file overview shows to whoever is looking, not every file
 * on the building, and says so in its label rather than in a badge.
 */
final class NavCountersTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->cooperation = Cooperation::factory()->create();

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        HoomdossierSession::setCooperation($this->cooperation);
        URL::defaults(['cooperation' => $this->cooperation->slug]);
    }

    private function uploadFile(string $tag, bool $sharedWithCooperation): Media
    {
        $path = tempnam(sys_get_temp_dir(), 'media') . '.png';
        file_put_contents($path, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        ));

        /** @var Media $media */
        $media = MediaUploader::fromSource($path)
            ->toDestination('uploads', 'nav-counters-test')
            ->useFilename(uniqid('probe', false))
            ->upload();

        $media->custom_properties = ['share_with_cooperation' => $sharedWithCooperation];
        $media->save();

        $this->building->syncMedia($media, [$tag]);
        @unlink($path);

        return $media;
    }

    private function componentFor(User $user, InputSource $inputSource, string $role): Testable
    {
        $this->actingAs($user->account);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            $inputSource,
            $inputSource,
            Role::findByName($role),
        );

        return Livewire::test(NavCounters::class, ['fileUrl' => '/files']);
    }

    private function fileCountFor(User $user, InputSource $inputSource, string $role): int
    {
        return $this->componentFor($user, $inputSource, $role)->get('fileCount');
    }

    private function residentComponent(): Testable
    {
        return $this->componentFor($this->building->user, InputSource::resident(), RoleHelper::ROLE_RESIDENT);
    }

    public function test_the_label_has_no_count_without_files(): void
    {
        $this->residentComponent()
            ->assertSeeText('Bestanden')
            ->assertDontSeeText('0 Bestanden')
            ->assertDontSeeHtml('nav-badge');
    }

    public function test_the_label_is_singular_for_one_file(): void
    {
        $this->uploadFile(MediaHelper::GENERIC_FILE, true);

        $this->residentComponent()
            ->assertSeeText('1 Bestand')
            ->assertDontSeeText('Bestanden');
    }

    public function test_the_label_carries_the_count_for_more_files(): void
    {
        $this->uploadFile(MediaHelper::GENERIC_FILE, true);
        $this->uploadFile(MediaHelper::REPORT, true);

        $this->residentComponent()
            ->assertSeeText('2 Bestanden')
            // The count moved into the label, so there is no badge next to it any more.
            ->assertDontSeeHtml('nav-badge');
    }

    public function test_the_label_does_not_call_the_files_shared(): void
    {
        // The resident's count includes the files they kept to themselves, so "shared" would lie.
        $this->uploadFile(MediaHelper::GENERIC_FILE, true);
        $this->uploadFile(MediaHelper::REPORT, false);

        $this->residentComponent()
            ->assertSeeText('2 Bestanden')
            ->assertDontSeeText('Gedeeld')
            ->assertDontSeeText('gedeeld');
    }

    public function test_the_resident_counts_every_file_on_their_house(): void
    {
        $this->uploadFile(MediaHelper::GENERIC_FILE, true);
        $this->uploadFile(MediaHelper::REPORT, false);

        $count = $this->fileCountFor($this->building->user, InputSource::resident(), RoleHelper::ROLE_RESIDENT);

        $this->assertSame(2, $count);
    }

    public function test_the_cooperation_counts_only_the_files_shared_with_it(): void
    {
        $this->uploadFile(MediaHelper::GENERIC_FILE, true);
        $this->uploadFile(MediaHelper::REPORT, false);

        // withBuilding() because every real user has one, and the messages item asks whether the
        // coordinator is filling the tool for another building than their own.
        $coordinator = User::factory()->withAccount()->withBuilding()->asCoordinator()
            ->create(['cooperation_id' => $this->cooperation->id]);

        $count = $this->fileCountFor(
            $coordinator,
            InputSource::findByShort(InputSource::COOPERATION_SHORT),
            RoleHelper::ROLE_COORDINATOR,
        );

        // The overview leaves out the report the resident kept to themselves, so the badge does too.
        $this->assertSame(1, $count);
    }

    public function test_the_building_photo_is_not_counted(): void
    {
        $this->uploadFile(MediaHelper::GENERIC_FILE, true);
        $this->uploadFile(MediaHelper::BUILDING_IMAGE, true);

        $count = $this->fileCountFor($this->building->user, InputSource::resident(), RoleHelper::ROLE_RESIDENT);

        $this->assertSame(1, $count);
    }
}
