<?php

namespace App\Models;

use App\Actions\GetGroupChangesAction;
use App\Actions\GroupChangesResult;
use App\Models\Concerns\HasMainImage;
use App\Models\Scopes\LocalGroupScope;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Unguarded]
#[ScopedBy([LocalGroupScope::class])]
class Group extends Model implements HasMedia
{
    use HasFactory;
    use HasMainImage;
    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
            'invisible' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Group::class, 'parent_id');
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_public', 'role')->withTimestamps();
    }

    /**
     * The members shown on public rosters (chapter + ride pages): everyone who
     * has not opted out via the `is_public` pivot flag. Filters the loaded
     * `users` collection in memory, so eager-loading `users` is enough — no
     * extra query. Use `users()` (unfiltered) for membership/auth checks.
     */
    public function getPublicMembersAttribute(): Collection
    {
        return $this->users->filter(fn (User $user): bool => (bool) $user->pivot->is_public)->values();
    }

    /**
     * The invisible country root (Belgium): articles attached to it are
     * national news rather than a chapter's.
     */
    public function isNationalRoot(): bool
    {
        return $this->invisible && $this->parent_id === null;
    }

    /**
     * Invisible groups (country/region nodes) have no public chapter page —
     * groups.show 404s them — so UI must never link to them.
     */
    public function hasPublicPage(): bool
    {
        return ! $this->invisible;
    }

    public function publicLabel(): string
    {
        return $this->isNationalRoot() ? __('about.news_national') : $this->name;
    }

    public function changes(?CarbonInterface $startDate = null, ?CarbonInterface $endDate = null): GroupChangesResult
    {
        return (new GetGroupChangesAction($this, $startDate, $endDate))->execute();
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
            });

        $this
            ->addMediaCollection('gallery')
            ->withResponsiveImages()
            ->registerMediaConversions(function (Media $media) {
                $this->registerMediaConversions($media);
            });
    }

    public function partners(): HasMany
    {
        return $this->hasMany(Partner::class);
    }

    public function pressArticles(): MorphToMany
    {
        return $this->morphToMany(PressArticle::class, 'press_articleable');
    }

    public function contactForms(): HasMany
    {
        return $this->hasMany(ContactForm::class);
    }

    public function scopeVisible(Builder $query): void
    {
        $query->where('invisible', false);
    }
}
