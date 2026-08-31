import plantingLocations from '@/data/plantingLocations.json';
import type { PageContent } from './types';
import { PageContentSchema } from './types';

export async function fetchPublishedContent(): Promise<PageContent> {
  // Public-only fetch (no auth). If content is missing/invalid, we return a rejected promise.
  // The caller should decide how to handle loading/error.
  const res = await fetch('/api/public/content?key=full_content', { cache: 'no-store' });
  const json = await res.json();
  if (!res.ok || !json.ok) {
    throw new Error(json?.message ?? 'Failed to fetch published content');
  }
  return PageContentSchema.parse(json.value);
}


