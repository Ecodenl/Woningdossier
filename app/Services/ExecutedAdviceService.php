<?php

namespace App\Services;

use App\Helpers\Hoomdossier;
use App\Helpers\MediaHelper;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Media;
use App\Models\UserActionPlanAdvice;
use App\Services\SmartTwin\SharedWoonplan;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Plank\Mediable\Facades\MediaUploader;

/**
 * "Uitgevoerd": a measure the resident carried out, with the date, a description and documents.
 *
 * Stored on the advice itself (executed_at, executed_description) and on every input source's row
 * of it, so whichever row a later save copies to master carries it along. The documents hang on
 * the building, so they are on the "Bestanden" page too, each tied to the measure through a custom
 * property. Not to a row: a measure has a row per input source.
 *
 * Only available once SmartTwin owns the woonplan (isAvailableFor()). Everything else is built so
 * the old woonplan can have it as well by widening that one check: the calculation never deletes or
 * replaces an executed advice, with or without SmartTwin (see
 * UserActionPlanAdviceService::clearForStep() and ::isOwnedExternally()). The steps for that are in
 * the vault decision on "maatregel uitgevoerd", section "Bouwinstructie".
 */
class ExecutedAdviceService
{
    /** The custom property on a building document that ties it to an executed measure. */
    public const DOCUMENT_PROPERTY = 'executed_measure';

    public function __construct(private readonly SharedWoonplan $sharedWoonplan)
    {
    }

    /**
     * Whether "uitgevoerd" is offered for this building. The one place to widen it for the old
     * woonplan.
     */
    public static function isAvailableFor(Building $building): bool
    {
        return Hoomdossier::smartTwinOwnsWoonplanFor($building);
    }

    /**
     * Marks the measure as carried out and moves its card to "In orde", for resident, coach and
     * master alike. Any row of the advice may be passed in.
     */
    public function markExecuted(UserActionPlanAdvice $advice, CarbonInterface $date, ?string $description = null): UserActionPlanAdvice
    {
        return $this->write($advice, [
            'executed_at'          => $date,
            'executed_description' => $description,
            'category'             => UserActionPlanAdviceService::CATEGORY_COMPLETE,
        ]);
    }

    /**
     * Takes "uitgevoerd" back. The card goes to "Nu aanpakken": where it was before is not kept, and
     * an advice that is not carried out is something to do. Its documents stay on the building.
     */
    public function undoExecuted(UserActionPlanAdvice $advice): UserActionPlanAdvice
    {
        return $this->write($advice, [
            'executed_at'          => null,
            'executed_description' => null,
            'category'             => UserActionPlanAdviceService::CATEGORY_TO_DO,
        ]);
    }

    /**
     * Adds a document to an executed measure: on the building, with the given tag ("Factuur" unless
     * said otherwise; another one can be picked on the "Bestanden" page afterwards).
     */
    public function attachDocument(
        UserActionPlanAdvice $advice,
        string $path,
        string $filename,
        InputSource $uploadedBy,
        string $tag = MediaHelper::INVOICE,
    ): Media
    {
        if (! array_key_exists($tag, MediaHelper::getFillableTagsForClass(Building::class))) {
            throw new InvalidArgumentException("A building document cannot be tagged '{$tag}'.");
        }

        $master = $this->masterOf($advice);
        $building = $this->buildingOf($master);

        // As the "Bestanden" page does: the coach's own uploads are shared, the resident's follow
        // whether they allow the cooperation access.
        $shareWithCooperation = in_array($uploadedBy->short, [InputSource::COACH_SHORT, InputSource::COOPERATION_SHORT], true)
            || (bool) $building->user()->forAllCooperations()->first()?->allow_access;

        /** @var Media $media */
        $media = MediaUploader::fromSource($path)
            ->toDestination('uploads', "buildings/{$building->id}")
            ->useFilename($filename)
            ->beforeSave(function (Media $media) use ($shareWithCooperation, $master, $uploadedBy) {
                $media->custom_properties = [
                    'share_with_cooperation' => $shareWithCooperation,
                    self::DOCUMENT_PROPERTY  => $this->measureKey($master),
                ];
                $media->input_source_id = $uploadedBy->id;
            })
            ->upload();

        $building->attachMedia($media, $tag);

        return $media;
    }

    /**
     * The documents of an executed measure.
     *
     * @return Collection<int, Media>
     */
    public function documentsOf(UserActionPlanAdvice $advice): Collection
    {
        $master = $this->masterOf($advice);
        $key = $this->measureKey($master);

        $mediaIds = $this->buildingOf($master)->media()->pluck('media.id');

        return Media::query()->whereIn('id', $mediaIds)->get()
            ->filter(fn (Media $media) => data_get($media->custom_properties, self::DOCUMENT_PROPERTY) == $key)
            ->values();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function write(UserActionPlanAdvice $advice, array $attributes): UserActionPlanAdvice
    {
        $master = $this->masterOf($advice);

        if (! static::isAvailableFor($this->buildingOf($master))) {
            throw new DomainException('"Uitgevoerd" is only available once SmartTwin owns the woonplan.');
        }

        $master->update($attributes);
        $this->sharedWoonplan->copyFromMaster($master);

        return $master->fresh();
    }

    private function masterOf(UserActionPlanAdvice $advice): UserActionPlanAdvice
    {
        $master = $this->sharedWoonplan->masterRowOf($advice);

        if (! $master instanceof UserActionPlanAdvice) {
            throw new DomainException('The advice has no master row.');
        }

        return $master;
    }

    private function buildingOf(UserActionPlanAdvice $advice): Building
    {
        return Building::where('user_id', $advice->user_id)->firstOrFail();
    }

    /**
     * What ties a document to a measure across input sources: the measure, or the hash of an own
     * measure, whose model differs per input source.
     *
     * @return array{type: string, id?: int, hash?: string}
     */
    private function measureKey(UserActionPlanAdvice $advice): array
    {
        if (CustomMeasureApplication::class === $advice->user_action_plan_advisable_type) {
            return [
                'type' => CustomMeasureApplication::class,
                'hash' => (string) CustomMeasureApplication::allInputSources()->find($advice->user_action_plan_advisable_id)?->hash,
            ];
        }

        return ['type' => $advice->user_action_plan_advisable_type, 'id' => (int) $advice->user_action_plan_advisable_id];
    }
}
