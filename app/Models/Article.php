<?php

namespace App\Models;

use App\Models\Concerns\HasMainImage;
use App\Models\Scopes\LocalGroupScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Unguarded]
#[ScopedBy([LocalGroupScope::class])]
class Article extends Model implements HasMedia
{
    use HasFactory;
    use HasMainImage;
    use InteractsWithMedia;

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
            ->addMediaCollection('main')
            ->singleFile()
            ->withResponsiveImages()
            ->registerMediaConversions(function (Media $media) {
                $this->registerMediaConversions($media);
            });

        $this
            ->addMediaCollection('gallery')
            ->withResponsiveImages()
            ->registerMediaConversions(function (Media $media) {
                $this->registerMediaConversions($media);
            });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    public function pressArticles(): MorphToMany
    {
        return $this->morphToMany(PressArticle::class, 'press_articleable');
    }
}
