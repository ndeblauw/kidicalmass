// Liège runs on Google Sites — photos are served from *.googleusercontent.com
// with a size suffix (=w1280-h960-...). Swapping that suffix to =s0 returns the
// original full-resolution upload.
//
// Usage: node scripts/scrape-liege-photos.cjs

const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const OUT = path.join(__dirname, '..', 'docs', 'raw', 'website', 'assets', 'chapters', 'liege');

function toFullRes(url) {
  // .../<id>=w1280-h960-rw  ->  .../<id>=s0  (full-resolution original)
  return url.replace(/=[^/=]*$/, '') + '=s0';
}

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: 1440, height: 1000 } });
  const page = await ctx.newPage();
  await page.goto('https://www.kidicalmassliege.org/', { waitUntil: 'domcontentloaded', timeout: 60000 });
  await page.waitForTimeout(3000);
  await page.evaluate(async () => {
    await new Promise((resolve) => {
      let t = 0;
      const i = setInterval(() => {
        window.scrollBy(0, 700); t += 700;
        if (t >= document.body.scrollHeight + 2500) { clearInterval(i); resolve(); }
      }, 150);
    });
  });
  await page.waitForTimeout(2500);

  const found = await page.evaluate(() => {
    const out = [];
    document.querySelectorAll('img').forEach((img) => {
      const src = img.currentSrc || img.src;
      if (src && /googleusercontent\.com/.test(src)) {
        out.push({ src, w: img.naturalWidth, h: img.naturalHeight });
      }
    });
    return out;
  });

  // Keep content photos (drop tiny avatars/icons), dedupe by image id.
  const byId = new Map();
  for (const { src, w } of found) {
    if (w && w < 400) { continue; }
    const id = (src.match(/\/([A-Za-z0-9_-]{20,})=/) || src.match(/\/([^/=?]+)=/) || [])[1] || src;
    if (!byId.has(id) || w > byId.get(id).w) { byId.set(id, { src, w }); }
  }

  fs.mkdirSync(OUT, { recursive: true });
  const saved = [];
  let i = 0;
  for (const { src } of byId.values()) {
    i++;
    const url = toFullRes(src);
    try {
      const resp = await ctx.request.get(url, { timeout: 30000 });
      const buf = await resp.body();
      if (buf.length < 5000) { continue; }
      const ct = resp.headers()['content-type'] || '';
      const ext = ct.includes('png') ? 'png' : ct.includes('webp') ? 'webp' : 'jpg';
      const name = `${String(i).padStart(2, '0')}-liege.${ext}`;
      fs.writeFileSync(path.join(OUT, name), buf);
      saved.push({ file: name, url, bytes: buf.length });
    } catch (e) {
      saved.push({ url, error: e.message.split('\n')[0] });
    }
  }
  fs.writeFileSync(path.join(OUT, 'manifest.json'), JSON.stringify({
    slug: 'liege', source: 'https://www.kidicalmassliege.org/', platform: 'google-sites',
    local_photos: saved.filter((s) => s.file).length, images: saved,
  }, null, 2));
  await browser.close();
  console.log(`Liège: found ${found.length} googleusercontent imgs, saved ${saved.filter((s) => s.file).length} full-res photos.`);
  saved.filter((s) => s.file).forEach((s) => console.log(`  ${s.file}  ${(s.bytes / 1024).toFixed(0)} KB`));
})();
