'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { Eye, EyeOff, Lock, User, ShieldAlert, Loader2 } from 'lucide-react';

export default function LoginView() {
  const router = useRouter();
  const [showPassword, setShowPassword] = useState(false);
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  // const [error, setError] = useState('');
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    // If the visitor is already signed in, send them straight to /admin.
    fetch('/api/admin/me')
      .then((r) => r.json())
      .then((data: { authenticated?: boolean; mustResetPassword?: boolean }) => {
        if (data.authenticated && !data.mustResetPassword) router.replace('/admin');
        if (data.authenticated && data.mustResetPassword) router.replace('/admin/reset-password');
      })
      .catch(() => undefined);
  }, [router]);

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    setLoading(true);
    try {
      const res = await fetch('/api/admin/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
      });
      const data = await res.json();
      if (!res.ok || !data.ok) {
        setError(data.message ?? 'Invalid credentials.');
        return;
      }
      router.replace(data.mustResetPassword ? '/admin/reset-password' : '/admin');
      router.refresh();
    } catch {
      setError('Unable to sign in. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-background flex flex-col justify-center items-center p-6 selection:bg-forest-100 selection:text-forest-900">
      {/* Decorative Background Elements */}
      <div className="absolute inset-0 overflow-hidden -z-10">
        <div className="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-forest-50/50 blur-3xl" />
        <div className="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] rounded-full bg-earth-50/50 blur-3xl" />
      </div>

      <div className="w-full max-w-[440px]">
        {/* Brand/Logo Area */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white border border-earth-200 shadow-sm mb-4">
            <Lock className="w-8 h-8 text-forest-600" />
          </div>
          <h1 className="text-3xl font-serif font-bold text-foreground tracking-tight">Admin Console</h1>
          <p className="mt-2 text-foreground/60 text-sm">Secure access for system administrators only.</p>
        </div>

        {/* Main Card */}
        <div className="bg-white/80 backdrop-blur-xl rounded-[2rem] border border-earth-200 p-8 shadow-[0_20px_50px_rgba(0,0,0,0.05)]">
          <form onSubmit={submit} className="space-y-6">
            
            {/* Username Field */}
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wider text-foreground/50 ml-1">
                Username
              </label>
              <div className="relative group">
                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <User className="h-5 w-5 text-earth-400 group-focus-within:text-forest-500 transition-colors" />
                </div>
                <input
                  type="text"
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  placeholder="Enter admin ID"
                  className="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-earth-200 rounded-2xl text-foreground placeholder:text-earth-300 outline-none transition-all focus:bg-white focus:ring-4 focus:ring-forest-500/10 focus:border-forest-500"
                  required
                />
              </div>
            </div>

            {/* Password Field */}
            <div className="space-y-2">
              <label className="text-xs font-semibold uppercase tracking-wider text-foreground/50 ml-1">
                Password
              </label>
              <div className="relative group">
                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <Lock className="h-5 w-5 text-earth-400 group-focus-within:text-forest-500 transition-colors" />
                </div>
                <input
                  type={showPassword ? "text" : "password"}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="••••••••"
                  className="block w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-earth-200 rounded-2xl text-foreground placeholder:text-earth-300 outline-none transition-all focus:bg-white focus:ring-4 focus:ring-forest-500/10 focus:border-forest-500"
                  required
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute inset-y-0 right-0 pr-4 flex items-center text-earth-400 hover:text-forest-600 transition-colors"
                >
                  {showPassword ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
                </button>
              </div>
            </div>

            {error && (
              <div className="flex items-center gap-3 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm text-red-700 animate-in fade-in slide-in-from-top-1">
                <ShieldAlert className="h-5 w-5 shrink-0" />
                <p>{error}</p>
              </div>
            )}

            <button
              type="submit"
              disabled={loading}
              className="relative w-full overflow-hidden rounded-2xl bg-forest-600 px-5 py-4 text-white font-semibold shadow-lg shadow-forest-500/20 transition-all hover:bg-forest-700 hover:shadow-forest-500/30 active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed group"
            >
              <span className={`flex items-center justify-center gap-2 ${loading ? 'opacity-0' : 'opacity-100 transition-opacity'}`}>
                Access Console
              </span>
              {loading && (
                <div className="absolute inset-0 flex items-center justify-center">
                  <Loader2 className="h-5 w-5 animate-spin" />
                </div>
              )}
            </button>
          </form>

          {/* Legal Warning Section */}
          <div className="mt-8 pt-6 border-t border-earth-100">
            <div className="flex gap-3">
              <ShieldAlert className="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
              <p className="text-[11px] leading-relaxed text-foreground/50 uppercase tracking-tight">
                <strong>Warning:</strong> Unauthorized access to this environment is strictly prohibited. 
                System usage is monitored and logged. Continued use implies consent to security auditing 
                under applicable data protection laws.
              </p>
            </div>
          </div>
        </div>

        {/* Footer Link */}
        <p className="mt-8 text-center text-sm text-foreground/40">
          Need help? <a href="#" className="underline decoration-earth-200 underline-offset-4 hover:text-forest-600 transition-colors">Contact Support</a>
        </p>
      </div>
    </div>
  );
}
