import { useMemo, useState } from 'react';
import type { ReactNode } from 'react';
import { api } from './api';
import { AuthContext } from './auth-context';
import type { AuthUser } from '../types';

export const AuthProvider = ({ children }: { children: ReactNode }) => {
  const [user, setUser] = useState<AuthUser | null>(() => {
    const raw = localStorage.getItem('user');
    return raw ? (JSON.parse(raw) as AuthUser) : null;
  });

  const value = useMemo(
    () => ({
      user,
      async login(email: string, password: string) {
        const response = await api.post('/api/auth/login', { email, password });
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        setUser(response.data.user);
      },
      logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        setUser(null);
      },
    }),
    [user],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};
