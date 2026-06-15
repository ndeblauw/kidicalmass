<?php

namespace App\Livewire;

use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class LocationPicker extends Component
{
    public string $query = '';

    public bool $editing = false;

    public bool $compact = false;

    /**
     * @return Collection<int, PostalCode>
     */
    public function suggestions(): Collection
    {
        $term = trim($this->query);

        if (mb_strlen($term) < 2) {
            return new Collection;
        }

        return PostalCode::query()
            ->where('zip', 'like', $term.'%')
            ->orWhere('name', 'like', $term.'%')
            ->orderBy('zip')
            ->limit(8)
            ->get();
    }

    public function choose(string $zip): void
    {
        $row = PostalCode::where('zip', $zip)->first();

        if (! $row) {
            return;
        }

        $this->persist($zip, $row->latitude, $row->longitude, $row->name);
    }

    public function setFromCoords(float $lat, float $lng): void
    {
        $nearest = PostalCode::nearestTo($lat, $lng);

        if (! $nearest) {
            return;
        }

        $this->persist($nearest->zip, $nearest->latitude, $nearest->longitude, $nearest->name);
    }

    public function clear(): void
    {
        Cookie::queue(Cookie::forget(config('location.cookie')));
        $this->redirect($this->currentUrl());
    }

    protected function persist(string $zip, float $lat, float $lng, string $name): void
    {
        Cookie::queue(
            config('location.cookie'),
            json_encode(['zip' => $zip, 'lat' => $lat, 'lng' => $lng, 'name' => $name]),
            config('location.cookie_days') * 24 * 60,
        );

        $this->redirect($this->currentUrl());
    }

    protected function currentUrl(): string
    {
        return url()->previous() ?: url('/');
    }

    public function render()
    {
        return view('livewire.location-picker', [
            'current' => CurrentLocation::resolve(),
            'suggestions' => $this->suggestions(),
        ]);
    }
}
