<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CooperationStyle
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
 * @property-read \App\Models\Cooperation $cooperation
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle whereCssUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle whereQuaternaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle whereSecundairyColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle whereTertiaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CooperationStyle whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CooperationStyle extends Model
{
    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class);
    }
}
