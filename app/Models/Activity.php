<?php

namespace App\Models;

use App\Enums\ActivityType;
use App\Support\RideDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Activity extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'begin_date' => 'datetime',
            'activity_type' => ActivityType::class,
        ];
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
            ->registerMediaConversions(function (Media $media) {
                $this->registerMediaConversions($media);
            });

        $this
            ->addMediaCollection('gallery')
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

    public function getTimeLabelAttribute(): string
    {
        return RideDate::time($this->begin_date);
    }

    public function getDateShortAttribute(): string
    {
        return RideDate::short($this->begin_date);
    }

    public function getDateFullAttribute(): string
    {
        return RideDate::full($this->begin_date);
    }

    public function getDateMonthYearAttribute(): string
    {
        return RideDate::monthYear($this->begin_date);
    }
}
