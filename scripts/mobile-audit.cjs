/**
 * Mobile audit: full-page screenshots of every public page at iPhone-ish width.
 * Run: node scripts/mobile-audit.cjs
 * Output: /tmp/mobile-audit/<slug>.png  (+ a nav-open capture for home)
 */
const path = require('path');
const fs = require('fs');
const { execSync } = require('child_process');

const globalRoot = execSync('npm root -g').toString().trim();
const { chromium } = require(path.join(globalRoot, 'playwright'));

const BASE = process.env.APP_URL || 'https://kidicalmass.test';
const OUT = '/tmp/mobile-audit';
const VP = { width: 390, height: 844 };

const PAGES = [
    ['/nl', 'home'],
    ['/nl/events', 'events-index'],
    ['/nl/events/26', 'event-detail'],
    ['/nl/chapters', 'chapters-index'],
    ['/nl/chapters/1', 'chapter-detail'],
    ['/nl/getting-started', 'getting-started'],
    ['/nl/about', 'about'],
    ['/nl/about/mission', 'about-mission'],
    ['/nl/about/vision', 'about-vision'],
    ['/nl/about/organisation', 'about-organisation'],
    ['/nl/about/partners', 'about-partners'],
    ['/nl/about/press', 'about-press'],
    ['/nl/about/news', 'news-index'],
    ['/nl/about/news/1', 'news-detail'],
    ['/nl/steun-ons', 'steun-ons'],
    ['/nl/help-out', 'help-out'],
    ['/nl/find-a-bike', 'find-a-bike'],
    ['/nl/contact', 'contact'],
    ['/nl/privacy', 'privacy'],
];

(async () => {
    fs.mkdirSync(OUT, { recursive: true });
    const browser = await chromium.launch();
    const results = [];
    for (const [url, slug] of PAGES) {
        const context = await browser.newContext({
            ignoreHTTPSErrors: true,
            viewport: VP,
            deviceScaleFactor: 2,
            isMobile: true,
            hasTouch: true,
        });
        const page = await context.newPage();
        const errors = [];
        page.on('console', (m) => { if (m.type() === 'error') errors.push(m.text()); });
        page.on('pageerror', (e) => errors.push(String(e)));
        try {
            const resp = await page.goto(BASE + url, { waitUntil: 'networkidle', timeout: 30000 });
            // Measure horizontal overflow (a key mobile bug signal).
            const overflow = await page.evaluate(() => {
                const de = document.documentElement;
                return { scrollW: de.scrollWidth, clientW: de.clientWidth };
            });
            const out = `${OUT}/${slug}.png`;
            await page.screenshot({ path: out, fullPage: true });
            results.push({ slug, status: resp && resp.status(), overflowPx: overflow.scrollW - overflow.clientW, errors: errors.slice(0, 3) });
            console.log(`OK  ${slug.padEnd(20)} status=${resp && resp.status()} overflow=${overflow.scrollW - overflow.clientW}px errs=${errors.length}`);
        } catch (e) {
            results.push({ slug, error: String(e) });
            console.log(`ERR ${slug.padEnd(20)} ${e}`);
        }
        await context.close();
    }
    fs.writeFileSync(`${OUT}/_report.json`, JSON.stringify(results, null, 2));
    console.log('\nReport: ' + OUT + '/_report.json');
    await browser.close();
})();
