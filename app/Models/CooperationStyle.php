<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CooperationStyle.
 *
 * @property int $id
 * @property int $cooperation_id
 * @property string|null $logo_url
 * @property string $primary_color
 * @property string $secundairy_color
 * @property string $tertiary_color
 * @property string $quaternary_color
 * @property string|null $css_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Cooperation $cooperation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle whereCssUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle whereQuaternaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle whereSecundairyColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle whereTertiaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CooperationStyle whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CooperationStyle extends Model
{
    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class);
    }
}
