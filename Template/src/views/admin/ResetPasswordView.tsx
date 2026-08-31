'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';

export default function ResetPasswordView() {
  const router = useRouter();
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const [checked, setChecked] = useState(false);

  useEffect(() => {
    fetch('/api/admin/me')
      .then((r) => r.json())
      .then((data: { authenticated?: boolean; mustResetPassword?: boolean }) => {
        if (!data.authenticated) {
          router.replace('/admin/login');
        } else if (!data.mustResetPassword) {
          router.replace('/admin');
        }
      })
      .finally(() => setChecked(true));
  }, [router]);

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    if (password.length < 8) {
      setError('Password must be at least 8 characters.');
      return;
    }
    if (password !== confirmPassword) {
      setError('Passwords do not match.');
      return;
    }
    setLoading(true);
    try {
      const res = await fetch('/api/admin/reset-password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ password, confirmPassword })
      });
      const data = await res.json();
      if (!res.ok || !data.ok) {
        setError(data.message ?? 'Unable to update password.');
        return;
      }
      router.replace('/admin');
      router.refresh();
    } catch {
      setError('Unable to update password. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  if (!checked) return null;

  return (
    <div className="min-h-screen bg-background py-24">
      <div className="max-w-xl mx-auto rounded-3xl border border-earth-200 bg-white/90 p-8 shadow-xl">
        <h1 className="font-serif text-4xl font-bold text-foreground mb-4">Reset admin password</h1>
        <p className="text-foreground/70 mb-6">
          Set a new password before you continue. This protects the admin console and prevents the default password from being reused.
        </p>
        <form onSubmit={submit} className="space-y-5">
          <label className="block">
            <span className="text-sm font-medium text-foreground/80">New password</span>
            <input
              type="password"
              value={password}
              onChange={(event) => setPassword(event.target.value)}
              className="mt-2 w-full rounded-2xl border border-earth-200 bg-slate-50 px-4 py-3 text-foreground outline-none focus:border-forest-500"
              autoComplete="new-password"
              required
            />
          </label>
          <label className="block">
            <span className="text-sm font-medium text-foreground/80">Confirm password</span>
            <input
              type="password"
              value={confirmPassword}
              onChange={(event) => setConfirmPassword(event.target.value)}
              className="mt-2 w-full rounded-2xl border border-earth-200 bg-slate-50 px-4 py-3 text-foreground outline-none focus:border-forest-500"
              autoComplete="new-password"
              required
            />
          </label>
          {error ? (
            <div className="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">{error}</div>
          ) : null}
          <button
            type="submit"
            disabled={loading}
            className="w-full rounded-2xl bg-forest-600 px-5 py-3 text-white shadow-medium transition hover:bg-forest-700 disabled:cursor-not-allowed disabled:opacity-70"
          >
            {loading ? 'Updating…' : 'Update password'}
          </button>
        </form>
      </div>
    </div>
  );
}
