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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage beingProcessed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage forMe(\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage leaveOutPersonalFiles()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage mostRecent()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereAvailableUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereFileTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereIsBeingProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileStorage withExpired()
 * @mixin \Eloquent
 */
class FileStorage extends Model
{
    use GetValueTrait, GetMyValuesTrait;

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
     * @param Builder $query
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
     * @param Builder $query
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
     * @param Builder $query
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
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeMostRecent(Builder $query, $questionnaire = null)
    {
        if ($questionnaire instanceof Questionnaire) {
//        dd($query->where('questionnaire_id', 3)->first(), $questionnaire->id);
            return $query->orderByDesc('created_at')->where('questionnaire_id', $questionnaire->id);
        }
        return $query->orderByDesc('created_at');
    }

    /**
     * Return the belongsto relationship on a cooperation.
     *
     * @return BelongsTo
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
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a specific file is being processed.
     *
     * @return bool
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
