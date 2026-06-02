@php
    use App\Support\Build\BuildStatus;
    use Illuminate\Support\Str;

    // slug → page ID, so every sitemap node that names a route links to its pipeline row.
    $slugToId = collect($pages)->pluck('id', 'slug');
    $slugOf = fn (string $text): ?string => preg_match('/\((\/[^\s)]*)/', $text, $m) ? $m[1] : null;

    // Render one descendant line: tree-art prefix (muted) + content. If the node
    // names a known route, link the content to its #P-nn row; else linkify D-/PAT- tokens.
    $renderLine = function (string $line) use ($slugToId, $slugOf, $idMap): string {
        $pos = mb_strpos($line, '── ');
        if ($pos !== false) {
            $prefix = mb_substr($line, 0, $pos + 3);
            $rest = mb_substr($line, $pos + 3);
        } else {
            $prefix = '';
            $rest = $line;
        }
        $pre = '<span class="smap-pre">'.e($prefix).'</span>';
        $slug = $slugOf($rest);
        $pid = $slug !== null ? ($slugToId[$slug] ?? null) : null;

        return $pid
            ? $pre.'<a href="#'.e($pid).'" class="smap-node">'.e($rest).'</a>'
            : $pre.BuildStatus::linkify($rest, $idMap);
    };

    // Group the flat sitemap tree into top-level branches (collapsible) + banners/captions.
    $items = [];
    $open = null;
    foreach (preg_split('/\R/u', (string) $sitemap) as $i => $raw) {
        $line = rtrim($raw);
        $trim = trim($line);
        if ($trim === '' || $trim === '│') {
            continue; // vertical spacing rail
        }
        if (str_contains($line, '══')) {
            $items[] = ['kind' => 'banner', 'label' => trim(preg_replace('/═+/u', '', $trim))];
            $open = null;

            continue;
        }
        if (preg_match('/^[├└]──\s+(.*)$/u', $line, $m)) {
            $body = $m[1];
            $slug = $slugOf($body);
            $label = $body;
            $trail = '';
            if ($slug !== null && preg_match('/^(?<label>.*?)\s*\(\/[^)]*\)\s*(?<trail>.*)$/u', $body, $mm)) {
                $label = trim($mm['label']);
                $trail = trim($mm['trail']);
            }
            $items[] = [
                'kind' => 'branch',
                'pid' => $slug !== null ? ($slugToId[$slug] ?? null) : null,
                'slug' => $slug,
                'label' => $label,
                'trail' => $trail,
                'children' => [],
            ];
            $open = array_key_last($items);

            continue;
        }
        if ($open !== null) {
            $items[$open]['children'][] = $line;
        } elseif ($i === 0) {
            $items[] = ['kind' => 'root', 'text' => $trim];
        } else {
            $items[] = ['kind' => 'caption', 'text' => ltrim($trim, '│ ')];
        }
    }
    $firstBanner = true;
@endphp
<section id="structuur">
  <h2 class="sec">@include('build.icon', ['name' => 'map', 'class' => 'sec-ico']) Structuur — sitemap</h2>
  @if ($structureStale)
    <div class="stale-hint">
      ⓘ <code>20-structure.md</code> ({{ $structureStale['structureDate'] }}) is ouder dan een recent
      bewerkte pagina (<a href="#{{ $structureStale['pageId'] }}">{{ $structureStale['page'] }}</a>,
      {{ $structureStale['pageDate'] }}). De sitemap-omschrijvingen kunnen achterlopen — controleer of ze nog kloppen.
    </div>
  @endif
  @if (! $sitemap)
    <p class="legend">Sitemap-blok niet gevonden in 20-structure.md.</p>
  @else
    <p class="pipe-legend">Pagina's per niveau — klik <b>▸</b> om de details uit te klappen, of de <b>P-nn</b>-chip om naar de pipeline-rij te springen.</p>
    <div class="smap-actions">
      <button type="button" class="smap-toggle" data-smap="open">alles uitklappen</button>
      <button type="button" class="smap-toggle" data-smap="close">inklappen</button>
    </div>
    <div class="smap" id="smap">
      @foreach ($items as $it)
        @switch($it['kind'])
          @case('root')
            <div class="smap-root">{{ $it['text'] }}</div>
            @break
          @case('banner')
            <div class="smap-banner {{ $firstBanner ? 'first' : '' }}">{{ Str::lower($it['label']) }}</div>
            @php $firstBanner = false; @endphp
            @break
          @case('caption')
            <div class="smap-caption">{!! BuildStatus::linkify($it['text'], $idMap) !!}</div>
            @break
          @case('branch')
            @php
              $summary = '<span class="smap-label">'.e($it['label']).'</span>'
                .($it['slug'] ? ' <span class="smap-slug">'.e($it['slug']).'</span>' : '')
                .($it['trail'] !== '' ? ' <span class="smap-trail">'.BuildStatus::linkify($it['trail'], $idMap).'</span>' : '');
              $jump = $it['pid'] ? '<a class="smap-jump" href="#'.e($it['pid']).'">'.e($it['pid']).' ↗</a>' : '';
            @endphp
            @if (empty($it['children']))
              <div class="smap-row"><div class="smap-sum">{!! $summary !!}</div>{!! $jump !!}</div>
            @else
              <div class="smap-item">
                <details class="smap-group">
                  <summary>{!! $summary !!}</summary>
                  <div class="smap-body">
                    @foreach ($it['children'] as $child)
                      <div class="smap-line">{!! $renderLine($child) !!}</div>
                    @endforeach
                  </div>
                </details>
                {!! $jump !!}
              </div>
            @endif
            @break
        @endswitch
      @endforeach
    </div>
    <script>
      document.querySelectorAll('.smap-toggle').forEach(function (b) {
        b.addEventListener('click', function () {
          var open = b.dataset.smap === 'open';
          document.querySelectorAll('#smap details.smap-group').forEach(function (d) { d.open = open; });
        });
      });
    </script>
  @endif
</section>
