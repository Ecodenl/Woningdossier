<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use Illuminate\Database\Eloquent\Model;

class ToolSetting extends Model
{
    protected $fillable = [
        'changed_input_source_id', 'has_changed', 'building_id'
    ];

    protected $casts = [
        'has_changed' => 'bool'
    ];


    /**
     * check if its changed
     *
     * @return bool
     */
    public function hasChanged(): bool
    {
        return $this->has_changed;
    }


    /**
     * Return a collection of tool settings for a building where is is not the current inputsource
     *
     * @param int $buildingId
     * @return \Illuminate\Support\Collection
     */
    public static function getChangedSettings(int $buildingId)
    {
        $toolSettings = self::where('building_id', $buildingId)
            ->where('changed_input_source_id', '!=', HoomdossierSession::getInputSource())
            ->get();
        return $toolSettings;
    }

    /**
     * Get the input source from the tool setting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inputSource()
    {
        return $this->belongsTo(InputSource::class, 'changed_input_source_id');
    }
}
