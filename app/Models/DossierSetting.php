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
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting whereDoneAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DossierSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DossierSetting extends Model
{
    use HasFactory, GetMyValuesTrait, GetValueTrait;

    public $fillable = ['building_id', 'input_source_id', 'type', 'done_at'];
}
