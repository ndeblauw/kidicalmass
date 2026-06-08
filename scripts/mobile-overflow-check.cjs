// scripts/mobile-overflow-check.cjs
// MANUAL dev tool — NOT part of `php artisan test`. Exit 1 if any route overflows at 390px.
//   node scripts/mobile-overflow-check.cjs                  # full 19-route sweep (pre-merge)
//   node scripts/mobile-overflow-check.cjs /nl/events       # just one route (fast, during iteration)
const path = require('path');
const { execSync } = require('child_process');
const globalRoot = execSync('npm root -g').toString().trim();
const { chromium } = require(path.join(globalRoot, 'playwright'));

const BASE = process.env.APP_URL || 'https://kidicalmass.test';
const VP = { width: 390, height: 844 };
const ALL = [
    '/nl', '/nl/events', '/nl/events/26', '/nl/chapters', '/nl/chapters/1',
    '/nl/getting-started', '/nl/steun-ons', '/nl/help-out', '/nl/find-a-bike',
    '/nl/about', '/nl/about/mission', '/nl/about/vision', '/nl/about/organisation',
    '/nl/about/partners', '/nl/about/press', '/nl/about/news', '/nl/about/news/1',
    '/nl/contact', '/nl/privacy',
];
const ROUTES = process.argv.slice(2).length ? process.argv.slice(2) : ALL;

(async () => {
    const browser = await chromium.launch();
    const offenders = [];
    for (const route of ROUTES) {
        const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: VP, deviceScaleFactor: 2, isMobile: true, hasTouch: true });
        const page = await ctx.newPage();
        await page.goto(BASE + route, { waitUntil: 'networkidle', timeout: 30000 });
        // Let Livewire/Alpine finish hydrating, then strip the dev-only Laravel
        // Debugbar (a fixed, full-width overlay that is absent in production and
        // otherwise produces flaky horizontal-overflow false positives).
        await page.waitForTimeout(400);
        const { scrollW, clientW } = await page.evaluate(() => {
            document.querySelector('.phpdebugbar')?.remove();
            return {
                scrollW: document.documentElement.scrollWidth,
                clientW: document.documentElement.clientWidth,
            };
        });
        const overflow = scrollW - clientW;
        if (overflow > 1) offenders.push({ route, overflow });
        console.log(`${overflow > 1 ? 'FAIL' : 'ok  '} ${route.padEnd(28)} overflow=${overflow}px`);
        await ctx.close();
    }
    await browser.close();
    if (offenders.length) {
        console.error(`\n${offenders.length} route(s) overflow at 390px.`);
        process.exit(1);
    }
    console.log('\nAll checked routes fit at 390px.');
})();
