{{-- 503 — onderhoudsmodus. Bewust standalone: geen site-layout, geen @vite, geen DB,
     geen lang-files. Als de app plat ligt moet deze pagina nog renderen. Letterlijke
     tokenwaarden gekopieerd uit @theme in resources/css/app.css (de enige gedoogde plek). --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Even aan het sleutelen | Kidical Mass</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito-sans:400,700%7Ccaprasimo:400&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; box-sizing: border-box; padding: 1.5rem; background: #fff; color: #281a39; font-family: 'Nunito Sans', ui-sans-serif, system-ui, sans-serif; text-align: center; }
        main { max-width: 34rem; }
        img { height: 11rem; width: auto; }
        h1 { margin: 1.5rem 0 0.75rem; font-family: 'Caprasimo', 'Nunito Sans', sans-serif; font-weight: 400; font-size: 2rem; }
        p { margin: 0; font-size: 1.125rem; line-height: 1.6; color: rgba(40, 26, 57, 0.75); }
    </style>
</head>
<body data-error-page="503">
    <main>
        <img src="/img/illustrations/volunteer-with-wrench.svg" alt="" aria-hidden="true">
        <h1>We zijn even aan het sleutelen</h1>
        <p>De site is zo terug. Probeer het over een paar minuten opnieuw.</p>
    </main>
</body>
</html>
