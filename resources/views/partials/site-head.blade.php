@php
    $siteName = 'Kidical Mass België';
    $fullTitle = ($title ?? null) ? $title.' · '.$siteName : __('meta.home_title');
    $metaDescription = $description ?? __('meta.default');
    $canonical = request()->url();
    $shareImage = $ogImage ?? asset('img/og-default.jpg');
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:title" content="{{ $title ?? $siteName }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:image" content="{{ $shareImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $title ?? $siteName }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="nl_BE">
<meta name="twitter:card" content="summary_large_image">

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#1d67cd">

<link rel="preconnect" href="https://fonts.bunny.net">
{{-- Bunny needs families pipe-separated in ONE family= param; the repeated &family=
     syntax silently keeps only the first (so Poppins never loaded before this fix).
     display=swap shows the fallback immediately instead of blocking on the webfont. --}}
<link href="https://fonts.bunny.net/css?family=nunito-sans:400,400i,700%7Cpoppins:800%7Ccaprasimo:400&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
