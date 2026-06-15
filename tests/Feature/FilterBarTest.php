<?php

test('filter bar renders its wrapper and its slot', function () {
    $view = $this->blade('<x-filter-bar><span>SLOTTED</span></x-filter-bar>');

    $view->assertSee('filter-bar', false); // wrapper class
    $view->assertSee('SLOTTED');           // slotted controls
});
