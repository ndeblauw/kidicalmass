<?php

namespace App\Http\Controllers;

use App\Enums\PressType;
use App\Models\Press;
use Illuminate\View\View;

class PressController extends Controller
{
    public function index(): View
    {
        $query = Press::with('groups')
            ->where('visible', true)
            ->orderBy('publication_date', 'desc');

        if ($type = request('type')) {
            $query->where('media_type', $type);
        }

        $press = $query->paginate(12)->withQueryString();

        $types = PressType::getOptionsArray();

        return view('press.index', compact('press', 'types'));
    }
}
