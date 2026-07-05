<?php

use App\Models\ContactForm;

it('prunes enquiries 12 months after handling and 24 months after receipt', function () {
    $handledOld = ContactForm::factory()->create(['handled_at' => now()->subMonths(13)]);
    $handledRecent = ContactForm::factory()->create(['handled_at' => now()->subMonths(2)]);
    $staleUnhandled = ContactForm::factory()->create(['created_at' => now()->subMonths(25)]);
    $freshUnhandled = ContactForm::factory()->create();

    $this->artisan('model:prune', ['--model' => ContactForm::class]);

    $remaining = ContactForm::withoutGlobalScopes()->pluck('id');

    expect($remaining)
        ->toContain($handledRecent->id)
        ->toContain($freshUnhandled->id)
        ->not->toContain($handledOld->id)
        ->not->toContain($staleUnhandled->id);
});
