@php use App\Support\Build\BuildStatus; @endphp
<section id="patronen">
  <h2 class="sec">@include('build.icon', ['name' => 'squares-2x2', 'class' => 'sec-ico']) Patronen — shared library</h2>
  <p class="pipe-legend">Herbruikbare patronen (<b>PAT-</b>) — bron van waarheid voor velden &amp; gedrag. Status leeft per pagina (UI-kolom), niet per patroon.</p>
  @foreach ($patterns as $p)
    <div class="row" id="{{ $p['id'] }}">
      <div class="rid">{{ $p['id'] }}</div>
      <div>
        <b>{{ $p['name'] }}</b>
        @if (! empty($p['drift']))<span class="warn" title="{{ implode(' · ', $p['drift']) }}">⚠</span>@endif
        <br><span class="gaps">{{ BuildStatus::summarize($p['what'], 120) }}</span>
        <br><span class="gaps">used on: {!! BuildStatus::linkify(BuildStatus::plainify($p['usedOn']), $idMap) !!}</span>
      </div>
    </div>
  @endforeach
</section>
