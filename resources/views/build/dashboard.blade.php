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
    <a href="#paginas">@include('build.icon', ['name' => 'document-text', 'class' => 'nav-ico']) Pagina's</a>
    <a href="#patronen">@include('build.icon', ['name' => 'squares-2x2', 'class' => 'nav-ico']) Patronen</a>
    <a href="#concerns">@include('build.icon', ['name' => 'flag', 'class' => 'nav-ico']) Concerns</a>
  </nav>

  @include('build.sections.overzicht')
  @include('build.sections.paginas')
  @include('build.sections.patronen')
  @include('build.sections.concerns')
</div>
</body>
</html>
