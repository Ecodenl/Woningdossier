<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DossierSetting
 *
 * @property int $id
 * @property int|null $input_source_id
 * @property int $building_id
 * @property string $type
 * @property string|null $done_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting whereDoneAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DossierSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DossierSetting extends Model
{
    use HasFactory, GetMyValuesTrait, GetValueTrait;

    public $fillable = ['building_id', 'input_source_id', 'type', 'done_at'];
}
