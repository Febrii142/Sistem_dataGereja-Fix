import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../lib/useAuth';

export const LoginPage = () => {
  const navigate = useNavigate();
  const { login } = useAuth();
  const [email, setEmail] = useState('admin@gereja.local');
  const [password, setPassword] = useState('Admin123!');
  const [error, setError] = useState('');

  return (
    <div className="flex min-h-screen items-center justify-center bg-slate-100 px-4">
      <form
        className="w-full max-w-md space-y-4 rounded-xl bg-white p-6 shadow"
        onSubmit={async (event) => {
          event.preventDefault();
          try {
            setError('');
            await login(email, password);
            navigate('/');
          } catch {
            setError('Login gagal, periksa email/password.');
          }
        }}
      >
        <h1 className="text-xl font-bold">Login Sistem Informasi Jemaat</h1>
        {error ? <p className="text-sm text-red-600">{error}</p> : null}
        <label className="block text-sm">
          Email
          <input className="mt-1 w-full rounded border px-3 py-2" value={email} onChange={(e) => setEmail(e.target.value)} />
        </label>
        <label className="block text-sm">
          Password
          <input
            type="password"
            className="mt-1 w-full rounded border px-3 py-2"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />
        </label>
        <button className="w-full rounded bg-slate-900 py-2 text-white" type="submit">
          Masuk
        </button>
      </form>
    </div>
  );
};
