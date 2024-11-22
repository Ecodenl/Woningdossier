<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ApplianceProperty
 *
 * @property int $id
 * @property int|null $appliance_id
 * @property string $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Appliance|null $appliance
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplianceProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplianceProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplianceProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplianceProperty whereApplianceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplianceProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplianceProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplianceProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplianceProperty whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplianceProperty whereValue($value)
 * @mixin \Eloquent
 */
class ApplianceProperty extends Model
{
    public function appliance(): BelongsTo
    {
        return $this->belongsTo(Appliance::class);
    }
}
