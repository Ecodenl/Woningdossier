<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\CooperationPresetContent
 *
 * @property int $id
 * @property int $cooperation_preset_id
 * @property array $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CooperationPreset $cooperationPreset
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPresetContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPresetContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPresetContent query()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPresetContent whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPresetContent whereCooperationPresetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPresetContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPresetContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPresetContent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CooperationPresetContent extends Model
{
    protected $fillable = [
        'cooperation_preset_id', 'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function cooperationPreset(): BelongsTo
    {
        return $this->belongsTo(CooperationPreset::class);
    }
}