<?php

// <x-belgium-map> fed by LocalGroupsMap: one dot per visible non-Brussels group,
// one counted Brussels bubble, and a picture that follows every change to the
// groups on the next render (no regeneration step, no cache to clear).

use App\Models\Group;
use App\Models\PostalCode;
use App\Support\Map\BelgiumMap;
use App\Support\Map\LocalGroupsMap;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    app()->setLocale('nl');

    $belgium = Group::factory()->create(['name_nl' => 'Belgium', 'invisible' => true]);
    $region = fn (string $name) => Group::factory()->withParent($belgium)->create(['name_nl' => $name, 'invisible' => true]);
    $this->brussels = $region('Brussels Capital Region');
    $this->flanders = $region('Flanders');
    $this->wallonia = $region('Wallonia');

    foreach ([
        ['1000', 'Brussel', 50.8504, 4.3488],
        ['1050', 'Elsene', 50.8333, 4.3667],
        ['9000', 'Gent', 51.05, 3.7167],
        ['5000', 'Namen', 50.4686, 4.9117],
        ['3000', 'Leuven', 50.8796, 4.7009],
    ] as [$zip, $name, $lat, $lng]) {
        PostalCode::create(['zip' => $zip, 'name' => $name, 'latitude' => $lat, 'longitude' => $lng]);
    }

    $group = fn (Group $parent, string $name, ?string $zip, bool $invisible = false) => Group::factory()->withParent($parent)
        ->create(['name_nl' => $name, 'name_fr' => $name, 'zip' => $zip, 'invisible' => $invisible]);

    $group($this->brussels, 'Brussel Stad', '1000');
    $group($this->brussels, 'Elsene', '1050');
    $this->gent = $group($this->flanders, 'Gent', '9000');
    $this->namen = $group($this->wallonia, 'Namen', '5000');
    $group($this->flanders, 'Zonder postcode', null);
    $group($this->flanders, 'Verborgen', '3000', invisible: true);
});

function renderBelgiumMap(): string
{
    return Blade::render('<x-belgium-map :map="$map" />', ['map' => app(LocalGroupsMap::class)->data()]);
}

/**
 * The drawn dots, read from the markup by their data hook (attribute order and
 * whitespace do not matter).
 *
 * @return list<array{region: string, cx: string, cy: string}>
 */
function mapDots(string $html): array
{
    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="utf-8">'.$html);

    $dots = [];
    foreach ((new DOMXPath($document))->query('//*[@data-marker]') as $dot) {
        $dots[] = ['region' => $dot->getAttribute('data-region'), 'cx' => $dot->getAttribute('cx'), 'cy' => $dot->getAttribute('cy')];
    }

    return $dots;
}

function projected(float $lng, float $lat): array
{
    $point = BelgiumMap::fromFile(database_path(LocalGroupsMap::OUTLINE_PATH))->project($lng, $lat);

    return ['cx' => (string) $point['x'], 'cy' => (string) $point['y']];
}

it('draws a dot per visible non-Brussels group with a centroid and one bubble counting the Brussels groups', function () {
    $html = renderBelgiumMap();

    // The accessible name describes what is drawn: 'Zonder postcode' has no dot, so it is not counted.
    expect($html)->toContain('data-belgium-map')
        ->toContain('role="img"')
        ->toContain(__('components.belgium_map.label', ['parts' => '']))
        ->toContain(trans_choice('components.belgium_map.first', 2, ['count' => 2, 'where' => __('components.belgium_map.where.brussels')]))
        ->toContain(__('components.belgium_map.next', ['count' => 1, 'where' => __('components.belgium_map.where.flanders')]))
        ->and(mapDots($html))->toHaveCount(2)
        ->and(collect(mapDots($html))->pluck('region')->sort()->values()->all())->toBe(['flanders', 'wallonia'])
        ->and(mapDots($html))->toContain(['region' => 'flanders', ...projected(3.7167, 51.05)]);

    preg_match('/data-region="brussels" data-count="(\d+)"/', $html, $bubble);
    expect($bubble[1] ?? null)->toBe('2');
});

it('reflects added, hidden and moved groups on the next render', function () {
    $before = renderBelgiumMap();
    expect(renderBelgiumMap())->toBe($before)->toContain('data-count="2"'); // stable when nothing changed

    // Added.
    Group::factory()->withParent($this->brussels)->create(['name_nl' => 'Ukkel', 'zip' => '1000', 'invisible' => false]);
    expect(renderBelgiumMap())->toContain('data-count="3"');

    // Hidden (through the query builder, bypassing model events and timestamps).
    DB::table('groups')->where('id', $this->namen->id)->update(['invisible' => true]);
    expect(collect(mapDots(renderBelgiumMap()))->pluck('region')->all())->toBe(['flanders']);

    // Moved to another postcode.
    $this->gent->update(['zip' => '3000']);
    expect(mapDots(renderBelgiumMap()))->toBe([['region' => 'flanders', ...projected(4.7009, 50.8796)]]);
});
