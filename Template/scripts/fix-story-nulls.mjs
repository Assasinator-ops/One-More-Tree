// Wrap potentially-undefined array access with `?? []` to satisfy
// strictNullChecks on chapter.descriptions and chapter.infoCards.
import { readFileSync, writeFileSync } from 'node:fs';

const FILES = ['src/sections/StorySeed.tsx', 'src/sections/StoryGrowth.tsx', 'src/sections/StoryMature.tsx'];

for (const file of FILES) {
  let src = readFileSync(file, 'utf8');
  const before = src;

  src = src.replace(/chapter\.descriptions\.map/g, '(chapter.descriptions ?? []).map');
  src = src.replace(/chapter\.infoCards\.map/g, '(chapter.infoCards ?? []).map');

  if (src !== before) {
    writeFileSync(file, src, 'utf8');
    console.log(`patched ${file}`);
  } else {
    console.log(`no changes for ${file}`);
  }
}