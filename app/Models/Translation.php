<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Translation
 *
 * @property int $id
 * @property string $key
 * @property string $language
 * @property string $translation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereTranslation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereUpdatedAt($value)
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
