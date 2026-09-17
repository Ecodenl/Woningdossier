<?php

namespace App\Models;

use App\Enums\MappingType;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * One product from SmartTwin's solution catalogue.
 *
 * Imported by api:smarttwin:import-solutions so the coupling screen has something to list. Which
 * measure application a solution is stays in `mappings` — see MappingType::SMARTTWIN_SOLUTION_MEASURE_APPLICATION
 * and SolutionMeasures — because an advice can name a product this table has never seen, and that
 * coupling has to resolve regardless.
 *
 * @property int $id
 * @property string $external_id
 * @property string $name
 * @property string|null $kind
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder<static>|SmartTwinSolution withdrawn()
 * @method static Builder<static>|SmartTwinSolution newModelQuery()
 * @method static Builder<static>|SmartTwinSolution newQuery()
 * @method static Builder<static>|SmartTwinSolution query()
 * @mixin \Eloquent
 */
class SmartTwinSolution extends Model
{
    protected $table = 'smarttwin_solutions';

    protected $fillable = [
        'external_id',
        'name',
        'kind',
        'last_seen_at',
    ];

    /**
     * A solution id reads as `<kind of measure>|<provider>:<product>`, for instance
     * `Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls`. The part in front is the kind,
     * which is how the catalogue is grouped for whoever does the coupling.
     *
     * Undocumented in the API specification, hence the null when an id does not read that way.
     */
    public const PROVIDER_SEPARATOR = '|';

    public static function kindOf(string $externalId): ?string
    {
        if (! str_contains($externalId, self::PROVIDER_SEPARATOR)) {
            return null;
        }

        $kind = trim(explode(self::PROVIDER_SEPARATOR, $externalId, 2)[0]);

        return '' === $kind ? null : $kind;
    }

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
        ];
    }

    /**
     * Products the last import did not see, which means SmartTwin withdrew them.
     *
     * There is no "last import" timestamp anywhere; the newest last_seen_at is it, because an import
     * touches every product the catalogue holds.
     */
    #[Scope]
    protected function withdrawn(Builder $query): Builder
    {
        $lastImport = static::max('last_seen_at');

        return is_null($lastImport)
            ? $query->whereRaw('1 = 0')
            : $query->where('last_seen_at', '<', $lastImport);
    }

    /**
     * The type every coupling of a solution carries, so callers need not reach for the enum.
     */
    public static function mappingType(): string
    {
        return MappingType::SMARTTWIN_SOLUTION_MEASURE_APPLICATION->value;
    }
}
