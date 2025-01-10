<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

/**
 * App\Models\Media
 *
 * @property int $id
 * @property string $disk
 * @property string $directory
 * @property string $filename
 * @property string $extension
 * @property string $mime_type
 * @property string $aggregate_type
 * @property int $size
 * @property string|null $variant_name
 * @property int|null $original_media_id
 * @property int|null $input_source_id
 * @property array<array-key, mixed>|null $custom_properties
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $alt
 * @property-read string $basename
 * @property-read string $url
 * @property-read \App\Models\InputSource|null $inputSource
 * @property-read Media|null $originalMedia
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Media> $variants
 * @property-read int|null $variants_count
 * @method static Builder<static>|Media forPathOnDisk(string $disk, string $path)
 * @method static Builder<static>|Media inDirectory(string $disk, string $directory, bool $recursive = false)
 * @method static Builder<static>|Media inOrUnderDirectory(string $disk, string $directory)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media query()
 * @method static Builder<static>|Media unordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereAggregateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereAlt($value)
 * @method static Builder<static>|Media whereBasename(string $basename)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCustomProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDirectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereInputSourceId($value)
 * @method static Builder<static>|Media whereIsOriginal()
 * @method static Builder<static>|Media whereIsVariant(?string $variant_name = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereOriginalMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereVariantName($value)
 * @mixin \Eloquent
 */
class Media extends \Plank\Mediable\Media
{
    protected $casts = [
        'size' => 'int',
        'custom_properties' => 'array',
    ];

    # Model methods
    public function getUrl(): string
    {
        $url = parent::getUrl();

        // Do some magic to ensure the correct subdomain is used within the host
        $mediaUrlHost = Request::create($url)->getSchemeAndHttpHost();
        $currentUrlHost = Request::getSchemeAndHttpHost();

        if ($mediaUrlHost !== $currentUrlHost) {
            $url = str_replace($mediaUrlHost, $currentUrlHost, $url);
        }

        return $url;
    }

    public function getPath(): string
    {
        return parse_url($this->getUrl())['path'] ?? '';
    }

    # Relations
    public function inputSource(): BelongsTo
    {
        return $this->belongsTo(InputSource::class);
    }
}
