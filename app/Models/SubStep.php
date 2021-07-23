<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubStep
 *
 * @property int $id
 * @property mixed $name
 * @property int $order
 * @property int $step_id
 * @property int $sub_step_template_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep whereSubStepTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStep whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubStep extends Model
{
}
