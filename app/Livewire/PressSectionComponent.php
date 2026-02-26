<?php

namespace App\Livewire;

use App\Models\Press;
use Illuminate\View\View;
use Livewire\Component;

class PressSectionComponent extends Component
{
    public function render(): View
    {
        $items = Press::where('visible', true)
            ->orderByDesc('highlighted')
            ->orderByDesc('publication_date')
            ->limit(3)
            ->get();

        return view('livewire.press-section-component', compact('items'));
    }
}
