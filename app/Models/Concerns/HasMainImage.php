<?php

namespace App\Models\Concerns;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasMainImage
{
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
