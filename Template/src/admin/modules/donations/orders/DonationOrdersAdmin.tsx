'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

type DonationOrder = {
  id: string;
  amountEur: number;
  donorName?: string;
  email?: string;
  createdAt?: string;
};

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

function clampMoney(n: unknown) {
  const num = Number(n);
  return Number.isFinite(num) ? Math.max(0, Math.floor(num)) : 0;
}

function sanitizeOrders(raw: unknown): DonationOrder[] {
  if (!Array.isArray(raw)) return [];
  return raw
    .filter(Boolean)
    .map((o: any, idx) => {
      const id = String(o?.id ?? `order-${idx + 1}`).trim() || `order-${idx + 1}`;
      return {
        id,
        amountEur: clampMoney(o?.amountEur ?? o?.amount),
        donorName: typeof o?.donorName === 'string' ? o.donorName : undefined,
        email: typeof o?.email === 'string' ? o.email : undefined,
        createdAt: typeof o?.createdAt === 'string' ? o.createdAt : undefined,
      } as DonationOrder;
    });
}

export default function DonationOrdersAdmin() {
  const [loaded, setLoaded] = useState(false);
  const [orders, setOrders] = useState<DonationOrder[]>([]);
  const [selectedId, setSelectedId] = useState<string | null>(null);
  const selected = useMemo(() => orders.find((o) => o.id === selectedId) ?? null, [orders, selectedId]);

  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const stored = await adminApi.read<unknown>(AdminKeys.DonationOrders);
        const next = sanitizeOrders((stored as any)?.orders ?? stored);
        if (!alive) return;
        setOrders(next);
        setSelectedId(next[0]?.id ?? null);
      } catch {
        if (!alive) return;
        setOrders([]);
        setSelectedId(null);
      } finally {
        if (alive) setLoaded(true);
      }
    })();
    return () => {
      alive = false;
    };
  }, []);

  const onDelete = async () => {
    if (!selected) return;
    const next = orders.filter((o) => o.id !== selected.id);
    try {
      await adminApi.write(AdminKeys.DonationOrders, { orders: next });
      setOrders(next);
      setSelectedId(next[0]?.id ?? null);
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
    } catch (e) {
      setSaveResult({ ok: false, error: e instanceof Error ? e.message : 'Unknown error' });
    }
  };

  if (!loaded) {
    return (
      <div className="space-y-6">
        <div>
          <h2 className="font-serif text-2xl font-bold text-foreground">Donation orders</h2>
        </div>
        <div className="text-foreground/70">Loading…</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Donation orders</h2>
        <p className="text-foreground/70 mt-1">View donation orders captured from the public donation flow.</p>
      </div>

      <div className="flex flex-col lg:flex-row gap-6">
        <div className="lg:w-1/3 rounded-3xl border border-earth-200 bg-white/50 p-4 sm:p-5">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Orders</h3>
          </div>

          {orders.length === 0 ? (
            <div className="text-foreground/70">No orders yet.</div>
          ) : (
            <div className="space-y-3">
              {orders.map((o) => (
                <button
                  key={o.id}
                  type="button"
                  onClick={() => setSelectedId(o.id)}
                  className={`w-full text-left rounded-2xl border p-4 transition ${
                    selectedId === o.id ? 'border-forest-500 bg-forest-50' : 'border-earth-200 bg-white'
                  }`}
                >
                  <div className="flex items-center justify-between gap-2">
                    <div>
                      <div className="font-semibold text-foreground">€{o.amountEur}</div>
                      <div className="text-xs text-foreground/60">{o.donorName ?? o.email ?? o.id}</div>
                    </div>
                    <div className="text-xs text-foreground/70">View</div>
                  </div>
                </button>
              ))}
            </div>
          )}
        </div>

        <div className="lg:w-2/3 rounded-3xl border border-earth-200 bg-white/80 p-5 sm:p-6 space-y-6">
          <div className="space-y-3">
            <div className="text-sm text-foreground/50">Selected order</div>
            {selected ? (
              <div className="rounded-2xl border border-earth-200 bg-white/50 p-4">
                <div className="text-lg font-semibold text-foreground">€{selected.amountEur}</div>
                <div className="text-sm text-foreground/70 mt-1">
                  Donor: {selected.donorName ?? selected.email ?? '—'}
                </div>
                <div className="text-xs text-foreground/50 mt-1">ID: {selected.id}</div>
                <div className="text-xs text-foreground/50 mt-1">Created: {selected.createdAt ?? '—'}</div>
              </div>
            ) : (
              <div className="text-foreground/70">Select an order from the list.</div>
            )}
          </div>

          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <button
              type="button"
              onClick={() => void onDelete()}
              disabled={!selected}
              className="rounded-xl px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition disabled:opacity-50"
            >
              Delete order
            </button>

            <div className="text-sm text-foreground/60">
              {saveResult?.ok ? `Saved at ${new Date(saveResult.savedAt).toLocaleString()}` : saveResult?.error ?? 'Ready'}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

