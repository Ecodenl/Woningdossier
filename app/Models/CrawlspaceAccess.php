<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CrawlspaceAccess
 *
 * @property int $id
 * @property array $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlspaceAccess whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CrawlspaceAccess extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
