<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * A member of the national coordination team, shown on "Hoe we werken".
 * Curated in the admin; photo and bios arrive from the duo themselves.
 */
#[Unguarded]
class TeamMember extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
            'visible' => 'boolean',
        ];
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('photo')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->registerMediaConversions($media);
            });
    }
}
