// Scrape per-chapter photos from the live Wix site at full resolution.
// Wix serves images from static.wixstatic.com with transform params
// (/v1/fill/w_980,.../file.jpg). Stripping everything after the media id
// (…~mv2.jpg) yields the originally-uploaded file at full resolution.
//
// Usage: node scripts/scrape-chapter-photos.cjs
// Output: docs/raw/website/assets/chapters/<slug>/<nn>-<mediaid>.<ext> + manifest.json
//
// Run as .cjs because the project is "type": "module".

const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const OUT_ROOT = path.join(__dirname, '..', 'docs', 'raw', 'website', 'assets', 'chapters');

// Canonical chapter pages (redirect map) + Liège on its own domain.
const TARGETS = [
  { slug: '1000-bxl', url: 'https://www.kidicalmass.be/1000' },
  { slug: '1030-schaarbeek', url: 'https://www.kidicalmass.be/1030' },
  { slug: '1040-etterbeek', url: 'https://www.kidicalmass.be/1040' },
  { slug: '1050-elsene', url: 'https://www.kidicalmass.be/1050' },
  { slug: '1060-sint-gillis', url: 'https://www.kidicalmass.be/1060' },
  { slug: '1070-anderlecht', url: 'https://www.kidicalmass.be/1070' },
  { slug: '1080-molenbeek', url: 'https://www.kidicalmass.be/1080' },
  { slug: '1090-jette', url: 'https://www.kidicalmass.be/1090' },
  { slug: '1120-noh', url: 'https://www.kidicalmass.be/1120' },
  { slug: '1170-wb-oudergem', url: 'https://www.kidicalmass.be/1170' },
  { slug: '1190-vorst', url: 'https://www.kidicalmass.be/1190' },
  { slug: '5000-namur', url: 'https://www.kidicalmass.be/5000' },
  { slug: '7000-mons', url: 'https://www.kidicalmass.be/7000' },
  { slug: 'liege', url: 'https://www.kidicalmassliege.org/' },
];

// Turn any wixstatic URL into the full-res original by dropping the /v1/... transform.
function toOriginal(url) {
  const m = url.match(/^(https:\/\/static\.wixstatic\.com\/media\/[^/]+)(\/v1\/.*)?$/);
  return m ? m[1] : url;
}

// Pull the displayed width out of a transform URL (w_980) for size filtering.
function widthOf(url) {
  const m = url.match(/\/v1\/[^/]*?w_(\d+)/);
  return m ? parseInt(m[1], 10) : 0;
}

function mediaId(url) {
  const m = url.match(/\/media\/([^/]+?)(~mv2)?\.(jpe?g|png|webp|gif|avif)/i);
  return m ? m[1].replace(/[^a-z0-9_]/gi, '') : 'img';
}

function extOf(url) {
  const m = url.match(/\.(jpe?g|png|webp|gif|avif)/i);
  return m ? m[1].toLowerCase().replace('jpeg', 'jpg') : 'jpg';
}

async function autoScroll(page) {
  await page.evaluate(async () => {
    await new Promise((resolve) => {
      let total = 0;
      const step = 600;
      const timer = setInterval(() => {
        window.scrollBy(0, step);
        total += step;
        if (total >= document.body.scrollHeight + 2000) {
          clearInterval(timer);
          resolve();
        }
      }, 200);
    });
  });
  await page.evaluate(() => window.scrollTo(0, 0));
}

async function collectUrls(page) {
  return page.evaluate(() => {
    const out = [];
    const isImg = (u) => u && /^https?:\/\//.test(u) && /(wixstatic|\.(jpe?g|png|webp|gif|avif)(\?|$))/i.test(u);
    const push = (u, nw = 0) => { if (isImg(u)) out.push({ url: u.split(' ')[0], nw }); };
    // <img src> + srcset, with intrinsic size when available
    document.querySelectorAll('img').forEach((img) => {
      push(img.currentSrc || img.src, img.naturalWidth || 0);
      (img.srcset || '').split(',').forEach((s) => push(s.trim()));
    });
    document.querySelectorAll('source').forEach((s) => {
      (s.srcset || '').split(',').forEach((x) => push(x.trim()));
    });
    document.querySelectorAll('*').forEach((el) => {
      const bg = getComputedStyle(el).backgroundImage;
      if (bg && bg !== 'none') {
        const m = bg.match(/url\(["']?([^"')]+)["']?\)/);
        if (m) push(m[1]);
      }
    });
    document.querySelectorAll('link[href], meta[content]').forEach((el) => {
      push(el.getAttribute('href') || el.getAttribute('content'));
    });
    return out;
  });
}

(async () => {
  const browser = await chromium.launch();
  const context = await browser.newContext({
    ignoreHTTPSErrors: true,
    viewport: { width: 1440, height: 1000 },
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36',
  });
  const summary = [];

  for (const target of TARGETS) {
    const page = await context.newPage();
    let status = 'ok';
    let originals = [];
    try {
      const resp = await page.goto(target.url, { waitUntil: 'domcontentloaded', timeout: 60000 });
      if (resp && resp.status() >= 400) { status = `http-${resp.status()}`; }
      await page.waitForTimeout(2500);
      await autoScroll(page);
      await page.waitForTimeout(2000);
      const raw = await collectUrls(page);

      // Keep content photos; drop tiny icons/logos/avatars/spacers.
      const isWix = (u) => u.includes('static.wixstatic.com/media/');
      const photos = raw.filter(({ url, nw }) => {
        if (isWix(url)) {
          const w = widthOf(url);
          return w === 0 || w >= 250; // w_0 = untransformed original
        }
        return nw === 0 || nw >= 400; // non-wix: judge by intrinsic width
      });

      // Dedupe by media id (wix) or full url (other), keeping the largest seen.
      const byId = new Map();
      for (const { url, nw } of photos) {
        const id = isWix(url) ? mediaId(url) : url.split('?')[0];
        const w = isWix(url) ? widthOf(url) : nw;
        if (!byId.has(id) || w > byId.get(id).w) { byId.set(id, { url, w }); }
      }
      originals = [...byId.values()].map((e) => (isWix(e.url) ? toOriginal(e.url) : e.url));
    } catch (err) {
      status = `error: ${err.message.split('\n')[0]}`;
    }

    const dir = path.join(OUT_ROOT, target.slug);
    fs.mkdirSync(dir, { recursive: true });

    const saved = [];
    let i = 0;
    for (const url of originals) {
      i++;
      try {
        const buf = await (await context.request.get(url, { timeout: 30000 })).body();
        if (buf.length < 3000) { continue; } // skip 1px / placeholder junk
        const name = `${String(i).padStart(2, '0')}-${mediaId(url)}.${extOf(url)}`;
        fs.writeFileSync(path.join(dir, name), buf);
        saved.push({ file: name, url, bytes: buf.length });
      } catch (e) {
        saved.push({ url, error: e.message.split('\n')[0] });
      }
    }

    fs.writeFileSync(
      path.join(dir, 'manifest.json'),
      JSON.stringify({ slug: target.slug, source: target.url, status, count: saved.filter((s) => s.file).length, images: saved }, null, 2)
    );
    summary.push({ slug: target.slug, status, found: originals.length, saved: saved.filter((s) => s.file).length });
    console.log(`${target.slug.padEnd(20)} ${status.padEnd(12)} found ${originals.length}  saved ${saved.filter((s) => s.file).length}`);
    await page.close();
  }

  await browser.close();
  console.log('\nDone. Output: docs/raw/website/assets/chapters/<slug>/');
  console.table(summary);
})();
