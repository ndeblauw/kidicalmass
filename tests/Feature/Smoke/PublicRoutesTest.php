<?php

use function Pest\Laravel\get;

it('serves every public route with 200', function (string $path) {
    get($path)->assertOk();
})->with('public routes');
