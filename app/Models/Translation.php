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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Translation query()
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

    /**
     * Return the translation from a key / uuid.
     *
     * @param $key
     *
     * @return mixed|string
     */
    public static function getTranslationFromKey($key): string
    {
        if (self::where('key', $key)->first() instanceof self) {
            return (string) self::where('key', $key)->first()->translation;
        }

        return (string) $key;
    }
}
