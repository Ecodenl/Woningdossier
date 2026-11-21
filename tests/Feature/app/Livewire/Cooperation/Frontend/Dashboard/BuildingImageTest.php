<?php

namespace Tests\Feature\app\Livewire\Cooperation\Frontend\Dashboard;

use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Helpers\RoleHelper;
use App\Livewire\Cooperation\Frontend\Dashboard\BuildingImage;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Plank\Mediable\Facades\MediaUploader;
use Tests\TestCase;

/**
 * Taking the photo of the house off the dashboard. Who may do so is the policy's business; these
 * check that the dashboard follows it, both in the button it shows and in the action behind it.
 */
final class BuildingImageTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');

        $this->cooperation = Cooperation::factory()->create();

        $resident = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        HoomdossierSession::setCooperation($this->cooperation);
    }

    private function uploadPhoto(): Media
    {
        $path = tempnam(sys_get_temp_dir(), 'media') . '.png';
        file_put_contents($path, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        ));

        /** @var Media $media */
        $media = MediaUploader::fromSource($path)
            ->toDestination('uploads', "buildings/{$this->building->id}")
            ->useFilename(uniqid('photo', false))
            ->upload();

        $this->building->syncMedia($media, MediaHelper::BUILDING_IMAGE);
        @unlink($path);

        return $media;
    }

    private function componentFor(User $user, InputSource $inputSource, string $role, bool $observing = false): Testable
    {
        $this->actingAs($user->account);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            $inputSource,
            $inputSource,
            Role::findByName($role),
        );
        HoomdossierSession::setIsObserving($observing);

        return Livewire::test(BuildingImage::class, ['building' => $this->building]);
    }

    private function residentComponent(): Testable
    {
        return $this->componentFor($this->building->user, InputSource::resident(), RoleHelper::ROLE_RESIDENT);
    }

    private function coupledCoach(): User
    {
        $coach = User::factory()->withAccount()->withBuilding()->asCoach()
            ->create(['cooperation_id' => $this->cooperation->id]);

        $this->building->buildingCoachStatuses()->create([
            'coach_id' => $coach->getKey(),
            'status'   => BuildingCoachStatus::STATUS_ADDED,
        ]);
        $this->building->buildingPermissions()->create(['user_id' => $coach->getKey()]);

        return $coach;
    }

    private function assertPhotoGone(Media $media): void
    {
        $this->assertNull($this->building->fresh()->firstMedia(MediaHelper::BUILDING_IMAGE));
        $this->assertModelMissing($media);
        // The file goes along with the row, or it stays on disk with nothing pointing at it.
        Storage::disk('uploads')->assertMissing($media->getDiskPath());
    }

    private function assertPhotoKept(Media $media): void
    {
        $this->assertModelExists($media);
        Storage::disk('uploads')->assertExists($media->getDiskPath());
    }

    public function test_the_resident_sees_the_delete_button_on_their_photo(): void
    {
        $this->uploadPhoto();

        $this->residentComponent()
            ->assertSeeText(__('home.dashboard.building.photo.delete'))
            ->assertSeeHtml('wire:click="deletePhoto"');
    }

    public function test_there_is_no_delete_button_without_a_photo(): void
    {
        $this->residentComponent()
            ->assertSeeText(__('home.dashboard.building.photo.add'))
            ->assertDontSeeText(__('home.dashboard.building.photo.delete'));
    }

    public function test_the_resident_can_delete_the_photo_of_their_house(): void
    {
        $media = $this->uploadPhoto();

        $this->residentComponent()
            ->call('deletePhoto')
            ->assertSet('image', null)
            // Back to the empty state, from where a new photo can be added.
            ->assertSeeText(__('home.dashboard.building.photo.add'))
            ->assertDontSeeText(__('home.dashboard.building.photo.delete'));

        $this->assertPhotoGone($media);
    }

    public function test_a_coupled_coach_can_delete_the_photo(): void
    {
        $media = $this->uploadPhoto();

        // A coach does not get the dashboard, and the photo is not shared with the cooperation the
        // way documents are, so they never see the button here. The action still follows the
        // policy, which lets the coach take the photo off the house as it does in the admin.
        $this->componentFor(
            $this->coupledCoach(),
            InputSource::findByShort(InputSource::COACH_SHORT),
            RoleHelper::ROLE_COACH,
        )
            ->call('deletePhoto');

        $this->assertPhotoGone($media);
    }

    public function test_an_observer_can_neither_see_nor_use_the_delete_button(): void
    {
        $media = $this->uploadPhoto();

        $coordinator = User::factory()->withAccount()->withBuilding()->asCoordinator()
            ->create(['cooperation_id' => $this->cooperation->id]);

        $this->componentFor(
            $coordinator,
            InputSource::findByShort(InputSource::COOPERATION_SHORT),
            RoleHelper::ROLE_COORDINATOR,
            observing: true,
        )
            ->assertDontSeeText(__('home.dashboard.building.photo.delete'))
            ->call('deletePhoto');

        $this->assertPhotoKept($media);
    }

    public function test_a_resident_of_another_house_cannot_delete_the_photo(): void
    {
        $media = $this->uploadPhoto();

        $neighbour = User::factory()->withAccount()->withBuilding()->asResident()
            ->create(['cooperation_id' => $this->cooperation->id]);

        // The button is hidden, so this is someone calling the action by hand.
        $this->componentFor($neighbour, InputSource::resident(), RoleHelper::ROLE_RESIDENT)
            ->call('deletePhoto');

        $this->assertPhotoKept($media);
    }
}
