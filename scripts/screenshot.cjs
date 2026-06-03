/**
 * Screenshot helper for the public site (Laravel Herd, HTTPS self-signed).
 *
 * Playwright is installed GLOBALLY (`npm install -g playwright`), so a bare
 * `require('playwright')` from the project cwd fails to resolve it. This helper
 * resolves it from the global root explicitly, so it works with a plain:
 *
 *   node scripts/screenshot.cjs                         # both index pages, desktop + mobile → /tmp
 *   node scripts/screenshot.cjs /nl/events out.png      # one page, desktop → out.png
 *   node scripts/screenshot.cjs /nl/events out.png mobile
 *
 * Project is ESM ("type":"module"), so this MUST stay a .cjs file.
 */
const path = require('path');
const { execSync } = require('child_process');

const globalRoot = execSync('npm root -g').toString().trim();
const { chromium } = require(path.join(globalRoot, 'playwright'));

const BASE = process.env.APP_URL || 'https://kidicalmass.test';
const VIEWPORTS = {
    desktop: { width: 1440, height: 900 },
    mobile: { width: 390, height: 844 },
};

const [, , argPath, argOut, argViewport] = process.argv;

const jobs = argPath
    ? [{ url: BASE + argPath, out: argOut || '/tmp/screenshot.png', vp: argViewport || 'desktop' }]
    : ['/nl/events', '/nl/chapters'].flatMap((p) =>
          Object.keys(VIEWPORTS).map((vp) => ({
              url: BASE + p,
              out: `/tmp/${p.replace(/\//g, '-').replace(/^-/, '')}-${vp}.png`,
              vp,
          })),
      );

(async () => {
    const browser = await chromium.launch();
    for (const job of jobs) {
        const context = await browser.newContext({
            ignoreHTTPSErrors: true,
            viewport: VIEWPORTS[job.vp],
            deviceScaleFactor: 2,
        });
        const page = await context.newPage();
        await page.goto(job.url, { waitUntil: 'networkidle' });
        await page.screenshot({ path: job.out, fullPage: true });
        console.log(job.out);
        await context.close();
    }
    await browser.close();
})();
