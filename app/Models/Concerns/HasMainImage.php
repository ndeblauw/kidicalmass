<?php

namespace App\Models\Concerns;

use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasMainImage
{
    public function gallery(): Collection
    {
        return $this->getMedia('gallery');
    }

    public function mainImage(): ?Media
    {
        return $this->getFirstMedia('main');
    }

    public function mainImageUrl(string $conversion = ''): ?string
    {
        $url = $this->getFirstMediaUrl('main', $conversion);

        if ($url) {
            return $url;
        }

        $slug = config('kidicalmass.default_images.'.class_basename($this));

        if (! $slug) {
            return null;
        }

        $path = "img/defaults/{$slug}";

        if ($conversion !== '') {
            $path .= '-'.$conversion;
        }

        return asset($path.'.jpg');
    }
}
