<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ToolSetting
 *
 * @property int $id
 * @property int $changed_input_source_id
 * @property int|null $input_source_id
 * @property int $building_id
 * @property bool $has_changed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource $changedInputSource
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereChangedInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereHasChanged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ToolSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ToolSetting extends Model
{
    use GetValueTrait, GetMyValuesTrait;

    protected $fillable = [
        'changed_input_source_id', 'has_changed', 'building_id',
        'input_source_id',
    ];

    protected $casts = [
        'has_changed' => 'bool',
    ];

    /**
     * check if its changed.
     *
     * @return bool
     */
    public function hasChanged(): bool
    {
        return $this->has_changed;
    }

    /**
     * Returns a collection of changed tool settings.
     *
     * Get the changed input sources for the current input source.
     *
     * @param int $buildingId
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getChangedSettings(int $buildingId)
    {
        $toolSettings = self::where('building_id', $buildingId)
            ->where('changed_input_source_id', '!=', HoomdossierSession::getInputSource())
            ->where('has_changed', true)
            ->get();

        return $toolSettings;
    }

    /**
     * Get the input source from the tool setting.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function changedInputSource()
    {
        return $this->belongsTo(InputSource::class, 'changed_input_source_id');
    }

    /**
     * Return the tool settings where has_changed is true.
     *
     * @param int $buildingId
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getUndoneChangedSettings(int $buildingId)
    {
        return self::getChangedSettings($buildingId)->where('has_changed', true);
    }
}
