<?php

namespace App\Models;

use App\Models\Scopes\LocalGroupScope;
use Database\Factories\PressArticleFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[ScopedBy([LocalGroupScope::class])]
class PressArticle extends Model implements HasMedia
{
    /** @use HasFactory<PressArticleFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'fr' && filled($this->title_fr)
            ? (string) $this->title_fr
            : (string) $this->title_nl;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('document')
            ->singleFile();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function activities(): MorphToMany
    {
        return $this->morphedByMany(Activity::class, 'press_articleable');
    }

    public function articles(): MorphToMany
    {
        return $this->morphedByMany(Article::class, 'press_articleable');
    }

    public function groups(): MorphToMany
    {
        return $this->morphedByMany(Group::class, 'press_articleable');
    }
}
