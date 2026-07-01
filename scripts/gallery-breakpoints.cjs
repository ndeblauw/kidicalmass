/**
 * Capture the chapter gallery ("In beeld") across the sm/md range and report
 * any element that overflows the viewport horizontally.
 *
 *   node scripts/gallery-breakpoints.cjs /nl/chapters/<slug>
 */
const path = require('path');
const { execSync } = require('child_process');
const globalRoot = execSync('npm root -g').toString().trim();
const { chromium } = require(path.join(globalRoot, 'playwright'));

const BASE = process.env.APP_URL || 'https://kidicalmass.test';
const target = process.argv[2] || '/nl/chapters';
const widths = process.argv[3] ? process.argv[3].split(',').map(Number) : [640, 768, 900, 1024, 1180];

(async () => {
    const browser = await chromium.launch();
    for (const w of widths) {
        const context = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: w, height: 1000 }, deviceScaleFactor: 2 });
        const page = await context.newPage();
        await page.goto(BASE + target, { waitUntil: 'networkidle' });

        // Report horizontal overflow culprits within the gallery section.
        const overflow = await page.evaluate(() => {
            const docW = document.documentElement.clientWidth;
            const offenders = [];
            document.querySelectorAll('.chapter-gallery, .chapter-gallery *').forEach((el) => {
                const r = el.getBoundingClientRect();
                if (r.right > docW + 1 || r.left < -1) {
                    offenders.push({ cls: el.className.toString().slice(0, 60), left: Math.round(r.left), right: Math.round(r.right) });
                }
            });
            return { docW, scrollW: document.documentElement.scrollWidth, offenders: offenders.slice(0, 8) };
        });
        console.log(`\n=== ${w}px ===  docW=${overflow.docW} scrollW=${overflow.scrollW} ${overflow.scrollW > overflow.docW ? '⚠️ OVERFLOW' : 'ok'}`);
        overflow.offenders.forEach((o) => console.log(`   ${o.left}..${o.right}  .${o.cls}`));

        const gallery = await page.$('.chapter-gallery');
        const out = `/tmp/gallery-${w}.png`;
        if (gallery) {
            await gallery.scrollIntoViewIfNeeded();
            await gallery.screenshot({ path: out });
        } else {
            await page.screenshot({ path: out, fullPage: true });
        }
        console.log(`   -> ${out}`);
        await context.close();
    }
    await browser.close();
})();
