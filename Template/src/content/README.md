# Content scaffold (admin-ready)

This folder will hold the typed content contracts and the public content loader.

Next steps:
1. Define contracts in `types.ts`
2. Implement `publicClient.ts` (fetches published content)
3. Implement `adminClient.ts` (writes drafts/publishes) — optional, server-side preferred
4. Refactor sections/pages to read from a single `usePublishedContent()` hook.

