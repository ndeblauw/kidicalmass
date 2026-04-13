#!/usr/bin/env node
/**
 * ingest.cjs — chunk a source file for LLM wiki ingestion
 *
 * Usage:
 *   node scripts/ingest.cjs <file-path>
 *
 * Accepts .md and .txt files. Splits into ~2000-word chunks with ~200-word
 * overlap and writes them to docs/raw/chunks/<basename>/chunk-01.md etc.
 */

'use strict';

const fs = require('fs');
const path = require('path');

const CHUNK_WORDS = 2000;
const OVERLAP_WORDS = 200;

function main() {
  const filePath = process.argv[2];

  if (!filePath) {
    console.error('Usage: node scripts/ingest.cjs <file-path>');
    process.exit(1);
  }

  const absPath = path.resolve(filePath);

  if (!fs.existsSync(absPath)) {
    console.error(`File not found: ${absPath}`);
    process.exit(1);
  }

  const ext = path.extname(absPath).toLowerCase();

  if (!['.md', '.txt'].includes(ext)) {
    console.error(`Unsupported file type: ${ext}. Only .md and .txt are supported.`);
    process.exit(1);
  }

  const text = fs.readFileSync(absPath, 'utf8');
  const words = text.split(/\s+/).filter(Boolean);
  const totalWords = words.length;

  const basename = path.basename(absPath, ext);
  const outDir = path.resolve('docs/raw/chunks', basename);
  fs.mkdirSync(outDir, { recursive: true });

  const chunks = [];

  if (totalWords <= CHUNK_WORDS) {
    chunks.push(words.join(' '));
  } else {
    let start = 0;
    while (start < totalWords) {
      const end = Math.min(start + CHUNK_WORDS, totalWords);
      chunks.push(words.slice(start, end).join(' '));
      if (end === totalWords) break;
      start = end - OVERLAP_WORDS;
    }
  }

  const outPaths = [];

  chunks.forEach((chunk, i) => {
    const num = String(i + 1).padStart(2, '0');
    const outFile = path.join(outDir, `chunk-${num}.md`);
    const header = `<!-- source: ${filePath} | chunk ${i + 1}/${chunks.length} -->\n\n`;
    fs.writeFileSync(outFile, header + chunk);
    outPaths.push(outFile);
  });

  console.log(`Source:      ${absPath}`);
  console.log(`Total words: ${totalWords}`);
  console.log(`Chunks:      ${chunks.length}`);
  console.log(`Output dir:  ${outDir}`);
  outPaths.forEach((p) => console.log(`  ${p}`));
}

main();
