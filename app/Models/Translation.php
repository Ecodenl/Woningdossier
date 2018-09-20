<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Translation.
 *
 * @property int $id
 * @property string $key
 * @property string $language
 * @property string $translation
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereTranslation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Translation extends Model
{
    public $fillable = [
        'key', 'translation', 'language',
    ];
}
