<?php

namespace App\Models;

use App\Models\Concerns\HasMainImage;
use App\Models\Scopes\LocalGroupScope;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
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

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    #[Scope]
    protected function drafts(Builder $query): void
    {
        $query->where('is_published', false);
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * Body HTML for the public page: rich-text (TinyMCE) content renders as-is,
     * legacy plain-text content keeps its escaped nl2br rendering.
     */
    protected function getContentHtmlAttribute(): HtmlString
    {
        $content = (string) $this->content_nl;

        return str_contains($content, '<p')
            ? new HtmlString($content)
            : new HtmlString(nl2br(e($content)));
    }

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

                $this->addMediaConversion('og')
                    ->fit(Fit::Crop, 1200, 630)
                    ->format('jpg');
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

    public function metaDescription(): string
    {
        return Str::limit(Str::squish(strip_tags($this->content_nl ?? '')), 155);
    }

    public function ogImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('main', 'og') ?: null;
    }
}
