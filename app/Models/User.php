<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable('name', 'email', 'password', 'superadmin', 'last_active_on')]
#[Hidden('password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token')]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'superadmin' => 'boolean',
            'last_active_on' => 'date',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)->withPivot('is_public', 'role')->withTimestamps();
    }

    public function isSuperAdmin(): bool
    {
        return $this->superadmin;
    }

    public function canAccessFilament(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isCaptain()) {
            return true;
        }

        return false;
    }

    public function isCaptain(): bool
    {
        return $this->groups()->wherePivot('role', 'captain')->exists();
    }

    public function isPinkVest(): bool
    {
        return $this->groups()->wherePivotIn('role', ['pinkvest', 'captain'])->exists();
    }

    public function isCaptainOf(Group $group): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->groups()
            ->whereKey($group)
            ->wherePivot('role', 'captain')
            ->exists();
    }

    public function isPinkVestOf(Group $group): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->groups()
            ->whereKey($group)
            ->wherePivotIn('role', ['pinkvest', 'captain'])
            ->exists();
    }
}
