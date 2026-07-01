<?php

namespace App\Models;

use App\Enums\ActivityType;
use App\Enums\RideLifecycleState;
use App\Models\Concerns\HasMainImage;
use App\Models\Scopes\LocalGroupScope;
use App\Support\RideDate;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Builder;
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
class Activity extends Model implements HasMedia
{
    use HasFactory;
    use HasMainImage;
    use InteractsWithMedia;

    protected $attributes = [
        'is_published' => false,
    ];

    protected function casts(): array
    {
        return [
            'begin_date' => 'datetime',
            'activity_type' => ActivityType::class,
            'is_published' => 'boolean',
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

        $this->addMediaCollection('gpx')->singleFile();
    }

    public function getRouteCoordinatesAttribute(): array
    {
        $media = $this->getFirstMedia('gpx');
        if (! $media) {
            return [];
        }

        $xml = simplexml_load_file($media->getPath());
        if (! $xml) {
            return [];
        }

        $xml->registerXPathNamespace('gpx', 'http://www.topografix.com/GPX/1/1');
        $points = $xml->xpath('//gpx:trkpt') ?: $xml->xpath('//trkpt');

        return collect($points)->map(fn ($pt) => [
            (float) $pt['lat'],
            (float) $pt['lon'],
        ])->toArray();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    public function pressArticles(): MorphToMany
    {
        return $this->morphToMany(PressArticle::class, 'press_articleable');
    }

    public function getEffectiveOrganizerAttribute(): ?User
    {
        if ($this->organizer_id) {
            return $this->organizer;
        }

        $group = $this->groups()->first();

        if ($group) {
            return $group->users()->first();
        }

        return $this->author;
    }

    public function getEndDateAttribute(): mixed
    {
        if (! $this->duration_minutes) {
            return null;
        }

        return $this->begin_date->addMinutes($this->duration_minutes);
    }

    public function getDurationLabelAttribute(): ?string
    {
        if (! $this->duration_minutes) {
            return null;
        }

        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours === 0) {
            return "{$minutes} min";
        }

        if ($minutes === 0) {
            return "{$hours} u";
        }

        return "{$hours} u {$minutes} min";
    }

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'fr' && filled($this->title_fr)
            ? (string) $this->title_fr
            : (string) $this->title_nl;
    }

    /**
     * A "Grande" / "Grote Kidical Mass" — the special flagship edition that earns the
     * star marker on its calendar lockup. Matched on the Dutch title so it stays stable
     * regardless of the active locale.
     */
    public function isGrande(): bool
    {
        return str((string) $this->title_nl)->lower()->contains(['grande', 'grote kidical']);
    }

    public function getTimeLabelAttribute(): string
    {
        return RideDate::time($this->begin_date);
    }

    public function getDateShortAttribute(): string
    {
        return RideDate::short($this->begin_date);
    }

    public function getWeekdayLabelAttribute(): string
    {
        return RideDate::weekday($this->begin_date);
    }

    public function getDateFullAttribute(): string
    {
        return RideDate::full($this->begin_date);
    }

    public function getDateMonthYearAttribute(): string
    {
        return RideDate::monthYear($this->begin_date);
    }

    public function isPast(): bool
    {
        $end = $this->end_date;

        return $end !== null && $end->isPast();
    }

    public function hasEnded(): bool
    {
        $end = $this->end_date ?? $this->begin_date;

        return $end !== null && $end->isPast();
    }

    public function lifecycleState(): RideLifecycleState
    {
        if (! $this->hasEnded()) {
            return RideLifecycleState::Upcoming;
        }

        return $this->hasGallery()
            ? RideLifecycleState::Recap
            : RideLifecycleState::AwaitingPhotos;
    }

    public function isUpcoming(): bool
    {
        return $this->lifecycleState() === RideLifecycleState::Upcoming;
    }

    public function isAwaitingPhotos(): bool
    {
        return $this->lifecycleState() === RideLifecycleState::AwaitingPhotos;
    }

    public function isRecap(): bool
    {
        return $this->lifecycleState() === RideLifecycleState::Recap;
    }

    public function hasMainImage(): bool
    {
        return $this->getFirstMedia('main') !== null;
    }

    public function hasGallery(): bool
    {
        return $this->getMedia('gallery')->isNotEmpty();
    }

    public function hasPressCoverage(): bool
    {
        return $this->pressArticles()->exists();
    }

    public function hasRoute(): bool
    {
        return filled($this->commute_link)
            || filled($this->komoot_url)
            || $this->getFirstMedia('gpx') !== null;
    }

    public function missingFields(): array
    {
        $missing = [];

        if (! filled($this->title_nl)) {
            $missing[] = 'title_nl';
        }

        if (! filled($this->title_fr)) {
            $missing[] = 'title_fr';
        }

        if (! filled($this->content_nl) && ! filled($this->content_fr)) {
            $missing[] = 'content';
        }

        if (! $this->hasMainImage()) {
            $missing[] = 'main_image';
        }

        if (! $this->hasRoute()) {
            $missing[] = 'route';
        }

        if (! filled($this->location)) {
            $missing[] = 'location';
        }

        if (! $this->organizer_id) {
            $missing[] = 'organizer';
        }

        if ($this->begin_date === null) {
            $missing[] = 'begin_date';
        }

        return $missing;
    }

    public function isComplete(): bool
    {
        return empty($this->missingFields());
    }
}
