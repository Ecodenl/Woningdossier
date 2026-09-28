<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ElementValue
 *
 * @property int $id
 * @property int $element_id
 * @property array<array-key, mixed> $value
 * @property float|null $calculate_value
 * @property int $order
 * @property array<array-key, mixed>|null $configurations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Element $element
 * @property-read int $insulation_factor
 * @property-read array $translatable_columns_from
 * @property-read mixed $translations
 * @method static \Database\Factories\ElementValueFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereConfigurations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereValue($value)
 * @mixin \Eloquent
 */
class ElementValue extends Model
{
    use HasFactory;

    use HasTranslations;

    protected $translatable = [
        'value',
    ];

    protected function casts(): array
    {
        return [
            'configurations' => 'array',
        ];
    }

    /**
     * The lowest calculate value that counts as insulated, per insulation element.
     *
     * The scales do not run equally long. Wall, floor and roof all start with Onbekend and Geen
     * isolatie, but the floor has a Slechte isolatie between Geen and Matige that the other two do
     * not, so its boundary sits one higher. Keeping the numbers here rather than spread over the
     * calculators is what kept the last shift of the scale from silently filing a badly insulated
     * floor as one that needs nothing.
     */
    private const INSULATED_FROM = [
        'wall-insulation'  => 3, // Matige isolatie
        'floor-insulation' => 4, // Matige isolatie, met Slechte isolatie op 3
        'roof-insulation'  => 3, // Matige isolatie
    ];

    /** The default for any element without an entry above, which is the boundary as it always was. */
    private const INSULATED_FROM_DEFAULT = 3;

    public static function insulatedFromCalculateValue(string $elementShort): int
    {
        return self::INSULATED_FROM[$elementShort] ?? self::INSULATED_FROM_DEFAULT;
    }

    /**
     * Whether this answer describes something that already has insulation worth the name.
     *
     * Drives two things: whether a measure can still save gas, and whether its advice lands in the
     * action plan as something to do or as something already done.
     */
    public function countsAsInsulated(): bool
    {
        return $this->calculate_value >= self::insulatedFromCalculateValue($this->element->short ?? '');
    }

    # Attributes
    public function getInsulationFactorAttribute(): int
    {
        $configurations = $this->configurations;
        return (int) ($configurations['insulation_factor'] ?? 0);
    }

    # Relations
    public function element(): BelongsTo
    {
        return $this->belongsTo(Element::class);
    }
}
