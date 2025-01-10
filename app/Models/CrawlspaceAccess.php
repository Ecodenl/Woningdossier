<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CrawlspaceAccess
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CrawlspaceAccess whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CrawlspaceAccess extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
