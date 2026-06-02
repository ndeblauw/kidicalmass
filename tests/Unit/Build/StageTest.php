<?php

use App\Support\Build\Stage;

it('maps status emoji to a stage', function () {
    expect(Stage::fromEmoji('🟠 bezig'))->toBe(Stage::InProgress)
        ->and(Stage::fromEmoji('🔴 niet begonnen'))->toBe(Stage::NotStarted)
        ->and(Stage::fromEmoji('🟢'))->toBe(Stage::Good)
        ->and(Stage::fromEmoji('⚪ n.v.t.'))->toBe(Stage::NotApplicable)
        ->and(Stage::fromEmoji('❓'))->toBe(Stage::ToDecide)
        ->and(Stage::fromEmoji('—'))->toBe(Stage::NotStarted);
});

it('exposes emoji and label', function () {
    expect(Stage::Good->emoji())->toBe('🟢')
        ->and(Stage::InProgress->label())->toBe('bezig');
});
