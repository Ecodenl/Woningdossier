<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CooperationStep.
 *
 * @property int      $id
 * @property int      $cooperation_id
 * @property int      $step_id
 * @property bool     $is_active
 * @property int|null $order
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationStep query()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationStep whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationStep whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationStep whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationStep whereStepId($value)
 * @mixin \Eloquent
 */
class CooperationStep extends Model
{
    protected $fillable = [
        'is_active', 'order', 'cooperation_id', 'step_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
