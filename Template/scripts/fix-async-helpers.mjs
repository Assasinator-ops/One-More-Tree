// Convert every `function NAME(...)` whose body uses `await serverStorage.`
// into `async function NAME(...)`. Idempotent.

import { readFileSync, writeFileSync, readdirSync, statSync } from 'node:fs';
import path from 'node:path';

const ROOT = path.resolve(process.cwd(), 'src/admin/modules');

function* walk(dir) {
  for (const entry of readdirSync(dir)) {
    const p = path.join(dir, entry);
    if (statSync(p).isDirectory()) yield* walk(p);
    else if (p.endsWith('.tsx')) yield p;
  }
}

function findMatchingBrace(src, openIdx) {
  let depth = 1;
  for (let i = openIdx + 1; i < src.length; i++) {
    const ch = src[i];
    if (ch === '{') depth++;
    else if (ch === '}') {
      depth--;
      if (depth === 0) return i;
    }
  }
  return -1;
}

let patched = 0;
for (const file of walk(ROOT)) {
  let src = readFileSync(file, 'utf8');
  if (!src.includes('serverStorage')) continue;

  let fileChanged = false;
  let cursor = 0;
  const out = [];

  while (true) {
    const matchIdx = src.indexOf('function ', cursor);
    if (matchIdx === -1) {
      out.push(src.slice(cursor));
      break;
    }
    const braceIdx = src.indexOf('{', matchIdx);
    if (braceIdx === -1) {
      out.push(src.slice(cursor));
      break;
    }
    const closeIdx = findMatchingBrace(src, braceIdx);
    if (closeIdx === -1) {
      out.push(src.slice(cursor));
      break;
    }
    const header = src.slice(matchIdx, braceIdx);
    const body = src.slice(braceIdx, closeIdx + 1);
    out.push(src.slice(cursor, matchIdx));

    if (/await\s+serverStorage/.test(body) && !/^async\s+function/.test(header)) {
      const newHeader = 'async ' + header;
      out.push(newHeader);
      out.push(body);
      fileChanged = true;
    } else {
      out.push(header);
      out.push(body);
    }
    cursor = closeIdx + 1;
  }

  if (fileChanged) {
    const updated = out.join('');
    writeFileSync(file, updated, 'utf8');
    patched++;
    console.log(`patched ${path.relative(process.cwd(), file)}`);
  }
}

console.log(`Done. ${patched} file(s) updated.`);
