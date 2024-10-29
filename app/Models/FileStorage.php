<?php

namespace App\Models;

use App\Scopes\AvailableScope;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\HasCooperationTrait;
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
 * @method static Builder|FileStorage allInputSources()
 * @method static Builder|FileStorage beingProcessed()
 * @method static Builder|FileStorage forAllCooperations()
 * @method static Builder|FileStorage forBuilding($building)
 * @method static Builder|FileStorage forInputSource(\App\Models\InputSource $inputSource)
 * @method static Builder|FileStorage forMe(?\App\Models\User $user = null)
 * @method static Builder|FileStorage forMyCooperation($cooperationId)
 * @method static Builder|FileStorage forUser($user)
 * @method static Builder|FileStorage leaveOutPersonalFiles()
 * @method static Builder|FileStorage mostRecent(?\App\Models\Questionnaire $questionnaire = null)
 * @method static Builder|FileStorage newModelQuery()
 * @method static Builder|FileStorage newQuery()
 * @method static Builder|FileStorage query()
 * @method static Builder|FileStorage residentInput()
 * @method static Builder|FileStorage whereAvailableUntil($value)
 * @method static Builder|FileStorage whereBuildingId($value)
 * @method static Builder|FileStorage whereCooperationId($value)
 * @method static Builder|FileStorage whereCreatedAt($value)
 * @method static Builder|FileStorage whereFileTypeId($value)
 * @method static Builder|FileStorage whereFilename($value)
 * @method static Builder|FileStorage whereId($value)
 * @method static Builder|FileStorage whereInputSourceId($value)
 * @method static Builder|FileStorage whereIsBeingProcessed($value)
 * @method static Builder|FileStorage whereQuestionnaireId($value)
 * @method static Builder|FileStorage whereUpdatedAt($value)
 * @method static Builder|FileStorage withExpired()
 * @mixin \Eloquent
 */
class FileStorage extends Model
{
    use GetValueTrait, GetMyValuesTrait, HasCooperationTrait;

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new AvailableScope());
    }

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
     * Query to scope the expired files.
     */
    public function scopeWithExpired(Builder $query): Builder
    {
        return $query->withoutGlobalScope(new AvailableScope());
    }

    /**
     * Query to leave out the personal files.
     */
    public function scopeLeaveOutPersonalFiles(Builder $query): Builder
    {
        return $query->whereNull('building_id');
    }

    /**
     * Query to scope the file's that are being processed.
     */
    public function scopeBeingProcessed(Builder $query): Builder
    {
        return $query->where('is_being_processed', true);
    }

    /**
     * Query to scope the most recent report.
     */
    public function scopeMostRecent(Builder $query, Questionnaire $questionnaire = null): Builder
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
    public function isProcessed()
    {
        $availableUntil = $this->created_at->addDays($this->fileType->duration ?? 7);
        $this->available_until = $availableUntil;
        $this->is_being_processed = false;
        $this->save();
    }
}
