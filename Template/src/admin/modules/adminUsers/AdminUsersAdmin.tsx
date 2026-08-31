import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';


type AdminUserView = {
  username: string;
  createdAt: string;
  mustResetPassword: boolean;
};

type Toast = {
  kind: 'success' | 'error';
  message: string;
} | null;

async function postAdminData(key: string, value: unknown) {
  const res = await fetch('/api/admin/data', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ key, value })
  });
  const data = (await res.json().catch(() => ({}))) as { ok?: boolean; message?: string };
  if (!res.ok || data.ok === false) {
    throw new Error(data.message ?? 'Request failed');
  }
  return data;
}

export default function AdminUsersAdmin() {
  const [users, setUsers] = useState<AdminUserView[]>([]);

  // Add account form
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [saving, setSaving] = useState(false);

  // Per-user actions
  const [editingUser, setEditingUser] = useState<string | null>(null);
  const [editPassword, setEditPassword] = useState('');
  const [editConfirmPassword, setEditConfirmPassword] = useState('');
  const [actionBusy, setActionBusy] = useState(false);

  const [toast, setToast] = useState<Toast>(null);
  const toastTimer = useMemo(() => ({ current: null as number | null }), []);

  const showToast = (next: Toast) => {
    setToast(next);
    if (toastTimer.current) window.clearTimeout(toastTimer.current);
    toastTimer.current = window.setTimeout(() => setToast(null), 5000);
  };

  useEffect(() => {
    (async () => {
      const value = await adminApi.read<AdminUserView[]>(AdminKeys.AdminUsers);
      if (value) setUsers(value);
    })();
  }, []);

  const refreshUsers = async () => {
    const value = await adminApi.read<AdminUserView[]>(AdminKeys.AdminUsers);
    if (value) setUsers(value);
  };


  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();

    if (!username.trim()) {
      showToast({ kind: 'error', message: 'Please enter a username.' });
      return;
    }
    if (password.length < 8) {
      showToast({ kind: 'error', message: 'Password must be at least 8 characters.' });
      return;
    }
    if (password !== confirmPassword) {
      showToast({ kind: 'error', message: 'Passwords do not match.' });
      return;
    }

    setSaving(true);
    setToast(null);
    try {
      await postAdminData('admin_users/create', { username, password });
      showToast({
        kind: 'success',
        message: 'New admin account added successfully. The account must reset its password on first login.'
      });
      setUsername('');
      setPassword('');
      setConfirmPassword('');
      refreshUsers();
    } catch (err) {
      showToast({ kind: 'error', message: err instanceof Error ? err.message : 'Unable to add admin.' });
    } finally {
      setSaving(false);
    }
  };

  const startEditPassword = (u: string) => {
    setEditingUser(u);
    setEditPassword('');
    setEditConfirmPassword('');
    setToast(null);
  };

  const cancelEditPassword = () => {
    setEditingUser(null);
    setEditPassword('');
    setEditConfirmPassword('');
    setActionBusy(false);
  };

  const saveNewPassword = async () => {
    if (!editingUser) return;
    if (editPassword.length < 8) {
      showToast({ kind: 'error', message: 'Password must be at least 8 characters.' });
      return;
    }
    if (editPassword !== editConfirmPassword) {
      showToast({ kind: 'error', message: 'Passwords do not match.' });
      return;
    }

    setActionBusy(true);
    setToast(null);
    try {
      await postAdminData('admin_users/password', {
        username: editingUser,
        password: editPassword
      });
      showToast({ kind: 'success', message: `Password updated for ${editingUser}.` });
      cancelEditPassword();
      refreshUsers();
    } catch (err) {
      showToast({ kind: 'error', message: err instanceof Error ? err.message : 'Unable to update password.' });
    } finally {
      setActionBusy(false);
    }
  };

  const deleteUser = async (u: string) => {
    const ok = window.confirm(
      `Delete admin account "${u}"? This cannot be undone.
\nTip: deletion is blocked for the default "admin" account and for the last remaining admin account.`
    );
    if (!ok) return;

    setActionBusy(true);
    setToast(null);
    try {
      await postAdminData('admin_users/delete', { username: u });
      showToast({ kind: 'success', message: `Deleted ${u}.` });
      if (editingUser === u) cancelEditPassword();
      refreshUsers();
    } catch (err) {
      showToast({ kind: 'error', message: err instanceof Error ? err.message : 'Unable to delete admin.' });
    } finally {
      setActionBusy(false);
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Admin users</h2>
        <p className="text-foreground/70 mt-1">
          Manage admin credentials. Create new admins, rotate passwords, or delete accounts. Operations are validated and
          guarded to prevent accidental lockouts.
        </p>
      </div>

      <div className="rounded-3xl border border-earth-200 bg-white/50 p-5 sm:p-6">
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {users.map((user) => {
            const isEditing = editingUser === user.username;
            return (
              <div key={user.username} className="rounded-2xl border border-earth-200 bg-slate-50 p-4">
                <div className="font-semibold text-foreground">{user.username}</div>
                <div className="text-sm text-foreground/60">Created {new Date(user.createdAt).toLocaleDateString()}</div>
                <div className="mt-2 text-sm text-foreground/70">
                  {user.mustResetPassword ? 'Password reset required' : 'Password set'}
                </div>

                <div className="mt-4 flex flex-col gap-2">
                  {isEditing ? (
                    <>
                      <label className="block">
                        <span className="text-sm font-medium text-foreground/80">New password</span>
                        <input
                          type="password"
                          value={editPassword}
                          onChange={(event) => setEditPassword(event.target.value)}
                          className="mt-2 w-full rounded-2xl border border-earth-200 bg-slate-50 px-4 py-2.5 text-foreground outline-none focus:border-forest-500"
                          autoComplete="new-password"
                          required
                        />
                      </label>
                      <label className="block">
                        <span className="text-sm font-medium text-foreground/80">Confirm</span>
                        <input
                          type="password"
                          value={editConfirmPassword}
                          onChange={(event) => setEditConfirmPassword(event.target.value)}
                          className="mt-2 w-full rounded-2xl border border-earth-200 bg-slate-50 px-4 py-2.5 text-foreground outline-none focus:border-forest-500"
                          autoComplete="new-password"
                          required
                        />
                      </label>

                      <div className="flex gap-2">
                        <button
                          type="button"
                          onClick={saveNewPassword}
                          disabled={actionBusy}
                          className="rounded-xl bg-forest-600 px-4 py-2 text-white transition hover:bg-forest-700 disabled:cursor-not-allowed disabled:opacity-70"
                        >
                          {actionBusy ? 'Saving…' : 'Save'}
                        </button>
                        <button
                          type="button"
                          onClick={cancelEditPassword}
                          disabled={actionBusy}
                          className="rounded-xl bg-white px-4 py-2 text-foreground/70 border border-earth-200 hover:text-foreground disabled:cursor-not-allowed disabled:opacity-70"
                        >
                          Cancel
                        </button>
                      </div>
                    </>
                  ) : (
                    <>
                      <button
                        type="button"
                        onClick={() => startEditPassword(user.username)}
                        disabled={actionBusy}
                        className="rounded-xl bg-white px-4 py-2 text-foreground/70 border border-earth-200 hover:text-foreground disabled:cursor-not-allowed disabled:opacity-70"
                      >
                        Set password
                      </button>
                      <button
                        type="button"
                        onClick={() => deleteUser(user.username)}
                        disabled={actionBusy}
                        className="rounded-xl bg-red-600 px-4 py-2 text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-70"
                      >
                        Delete
                      </button>
                    </>
                  )}
                </div>
              </div>
            );
          })}
        </div>
      </div>

      <div className="rounded-3xl border border-earth-200 bg-white/50 p-5 sm:p-6">
        <h3 className="font-semibold text-foreground mb-4">Add admin account</h3>
        <form onSubmit={handleSubmit} className="space-y-4">
          <label className="block">
            <span className="text-sm font-medium text-foreground/80">Username</span>
            <input
              value={username}
              onChange={(event) => setUsername(event.target.value)}
              className="mt-2 w-full rounded-2xl border border-earth-200 bg-slate-50 px-4 py-3 text-foreground outline-none focus:border-forest-500"
              required
            />
          </label>
          <label className="block">
            <span className="text-sm font-medium text-foreground/80">Password</span>
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

          {toast ? (
            <div
              className={
                toast.kind === 'error'
                  ? 'rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900'
                  : 'rounded-2xl border border-forest-200 bg-forest-50 px-4 py-3 text-sm text-forest-900'
              }
            >
              {toast.message}
            </div>
          ) : null}

          <button
            type="submit"
            disabled={saving}
            className="rounded-xl bg-forest-600 px-4 py-2 text-white transition hover:bg-forest-700 disabled:cursor-not-allowed disabled:opacity-70"
          >
            {saving ? 'Adding…' : 'Add admin'}
          </button>
        </form>
      </div>
    </div>
  );
}

