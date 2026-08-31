import { useEffect, useMemo, useState } from 'react';
import DonationTiersAdmin from './tiers/DonationTiersAdmin';
import DonationOrdersAdmin from './orders/DonationOrdersAdmin';

export type DonationsTabKey = 'tiers' | 'orders';

export default function DonationsAdmin() {
  const tabs = useMemo(() => {
    return [
      { key: 'tiers' as const, label: 'Donation Tiers' },
      { key: 'orders' as const, label: 'Donation Orders/Receipts' },
    ];
  }, []);

  const [activeTab, setActiveTab] = useState<DonationsTabKey>(tabs[0].key);

  // Placeholder scaffold: later we’ll add shared provider/data hooks.
  // For now tiers/orders components are self-contained (localStorage MVP).
  useEffect(() => {
    // no-op
  }, []);

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Donations Module</h2>
        <p className="text-foreground/70 mt-1">
          Administer donation tiers and donation orders/receipts (MVP: localStorage).
          Next step: connect to SQLite/CRM backend with auth + draft/publish.
        </p>
      </div>

      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="flex gap-2 flex-wrap">
          {tabs.map((t) => (
            <button
              key={t.key}
              type="button"
              onClick={() => setActiveTab(t.key)}
              className={
                activeTab === t.key
                  ? 'rounded-full px-4 py-2 bg-forest-600 text-white shadow-medium'
                  : 'rounded-full px-4 py-2 bg-white/60 border border-earth-200 text-foreground/70 hover:text-foreground transition-colors'
              }
            >
              {t.label}
            </button>
          ))}
        </div>
      </div>

      <div className="rounded-3xl glass-card border border-earth-200 p-4 sm:p-6">
        {activeTab === 'tiers' ? <DonationTiersAdmin /> : <DonationOrdersAdmin />}
      </div>
    </div>
  );
}

