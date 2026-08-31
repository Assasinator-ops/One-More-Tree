// Preview client scaffold.
// Later, when your CMS supports draft/preview endpoints,
// this module will fetch draft content when preview mode is enabled.

import { fetchPublishedContent } from './publicClient';
import type { PageContent } from './types';

export async function fetchPreviewContent(): Promise<PageContent> {
  // TODO: Replace with CMS draft/preview fetch.
  return fetchPublishedContent();
}

