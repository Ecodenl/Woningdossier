<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\ExampleBuildingContent
 *
 * @property int $id
 * @property int $example_building_id
 * @property int|null $build_year
 * @property array|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExampleBuilding $exampleBuilding
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuildingContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuildingContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuildingContent query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuildingContent whereBuildYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuildingContent whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuildingContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuildingContent whereExampleBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuildingContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuildingContent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExampleBuildingContent extends Model
{
    public $fillable = [
        'build_year', 'content',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array',
    ];

    public function exampleBuilding()
    {
        return $this->belongsTo(ExampleBuilding::class);
    }

    public function getValue($key)
    {
        return Arr::get($this->content, $key);
//        // for some weird reason the array get does not work, while it should do the same thing
//        return Arr::dot($this->content)[$key] ?? '';
//        return array_get($this->content, $key);
    }
}
