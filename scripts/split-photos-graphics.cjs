// Split each chapter folder into photos/ (genuine event photos) and graphics/
// (title cards, promo posters, illustrations, partner logos), based on a manual
// visual classification of the contact sheets. Everything not listed as a
// graphic is treated as a photo. Rewrites each manifest.json accordingly.
//
// Usage: node scripts/split-photos-graphics.cjs

const fs = require('fs');
const path = require('path');

const ROOT = path.join(__dirname, '..', 'docs', 'raw', 'website', 'assets', 'chapters');
const IMG = /\.(jpe?g|png|webp)$/i;

// Per group: numeric file-prefixes that are GRAPHICS (not photos).
const GRAPHICS = {
  '1000-bxl': ['02', '04'],
  '1030-schaarbeek': [],
  '1040-etterbeek': ['02'],
  '1050-elsene': ['02'],
  '1060-sint-gillis': ['02'],
  '1070-anderlecht': ['02'],
  '1120-noh': ['02', '04'],
  '1170-wb-oudergem': ['02', '03'],
  '1190-vorst': ['02'],
  '5000-namur': ['02', '07'],
  '7000-mons': [],
  liege: ['01', '02', '06', '07'],
};

const report = [];
for (const [g, graphicPrefixes] of Object.entries(GRAPHICS)) {
  const dir = path.join(ROOT, g);
  if (!fs.existsSync(dir)) { continue; }
  const files = fs.readdirSync(dir).filter((n) => IMG.test(n));
  const photosDir = path.join(dir, 'photos');
  const graphicsDir = path.join(dir, 'graphics');

  const photos = [];
  const graphics = [];
  for (const f of files) {
    const prefix = (f.match(/^(\d+)-/) || [])[1];
    const isGraphic = prefix && graphicPrefixes.includes(prefix);
    const targetDir = isGraphic ? graphicsDir : photosDir;
    fs.mkdirSync(targetDir, { recursive: true });
    fs.renameSync(path.join(dir, f), path.join(targetDir, f));
    (isGraphic ? graphics : photos).push(f);
  }

  // Rewrite manifest with the split, preserving source URL info if present.
  const manifestPath = path.join(dir, 'manifest.json');
  let manifest = {};
  if (fs.existsSync(manifestPath)) { manifest = JSON.parse(fs.readFileSync(manifestPath)); }
  delete manifest.images;
  delete manifest.local_photos;
  manifest.photos = photos.sort().map((f) => ({ file: `photos/${f}`, bytes: fs.statSync(path.join(photosDir, f)).size }));
  manifest.graphics = graphics.sort().map((f) => ({ file: `graphics/${f}`, bytes: fs.statSync(path.join(graphicsDir, f)).size }));
  manifest.photo_count = photos.length;
  manifest.graphic_count = graphics.length;
  fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));

  report.push({ group: g, photos: photos.length, graphics: graphics.length });
}

console.table(report);
console.log(`Photos total: ${report.reduce((a, r) => a + r.photos, 0)} | Graphics total: ${report.reduce((a, r) => a + r.graphics, 0)}`);
