'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';

export default function AdminGuard({ children }: { children: React.ReactNode }) {
  const router = useRouter();

  useEffect(() => {
    // Temporary: allow all for now; cookie gating/redirect handled by middleware.
    // This component exists so we can later replace client-side checks if needed.
  }, [router]);

  return children;
}
