<?php

namespace App\Models;

use App\Scopes\AvailableScope;
use App\Scopes\CooperationScope;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\FileStorage.
 *
 * @property int                             $id
 * @property int|null                        $cooperation_id
 * @property int|null                        $building_id
 * @property int|null                        $questionnaire_id
 * @property int|null                        $input_source_id
 * @property int                             $file_type_id
 * @property string                          $filename
 * @property \Illuminate\Support\Carbon|null $available_until
 * @property bool                            $is_being_processed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Cooperation|null    $cooperation
 * @property \App\Models\FileType            $fileType
 * @property \App\Models\InputSource|null    $inputSource
 * @property \App\Models\User                $user
 *
 * @method static Builder|FileStorage allInputSources()
 * @method static Builder|FileStorage beingProcessed()
 * @method static Builder|FileStorage forBuilding(\App\Models\Building $building)
 * @method static Builder|FileStorage forInputSource(\App\Models\InputSource $inputSource)
 * @method static Builder|FileStorage forMe(\App\Models\User $user = null)
 * @method static Builder|FileStorage leaveOutPersonalFiles()
 * @method static Builder|FileStorage mostRecent(\App\Models\Questionnaire $questionnaire = null)
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
    use GetValueTrait;
    use GetMyValuesTrait;

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new AvailableScope());
        static::addGlobalScope(new CooperationScope());
    }

    protected $fillable = [
        'cooperation_id', 'questionnaire_id', 'filename', 'user_id', 'input_source_id', 'file_type_id', 'content_type', 'is_being_processed', 'available_until',
    ];

    /**
     * Attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_being_processed' => 'bool',
        'available_until' => 'datetime',
    ];

    /**
     * Query to scope the expired files.
     *
     * @return Builder
     */
    public function scopeWithExpired(Builder $query)
    {
        return $query->withoutGlobalScope(new AvailableScope());
    }

    /**
     * Query to leave out the personal files.
     *
     * @return Builder
     */
    public function scopeLeaveOutPersonalFiles(Builder $query)
    {
        return $query->whereNull('building_id');
    }

    /**
     * Query to scope the file's that are being processed.
     *
     * @return Builder
     */
    public function scopeBeingProcessed(Builder $query)
    {
        return $query->where('is_being_processed', true);
    }

    /**
     * Query to scope the most recent report.
     *
     * @return Builder
     */
    public function scopeMostRecent(Builder $query, Questionnaire $questionnaire = null)
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

    public function fileType()
    {
        return $this->belongsTo(FileType::class, 'file_type_id');
    }

    /**
     * Return the belongsto relationship on a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
