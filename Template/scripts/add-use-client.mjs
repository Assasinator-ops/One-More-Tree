// Prepend `'use client';` to every src/sections/*.tsx file that uses hooks
// but is missing the directive. Required for Next 16 App Router.
import { readFileSync, writeFileSync, readdirSync } from 'node:fs';
import path from 'node:path';

const ROOT = path.resolve(process.cwd(), 'src/sections');

let updated = 0;
for (const entry of readdirSync(ROOT)) {
  if (!entry.endsWith('.tsx')) continue;
  const file = path.join(ROOT, entry);
  const src = readFileSync(file, 'utf8');
  if (src.includes("'use client'") || src.includes('"use client"')) continue;
  if (!/\buseState\b|\buseEffect\b|\buseRef\b|\buseMemo\b/.test(src)) continue;

  const patched = `'use client';\n\n` + src;
  writeFileSync(file, patched, 'utf8');
  updated++;
  console.log(`patched ${path.relative(process.cwd(), file)}`);
}
console.log(`Done. ${updated} file(s) updated.`);