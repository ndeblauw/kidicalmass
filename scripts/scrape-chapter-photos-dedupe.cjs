// Post-process the scraped chapter photos:
//  - Any image whose content appears in 2+ group folders is shared boilerplate
//    (logo, banners, crowdfunding QR, character illustrations) → move ONE copy
//    to _shared/ and remove it from every group folder.
//  - Group folders are left holding only that group's genuine local photos.
//  - Rewrite each manifest.json to reflect what remains.
//
// Usage: node scripts/scrape-chapter-photos-dedupe.cjs

const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const ROOT = path.join(__dirname, '..', 'docs', 'raw', 'website', 'assets', 'chapters');
const SHARED = path.join(ROOT, '_shared');
const IMG = /\.(jpe?g|png|webp|gif|avif)$/i;

const groups = fs.readdirSync(ROOT).filter((d) => {
  return d !== '_shared' && fs.statSync(path.join(ROOT, d)).isDirectory();
});

// hash -> { groups:Set, files:[{group,name,full,bytes}] }
const byHash = new Map();
for (const g of groups) {
  const dir = path.join(ROOT, g);
  for (const name of fs.readdirSync(dir)) {
    if (!IMG.test(name)) { continue; }
    const full = path.join(dir, name);
    const buf = fs.readFileSync(full);
    const h = crypto.createHash('md5').update(buf).digest('hex');
    if (!byHash.has(h)) { byHash.set(h, { groups: new Set(), files: [] }); }
    const e = byHash.get(h);
    e.groups.add(g);
    e.files.push({ group: g, name, full, bytes: buf.length });
  }
}

fs.mkdirSync(SHARED, { recursive: true });
let movedShared = 0;
let removedCopies = 0;

for (const [, e] of byHash) {
  if (e.groups.size < 2) { continue; } // unique to one group → keep in place
  // shared: keep one copy in _shared (strip numeric prefix for a clean name)
  const first = e.files[0];
  const cleanName = first.name.replace(/^\d+-/, '');
  const dest = path.join(SHARED, cleanName);
  if (!fs.existsSync(dest)) { fs.copyFileSync(first.full, dest); movedShared++; }
  for (const f of e.files) { fs.unlinkSync(f.full); removedCopies++; }
}

// Rewrite manifests to list the surviving local photos.
const report = [];
for (const g of groups) {
  const dir = path.join(ROOT, g);
  const files = fs.readdirSync(dir).filter((n) => IMG.test(n)).sort();
  const images = files.map((name) => {
    const st = fs.statSync(path.join(dir, name));
    return { file: name, bytes: st.size };
  });
  const manifestPath = path.join(dir, 'manifest.json');
  let manifest = {};
  if (fs.existsSync(manifestPath)) { manifest = JSON.parse(fs.readFileSync(manifestPath)); }
  manifest.local_photos = images.length;
  manifest.images = images;
  fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
  report.push({ group: g, local_photos: images.length });
}

console.log(`Moved ${movedShared} shared images to _shared/, removed ${removedCopies} duplicate copies from group folders.\n`);
console.table(report);
