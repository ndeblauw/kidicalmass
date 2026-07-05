<?php

namespace App\Models;

use App\Enums\PartnerCategory;
use App\Models\Scopes\LocalGroupScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Unguarded]
#[ScopedBy([LocalGroupScope::class])]
class Partner extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'show_logo' => 'boolean',
            'visible' => 'boolean',
            'category' => PartnerCategory::class,
        ];
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->format('png')
            ->width(150)
            ->height(150)
            ->sharpen(10);

        $this
            ->addMediaConversion('partner')
            ->format('png')
            ->height(80)
            ->sharpen(10);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->registerMediaConversions($media);
            });
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
