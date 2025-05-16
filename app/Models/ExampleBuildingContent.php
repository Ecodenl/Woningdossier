<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Models\ExampleBuildingContent
 *
 * @property int $id
 * @property int $example_building_id
 * @property int|null $build_year
 * @property array<array-key, mixed>|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExampleBuilding $exampleBuilding
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static \Database\Factories\ExampleBuildingContentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExampleBuildingContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExampleBuildingContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExampleBuildingContent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExampleBuildingContent whereBuildYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExampleBuildingContent whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExampleBuildingContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExampleBuildingContent whereExampleBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExampleBuildingContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExampleBuildingContent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExampleBuildingContent extends Model
{
    use HasFactory;

    public $fillable = [
        'build_year', 'content',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    public function exampleBuilding(): BelongsTo
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
