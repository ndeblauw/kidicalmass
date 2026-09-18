<?php

namespace App\Enums;

enum ActivityType: string
{
    case KIDICALMASS = 'kidicalmass';
    case MEETING = 'meeting';
    case WORKSHOP = 'workshop';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::KIDICALMASS => 'Kidicalmass',
            self::MEETING => 'Meeting',
            self::WORKSHOP => 'Workshop',
            self::OTHER => 'Other',
        };
    }

    /**
     * Whether this activity is a family bike parade. Drives the activity-detail
     * split: rides get the full ride layout (route map, pace promises, pink-vest
     * ask); every other type gets the lighter "basic activity" page.
     */
    public function isRide(): bool
    {
        return $this === self::KIDICALMASS;
    }

    /**
     * Public-site label for an activity type in the active locale. `label()`
     * stays English for Filament/admin and form option arrays; this mirrors
     * the NL fallbacks in <x-ride-row> and serves the FR site too.
     */
    public function labelNl(): string
    {
        return match ($this) {
            self::KIDICALMASS => 'Fietsparade',
            self::MEETING => 'Vergadering',
            self::WORKSHOP => 'Workshop',
            self::OTHER => 'Activiteit',
        };
    }

    public function labelLocalized(): string
    {
        return match ($this) {
            self::KIDICALMASS => __('activities.types.ride'),
            self::MEETING => __('activities.types.meeting'),
            self::WORKSHOP => __('activities.types.workshop'),
            self::OTHER => __('activities.types.other'),
        };
    }

    /**
     * Accent colour for the calendar lockup (<x-ride-day>): rides read red,
     * workshops green, meetings blue, anything else orange. Returns a CSS
     * custom-property reference so it can be dropped straight into an inline
     * `--ride-accent` declaration.
     */
    public function accentColor(): string
    {
        return match ($this) {
            self::WORKSHOP => 'var(--color-kidical-green)',
            self::MEETING => 'var(--color-kidical-blue)',
            self::OTHER => 'var(--color-kidical-orange)',
            self::KIDICALMASS => 'var(--color-kidical-red)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::KIDICALMASS => 'green',
            self::MEETING => 'blue',
            self::WORKSHOP => 'yellow',
            self::OTHER => 'gray',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::KIDICALMASS => 'bg-green-100 text-green-800',
            self::MEETING => 'bg-blue-100 text-blue-800',
            self::WORKSHOP => 'bg-yellow-100 text-yellow-800',
            self::OTHER => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get an array of options for use in forms and filters
     */
    public static function getOptionsArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->toArray();
    }
}
