@php use App\Support\Build\BuildStatus; @endphp
<section id="paginas">
  <h2 class="sec">@include('build.icon', ['name' => 'document-text', 'class' => 'sec-ico']) Pagina's — pipeline</h2>

  <p class="pipe-legend">
    Werkstroom per pagina — kolommen
    <b>UX</b> (briefing) →
    <b>Conf</b> (kan ik de content schrijven? 1–5) →
    <b>Wire</b> (wireframe goed, geen stijl) →
    <b>Assets</b> (beelden/downloads binnen) →
    <b>UI</b> (visuele stijl) →
    <b>Back</b> (backend-bedrading) →
    <b>CMS</b> (CMS-content geladen &amp; nagekeken) →
    <b>OK</b> (klant). Bolletjeskleur = status:
    <span class="sdot s-not-started"></span> niet begonnen ·
    <span class="sdot s-in-progress"></span> bezig ·
    <span class="sdot s-good"></span> goed ·
    <span class="sdot s-nvt"></span> n.v.t. ·
    <span class="sdot s-to-decide"></span> te beslissen ·
    <span class="warn">⚠</span> drift t.o.v. code.
  </p>

  <table>
    <thead><tr>
      <th>ID</th><th>Pagina</th><th>Type</th>
      <th><abbr title="UX-briefing — strategie + scope + structuur + skelet uitgewerkt">UX</abbr></th>
      <th><abbr title="Content-readiness — kan ik de échte paginatekst schrijven volgens de briefing? (1–5)">Conf</abbr></th>
      <th><abbr title="Wireframe — juiste content, duidelijke hiërarchie, goed ontworpen; nog zonder visuele stijl">Wire</abbr></th>
      <th><abbr title="Assets — alle juiste beelden + downloads voor de pagina binnen">Assets</abbr></th>
      <th><abbr title="UI — visuele stijl toegepast + gecheckt">UI</abbr></th>
      <th><abbr title="Backend — data/CMS-bedrading; of bewust beslist dat het niet nodig is">Back</abbr></th>
      <th><abbr title="CMS-content — de content in het CMS is geladen en klopt (⚪ = pagina heeft geen CMS-content; 🟠 = team moet nakijken of aanvullen)">CMS</abbr></th>
      <th><abbr title="Goedgekeurd door de klant">OK</abbr></th>
      <th>Top gaps</th>
    </tr></thead>
    <tbody>
      @foreach ($pages as $p)
        <tr id="{{ $p['id'] }}">
          <td class="pid">{{ $p['id'] }}</td>
          <td><b>{{ $p['name'] }}</b><br><span class="mono" style="color:var(--faint);font-size:11px">{{ $p['slug'] }}</span></td>
          <td>{{ \Illuminate\Support\Str::before($p['type'], ' ') }}</td>
          <td title="UX: {{ $p['stages']['ux']->label() }}"><span class="sdot s-{{ $p['stages']['ux']->value }}"></span></td>
          <td class="conf c{{ $p['confidence'] }}">{{ $p['confidence'] ?: '—' }}</td>
          @foreach (['wireframe' => 'Wire', 'assets' => 'Assets', 'ui' => 'UI', 'back' => 'Back', 'cms' => 'CMS', 'ok' => 'OK'] as $stage => $lbl)
            <td title="{{ $lbl }}: {{ $p['stages'][$stage]->label() }}"><span class="sdot s-{{ $p['stages'][$stage]->value }}"></span></td>
          @endforeach
          <td class="gaps">
            @if (! empty($p['drift']))<span class="warn" title="{{ implode(' · ', $p['drift']) }}">⚠</span> @endif
            {!! BuildStatus::linkify(BuildStatus::plainify($p['gaps']), $idMap) !!}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</section>
