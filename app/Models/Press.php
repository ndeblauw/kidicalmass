<?php

namespace App\Models;

use App\Enums\PressType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Press extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $guarded = [];

    protected $casts = [
        'publication_date' => 'date',
        'media_type' => PressType::class,
        'visible' => 'boolean',
        'highlighted' => 'boolean',
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);

        $this
            ->addMediaConversion('card')
            ->width(400)
            ->height(300)
            ->sharpen(10);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('attachment')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->registerMediaConversions($media);
            });
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }
}
