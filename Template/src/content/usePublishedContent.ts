import { useEffect, useState } from 'react';
import { fetchPublishedContent } from './publicClient';
import type { PageContent } from './types'; // type-only import for verbatimModuleSyntax

export function usePublishedContent() {
  const [content, setContent] = useState<PageContent | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<unknown>(null);

  useEffect(() => {
    let mounted = true;

    (async () => {
      try {
        const data = await fetchPublishedContent();
        if (!mounted) return;
        setContent(data);
      } catch (e) {
        if (!mounted) return;
        setError(e);
      }

      if (!mounted) return;
      setLoading(false);
    })();

    return () => {
      mounted = false;
    };
  }, []);


  return { content, loading, error };
}

