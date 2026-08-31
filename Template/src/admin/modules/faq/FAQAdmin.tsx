'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

type FAQItem = {
  id: string;
  question: string;
  answer: string;
};

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

function sanitizeFAQ(raw: unknown): FAQItem[] {
  if (!Array.isArray(raw)) return [];
  return raw
    .filter(Boolean)
    .map((item, index) => {
      const it = item as Partial<FAQItem>;
      const id = String(it?.id ?? `faq-${index + 1}`).trim();
      return {
        id: id || `faq-${index + 1}`,
        question: String(it?.question ?? '').trim() || `Question ${index + 1}`,
        answer: String(it?.answer ?? '').trim() || 'Answer goes here.',
      };
    })
    .filter((it) => it.id.length > 0);
}

function makeBlankFAQ(): FAQItem {
  return {
    id: `faq-${Date.now()}`,
    question: 'New question',
    answer: 'Provide a detailed answer here.',
  };
}

export default function FAQAdmin() {
  const [loaded, setLoaded] = useState(false);
  const [faq, setFaq] = useState<FAQItem[]>([]);
  const [selectedId, setSelectedId] = useState<string | null>(null);
  const [draft, setDraft] = useState<FAQItem>(makeBlankFAQ());
  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const stored = await adminApi.read<unknown>(AdminKeys.Faq);
        const next = sanitizeFAQ((stored as any)?.items ?? stored);
        if (!alive) return;
        const initial = next.length ? next : [makeBlankFAQ()];
        setFaq(initial);
        setSelectedId(initial[0]?.id ?? null);
        setDraft(initial[0] ?? makeBlankFAQ());
      } catch {
        if (!alive) return;
        const initial = [makeBlankFAQ()];
        setFaq(initial);
        setSelectedId(initial[0]?.id ?? null);
        setDraft(initial[0] ?? makeBlankFAQ());
      } finally {
        if (alive) setLoaded(true);
      }
    })();
    return () => {
      alive = false;
    };
  }, []);

  const selectedItem = useMemo(() => faq.find((item) => item.id === selectedId) ?? null, [faq, selectedId]);

  useEffect(() => {
    if (!loaded) return;
    if (selectedItem) {
      setDraft(selectedItem);
      setSaveResult(null);
    } else {
      const blank = makeBlankFAQ();
      setDraft(blank);
      setSaveResult(null);
    }
  }, [loaded, selectedItem]);

  const onSave = async () => {
    try {
      const saved: FAQItem = {
        ...draft,
        id: String(draft.id).trim() || `faq-${Date.now()}`,
        question: String(draft.question).trim() || 'New question',
        answer: String(draft.answer).trim() || 'Provide a detailed answer here.',
      };

      const nextFaq = selectedItem
        ? faq.map((item) => (item.id === saved.id ? saved : item))
        : [saved, ...faq];

      await adminApi.write(AdminKeys.Faq, { items: nextFaq });
      setFaq(nextFaq);
      setSelectedId(saved.id);
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
    } catch (e) {
      setSaveResult({ ok: false, error: e instanceof Error ? e.message : 'Unknown error' });
    }
  };

  const onDelete = async () => {
    if (!selectedItem) return;
    const next = faq.filter((item) => item.id !== selectedItem.id);
    const final = next.length ? next : [makeBlankFAQ()];
    try {
      await adminApi.write(AdminKeys.Faq, { items: final });
      setFaq(final);
      setSelectedId(final[0]?.id ?? null);
      setDraft(final[0] ?? makeBlankFAQ());
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
    } catch (e) {
      setSaveResult({ ok: false, error: e instanceof Error ? e.message : 'Unknown error' });
    }
  };

  const onAddNew = () => {
    const blank = makeBlankFAQ();
    setFaq((prev) => [blank, ...prev]);
    setSelectedId(blank.id);
    setDraft(blank);
    setSaveResult(null);
  };

  if (!loaded) {
    return (
      <div className="space-y-6">
        <div>
          <h2 className="font-serif text-2xl font-bold text-foreground">FAQ Admin</h2>
        </div>
        <div className="text-foreground/70">Loading…</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">FAQ Admin</h2>
        <p className="text-foreground/70 mt-1">Manage frequently asked questions and answers shown on the public FAQ page.</p>
      </div>

      <div className="flex flex-col lg:flex-row gap-6">
        <div className="lg:w-1/3 rounded-3xl border border-earth-200 bg-white/50 p-4 sm:p-5">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Questions</h3>
            <button type="button" onClick={onAddNew} className="rounded-full px-3 py-2 bg-forest-600 text-white text-sm">
              Add
            </button>
          </div>
          {faq.length === 0 ? (
            <div className="text-foreground/70">No FAQ items yet. Add one to get started.</div>
          ) : (
            <div className="space-y-3">
              {faq.map((item) => (
                <button
                  key={item.id}
                  type="button"
                  onClick={() => setSelectedId(item.id)}
                  className={`w-full text-left rounded-2xl border p-4 transition ${
                    selectedId === item.id ? 'border-forest-500 bg-forest-50' : 'border-earth-200 bg-white'
                  }`}
                >
                  <div className="font-semibold text-foreground">{item.question}</div>
                </button>
              ))}
            </div>
          )}
        </div>

        <div className="lg:w-2/3 rounded-3xl border border-earth-200 bg-white/80 p-5 sm:p-6 space-y-6">
          <div className="space-y-4">
            <div className="space-y-2">
              <label className="text-sm font-medium text-foreground/80">Question</label>
              <input
                value={draft.question}
                onChange={(e) => setDraft((prev) => ({ ...prev, question: e.target.value }))}
                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
              />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-foreground/80">Answer</label>
              <textarea
                rows={5}
                value={draft.answer}
                onChange={(e) => setDraft((prev) => ({ ...prev, answer: e.target.value }))}
                className="w-full rounded-2xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
              />
            </div>
          </div>

          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div className="flex gap-2">
              <button
                type="button"
                onClick={() => void onDelete()}
                disabled={!selectedItem}
                className="rounded-xl px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition disabled:opacity-50"
              >
                Delete
              </button>
              <button type="button" onClick={() => void onSave()} className="rounded-xl px-4 py-2 bg-forest-600 hover:bg-forest-700 text-white transition-colors shadow-medium">
                Save FAQ item
              </button>
            </div>
            <div className="text-sm text-foreground/60">
              {saveResult?.ok ? `Saved at ${new Date(saveResult.savedAt).toLocaleString()}` : saveResult?.error ?? 'Ready'}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

