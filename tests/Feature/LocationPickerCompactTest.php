<?php

use App\Livewire\LocationPicker;
use Livewire\Livewire;

test('picker gets the compact modifier only when compact is set', function () {
    Livewire::test(LocationPicker::class, ['compact' => true])
        ->assertSeeHtml('location-picker--compact');

    Livewire::test(LocationPicker::class)
        ->assertDontSeeHtml('location-picker--compact');
});
