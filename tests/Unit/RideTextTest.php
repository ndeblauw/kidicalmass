<?php

use App\Support\RideText;
use Tests\TestCase;

uses(TestCase::class);

it('splits the fixed text into trimmed items, dropping blank lines and list markers', function () {
    $text = "  - Eerste punt  \n\n• Tweede punt\r\n\u{00A0}\n* Derde punt";

    expect(RideText::goodToKnowItems($text))->toBe([
        ['label' => null, 'text' => 'Eerste punt'],
        ['label' => null, 'text' => 'Tweede punt'],
        ['label' => null, 'text' => 'Derde punt'],
    ]);
});

it('parses a leading label, including French spacing before the colon', function (string $line, string $label, string $text) {
    expect(RideText::goodToKnowItems($line))->toBe([['label' => $label, 'text' => $text]]);
})->with([
    'nl' => ['Helm: aangeraden, maar niet verplicht.', 'Helm', 'aangeraden, maar niet verplicht.'],
    'fr space' => ['Casque : conseillé', 'Casque', 'conseillé'],
    'fr nbsp' => ["Casque\u{00A0}: conseillé", 'Casque', 'conseillé'],
]);

it('leaves times and urls unlabelled', function (string $line) {
    expect(RideText::goodToKnowItems($line))->toBe([['label' => null, 'text' => $line]]);
})->with(['Start om 14:00 aan de kerk', 'Zie https://x.be']);

it('renders nothing for empty extra info', function (?string $text) {
    expect(RideText::renderExtraInfo($text))->toBeNull();
})->with([null, '', "  \n\t "]);

it('renders paragraphs, line breaks and external links', function () {
    $html = RideText::renderExtraInfo("Eerste regel\ntweede regel\n\nMeer op https://example.org")->toHtml();

    expect(substr_count($html, '<p>'))->toBe(2)
        ->and($html)->toContain('<br>')
        ->and($html)->toContain('href="https://example.org"')
        ->and($html)->toContain('target="_blank"')
        ->and($html)->toMatch('/rel="[^"]*noopener[^"]*"/')
        ->and($html)->toMatch('/rel="[^"]*noreferrer[^"]*"/');
});

it('strips raw html, unsafe links, headings and images', function (string $input, string $forbidden) {
    $html = RideText::renderExtraInfo($input)->toHtml();

    expect(strtolower($html))->not->toContain($forbidden);
})->with([
    'script' => ['Hallo <script>alert(1)</script>', '<script'],
    'inline html' => ['Hallo <b>vet</b>', '<b>'],
    'javascript link' => ['[klik](javascript:alert(1))', 'javascript:'],
    'heading' => ['# Titel', '<h1'],
    'image' => ['![alt](https://example.org/x.png)', '<img'],
]);
