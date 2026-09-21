<?php

namespace Tests\Feature\app\Policies;

use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Plank\Mediable\Facades\MediaUploader;
use Tests\TestCase;

/**
 * What a visitor who is not logged in may read.
 *
 * The rule used to be "anything attached to a cooperation", which the PDF report depended on for
 * the images it fetched over HTTP. It reads those off the disk now, so the rule is down to the two
 * tags that are genuinely on the login page. These pin that down, because widening it again is a
 * one-line change that nothing else would notice.
 */
final class MediaPolicyTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cooperation = Cooperation::factory()->create();

        $resident = User::factory()
            ->withAccount()
            ->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);

        $this->building = Building::factory()->create(['user_id' => $resident->id]);

        HoomdossierSession::setCooperation($this->cooperation);
    }

    private function uploadFor(object $mediable, string $tag): Media
    {
        $path = tempnam(sys_get_temp_dir(), 'media') . '.png';
        file_put_contents($path, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        ));

        /** @var Media $media */
        $media = MediaUploader::fromSource($path)
            ->toDestination('uploads', 'policy-test')
            ->useFilename(uniqid('probe', false))
            ->upload();

        $mediable->syncMedia($media, [$tag]);
        @unlink($path);

        return $media;
    }

    private function guestMayView(Media $media): bool
    {
        return Gate::forUser(null)->allows('view', [$media, null]);
    }

    public function test_a_guest_may_read_the_cooperation_logo(): void
    {
        // It is on the login page, where there is no session to authorise it with.
        $this->assertTrue($this->guestMayView($this->uploadFor($this->cooperation, MediaHelper::LOGO)));
    }

    public function test_a_guest_may_read_the_cooperation_background(): void
    {
        $this->assertTrue($this->guestMayView($this->uploadFor($this->cooperation, MediaHelper::BACKGROUND)));
    }

    public function test_a_guest_may_not_read_the_pdf_background(): void
    {
        // Only the report uses it, and the report reads it off the disk.
        $this->assertFalse($this->guestMayView($this->uploadFor($this->cooperation, MediaHelper::PDF_BACKGROUND)));
    }

    public function test_a_guest_may_not_read_a_building_photo(): void
    {
        $this->assertFalse($this->guestMayView($this->uploadFor($this->building, MediaHelper::BUILDING_IMAGE)));
    }

    public function test_the_resident_may_read_the_photo_of_their_own_house(): void
    {
        $media = $this->uploadFor($this->building, MediaHelper::BUILDING_IMAGE);

        $resident = $this->building->user;
        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        $this->actingAs($resident->account);
        HoomdossierSession::setHoomdossierSessions(
            $this->building,
            $inputSource,
            $inputSource,
            Role::findByName(RoleHelper::ROLE_RESIDENT),
        );

        // Whoever lives there sees it regardless of who uploaded it.
        $this->assertTrue(Gate::allows('view', [$media, $inputSource]));
    }
}
