<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Kidical Mass — build status</title>
<style>
  :root{ --line:#e5e7eb; --mut:#6b7280; --faint:#9ca3af; --panel:#f9fafb;
         --draft:#d97706; --final:#16a34a; --drift:#dc2626; --accent:#2563eb; }
  *{box-sizing:border-box}
  body{margin:0;background:#fff;color:#111827;font:13px/1.5 ui-sans-serif,system-ui,sans-serif}
  code,.mono{font-family:ui-monospace,Menlo,monospace}
  a{color:var(--accent);text-decoration:none} a:hover{text-decoration:underline}
  .wrap{max-width:1180px;margin:0 auto;padding:20px}
  header.top{display:flex;justify-content:space-between;align-items:baseline;border-bottom:1px solid var(--line);padding-bottom:12px;margin-bottom:16px}
  header.top h1{font-size:1.375rem;margin:0} .top .meta{color:var(--faint);font-size:11px}
  nav.sections{position:sticky;top:0;background:#fff;border-bottom:1px solid var(--line);padding:10px 0;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:18px;font-size:12px;z-index:5}
  nav.sections a{display:inline-flex;align-items:center;gap:6px}
  .nav-ico{width:15px;height:15px;flex:0 0 auto}
  h2.sec{display:flex;align-items:center;gap:10px;font-size:1.25rem;line-height:1.2;font-weight:600;color:#111827;border-bottom:1px solid var(--line);padding-bottom:10px;margin:38px 0 16px}
  .sec-ico{width:22px;height:22px;color:var(--mut);flex:0 0 auto}
  .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:18px}
  .stat{position:relative;display:block;background:var(--panel);border:1px solid var(--line);border-radius:8px;padding:14px;color:inherit;text-decoration:none}
  a.stat:hover{border-color:var(--mut);text-decoration:none}
  .stat .n{font-size:22px;font-weight:600} .stat .l{color:var(--mut);font-size:11px;margin-top:2px}
  .stat-ico{position:absolute;top:12px;right:12px;width:20px;height:20px;color:var(--faint)}
  .drift{background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px;margin-bottom:20px}
  .drift h3{margin:0 0 6px;font-size:12px;color:#b91c1c;text-transform:uppercase}
  .drift ul{margin:0;padding-left:18px} .drift li{color:#b91c1c}
  .smap{background:var(--panel);border:1px solid var(--line);border-radius:8px;padding:8px 14px;overflow:auto;font-size:12px}
  .smap-root{color:var(--faint);font-size:11px;font-family:ui-monospace,Menlo,monospace;margin-bottom:8px}
  .smap-banner{margin:14px 0 6px;font-size:10px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--faint);border-top:1px solid var(--line);padding-top:10px}
  .smap-banner.first{border-top:0;padding-top:0;margin-top:2px}
  .smap-caption{color:var(--faint);font-size:11px;margin:-2px 0 6px;font-style:italic}
  .smap-row,.smap-item{position:relative;border-bottom:1px solid var(--line)}
  .smap-row:last-child,.smap-item:last-child{border-bottom:0}
  .smap-group>summary,.smap-row>.smap-sum{list-style:none;padding:6px 64px 6px 0;display:flex;align-items:baseline;gap:8px}
  .smap-group>summary{cursor:pointer}
  .smap-group>summary::-webkit-details-marker{display:none}
  .smap-group>summary::before{content:'▸';color:var(--faint);font-size:9px;line-height:1.7;transition:transform .12s ease;display:inline-block;flex:0 0 auto}
  .smap-group[open]>summary::before{transform:rotate(90deg)}
  .smap-row>.smap-sum::before{content:'·';color:var(--faint);flex:0 0 auto}
  .smap-group>summary:hover{background:#fff}
  .smap-label{font-weight:600;color:#111827}
  .smap-slug{font-family:ui-monospace,Menlo,monospace;color:var(--faint);font-size:11px}
  .smap-trail{color:var(--mut);font-size:11px}
  .smap-jump{position:absolute;right:4px;top:6px;font-family:ui-monospace,Menlo,monospace;font-size:10px;color:var(--accent);border:1px solid var(--line);border-radius:10px;padding:1px 7px;background:#fff;white-space:nowrap}
  .smap-jump:hover{border-color:var(--accent);text-decoration:none}
  .smap-body{margin:0 0 8px 12px;border-left:1px solid var(--line);padding:2px 0 2px 12px;display:grid;gap:1px}
  .smap-line{font-family:ui-monospace,Menlo,monospace;white-space:pre-wrap;font-size:11.5px;color:var(--mut);line-height:1.55}
  .smap-pre{color:var(--faint)}
  .smap-node{color:#111827;font-weight:500}
  .smap-node:hover{color:var(--accent)}
  table{width:100%;border-collapse:collapse;font-size:12px}
  th{text-align:left;color:var(--faint);font-weight:500;font-size:10px;text-transform:uppercase;padding:6px;border-bottom:1px solid var(--line)}
  td{padding:8px 6px;border-bottom:1px solid var(--line);vertical-align:top}
  tr:hover td{background:var(--panel)}
  .pid,.rid{font-family:ui-monospace,monospace;color:var(--faint)}
  .conf{font-weight:700}.c1{color:#dc2626}.c2{color:#d97706}.c3{color:#ca8a04}.c4{color:#16a34a}.c5{color:#15803d}
  .warn{color:var(--drift);font-weight:700;cursor:help}
  .row{display:grid;grid-template-columns:72px 1fr;gap:10px;padding:8px 6px;border-bottom:1px solid var(--line)}
  .badge{font-size:10px;padding:1px 6px;border-radius:10px;border:1px solid var(--line);color:var(--mut)}
  .tok abbr{cursor:help;border-bottom:1px dotted var(--faint);text-decoration:none}
  .gaps{color:var(--mut);font-size:11px}
  :target{outline:2px solid var(--accent);outline-offset:3px;border-radius:6px}
  .legend{color:var(--faint);font-size:11px;margin-top:8px}
  .freshness{display:flex;flex-wrap:wrap;gap:16px;margin:-4px 0 18px;font-size:11px;color:var(--faint)}
  .freshness .fr-file{font-family:ui-monospace,Menlo,monospace}
  .freshness .fr-age{color:var(--mut)}
  .stale-hint{background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;margin-bottom:12px;font-size:12px;color:#92400e}
  .stale-hint code{background:rgba(0,0,0,.05);padding:1px 4px;border-radius:4px;font-size:11px}
  .stale-hint a{color:#92400e;text-decoration:underline}
  .cgroup{display:flex;align-items:center;gap:8px;margin:22px 0 6px;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--mut);font-weight:600}
  .cgroup .cgcount{color:var(--faint)}
  .dot{display:inline-block;width:8px;height:8px;border-radius:50%}
  .dot-open{background:#dc2626}.dot-partly{background:#d97706}.dot-closed{background:#16a34a}
  .crow{display:grid;grid-template-columns:64px 1fr;gap:12px;padding:7px 6px;border-bottom:1px solid var(--line);align-items:baseline}
  .crow:hover{background:var(--panel)}
  .crow .cid{font-family:ui-monospace,Menlo,monospace;color:var(--faint);font-size:11px}
  .crow .ctitle{color:#111827}
  .pipe-legend{font-size:11.5px;color:var(--mut);margin:-4px 0 16px;line-height:1.7}
  .pipe-legend b{color:#111827;font-weight:600}
  th abbr{text-decoration:none;cursor:help}
  .sdot{display:inline-block;width:11px;height:11px;border-radius:50%;vertical-align:middle}
  .s-not-started{background:#dc2626}
  .s-in-progress{background:#d97706}
  .s-good{background:#16a34a}
  .s-nvt{background:#d1d5db}
  .s-to-decide{background:transparent;border:1.5px solid #9ca3af}
</style>
</head>
<body>
<div class="wrap">
  <header class="top">
    <h1>Kidical Mass — build status <span class="mono meta">/build</span></h1>
    <span class="meta">live · parsed from wiki · {{ $generatedAt }} · non-prod only</span>
  </header>

  @if (! empty($sources))
    <div class="freshness">
      <span>bron laatst bewerkt:</span>
      @foreach ($sources as $s)
        <span><span class="fr-file">{{ $s['file'] }}</span> <span class="fr-age">{{ $s['ago'] }}</span></span>
      @endforeach
    </div>
  @endif

  @if (! empty($warnings))
    <div class="drift"><h3>parse-warnings</h3><ul>@foreach ($warnings as $w)<li>{{ $w }}</li>@endforeach</ul></div>
  @endif

  <nav class="sections">
    <a href="#overzicht">@include('build.icon', ['name' => 'chart-bar', 'class' => 'nav-ico']) Overzicht</a>
    <a href="#structuur">@include('build.icon', ['name' => 'map', 'class' => 'nav-ico']) Structuur</a>
    <a href="#paginas">@include('build.icon', ['name' => 'document-text', 'class' => 'nav-ico']) Pagina's</a>
    <a href="#patronen">@include('build.icon', ['name' => 'squares-2x2', 'class' => 'nav-ico']) Patronen</a>
    <a href="#concerns">@include('build.icon', ['name' => 'flag', 'class' => 'nav-ico']) Concerns</a>
  </nav>

  @include('build.sections.overzicht')
  @include('build.sections.structuur')
  @include('build.sections.paginas')
  @include('build.sections.patronen')
  @include('build.sections.concerns')
</div>
</body>
</html>
