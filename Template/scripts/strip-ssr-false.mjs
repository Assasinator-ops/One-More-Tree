// Convert all app/**/page.tsx files that use `dynamic(..., { ssr: false })`
// to a direct import. Next 16 forbids ssr:false in Server Components.

import { readFileSync, writeFileSync, readdirSync, statSync } from 'node:fs';
import path from 'node:path';

const ROOT = path.resolve(process.cwd(), 'app');

function* walk(dir) {
  for (const entry of readdirSync(dir)) {
    const p = path.join(dir, entry);
    if (statSync(p).isDirectory()) yield* walk(p);
    else if (p.endsWith('page.tsx')) yield p;
  }
}

let updated = 0;
for (const file of walk(ROOT)) {
  let src = readFileSync(file, 'utf8');
  if (!src.includes('ssr: false')) continue;

  // Strip the `import dynamic from 'next/dynamic';` line
  src = src.replace(/import\s+dynamic\s+from\s+['"]next\/dynamic['"];?\s*\n/, '');

  // Replace the `const XxxView = dynamic(() => import('...'), { ssr: false, loading: ... });` block
  // with `import XxxView from '...'`.
  src = src.replace(
    /const\s+(\w+)\s*=\s*dynamic\(\s*\(\s*\)\s*=>\s*import\(\s*['"]([^'"]+)['"]\s*\)\s*,\s*\{\s*ssr:\s*false[\s\S]*?\}\s*\);/,
    (_match, name, spec) => `import ${name} from '${spec}';`
  );

  writeFileSync(file, src, 'utf8');
  updated++;
  console.log(`patched ${path.relative(process.cwd(), file)}`);
}

console.log(`Done. ${updated} file(s) updated.`);