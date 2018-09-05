<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CrawlspaceAccess
 *
 * @property int $id
 * @property string $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CrawlspaceAccess translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CrawlspaceAccess whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CrawlspaceAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CrawlspaceAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CrawlspaceAccess whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CrawlspaceAccess whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CrawlspaceAccess whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CrawlspaceAccess extends Model
{
    use TranslatableTrait;


}
