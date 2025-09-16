<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Scopes\AvailableScope;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\HasCooperationTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\FileStorage
 *
 * @property int $id
 * @property int|null $cooperation_id
 * @property int|null $building_id
 * @property int|null $questionnaire_id
 * @property int|null $input_source_id
 * @property int $file_type_id
 * @property string $filename
 * @property \Illuminate\Support\Carbon|null $available_until
 * @property bool $is_being_processed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\Cooperation|null $cooperation
 * @property-read \App\Models\FileType $fileType
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static Builder<static>|FileStorage allInputSources()
 * @method static Builder<static>|FileStorage forAllCooperations()
 * @method static Builder<static>|FileStorage forBuilding(\App\Models\Building|int $building)
 * @method static Builder<static>|FileStorage forInputSource(\App\Models\InputSource $inputSource)
 * @method static Builder<static>|FileStorage forMe(?\App\Models\User $user = null)
 * @method static Builder<static>|FileStorage forMyCooperation(\App\Models\Cooperation|int $cooperation)
 * @method static Builder<static>|FileStorage forUser(\App\Models\User|int $user)
 * @method static Builder<static>|FileStorage newModelQuery()
 * @method static Builder<static>|FileStorage newQuery()
 * @method static Builder<static>|FileStorage query()
 * @method static Builder<static>|FileStorage residentInput()
 * @method static Builder<static>|FileStorage whereAvailableUntil($value)
 * @method static Builder<static>|FileStorage whereBuildingId($value)
 * @method static Builder<static>|FileStorage whereCooperationId($value)
 * @method static Builder<static>|FileStorage whereCreatedAt($value)
 * @method static Builder<static>|FileStorage whereFileTypeId($value)
 * @method static Builder<static>|FileStorage whereFilename($value)
 * @method static Builder<static>|FileStorage whereId($value)
 * @method static Builder<static>|FileStorage whereInputSourceId($value)
 * @method static Builder<static>|FileStorage whereIsBeingProcessed($value)
 * @method static Builder<static>|FileStorage whereQuestionnaireId($value)
 * @method static Builder<static>|FileStorage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
#[ScopedBy([AvailableScope::class])]
class FileStorage extends Model
{
    use GetValueTrait, GetMyValuesTrait, HasCooperationTrait;


    protected $fillable = [
        'cooperation_id', 'questionnaire_id', 'filename', 'building_id', 'input_source_id', 'file_type_id',
        'is_being_processed', 'available_until',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_being_processed' => 'bool',
            'available_until' => 'datetime',
        ];
    }

    /**
     * Scope to query without the available scope, meaning all file storages will be returned.
     */
    #[Scope]
    protected function withExpired(Builder $query): Builder
    {
        return $query->withoutGlobalScope(new AvailableScope());
    }

    /**
     * Scope to query only the expired files.
     */
    #[Scope]
    protected function expired(Builder $query): Builder
    {
        return $query->withExpired()->where('available_until', '<', Carbon::now());
    }

    /**
     * Query to leave out the personal files.
     */
    #[Scope]
    protected function leaveOutPersonalFiles(Builder $query): Builder
    {
        return $query->whereNull('building_id');
    }

    /**
     * Query to scope the file's that are being processed.
     */
    #[Scope]
    protected function beingProcessed(Builder $query): Builder
    {
        return $query->where('is_being_processed', true);
    }

    /**
     * Query to scope the most recent report.
     */
    #[Scope]
    protected function mostRecent(Builder $query, Questionnaire $questionnaire = null): Builder
    {
        if ($questionnaire instanceof Questionnaire) {
            return $query->orderByDesc('created_at')->where('questionnaire_id', $questionnaire->id);
        }

        return $query->orderByDesc('created_at');
    }

    /**
     * Return the belongsto relationship on a cooperation.
     */
    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class);
    }

    public function fileType(): BelongsTo
    {
        return $this->belongsTo(FileType::class, 'file_type_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Check if a specific file is being processed.
     */
    public function isBeingProcessed(): bool
    {
        return $this->is_being_processed;
    }

    /**
     * Method that's used when the file is done processing.
     */
    public function finishProcess(): void
    {
        $availableUntil = $this->created_at->addDays($this->fileType->duration ?? 7);
        $this->available_until = $availableUntil;
        $this->is_being_processed = false;
        $this->save();
    }
}
