import { useEffect, useState } from 'react';
import { api } from '../lib/api';
import type { AuthUser, Role } from '../types';

const roleOptions: Role[] = ['admin', 'pendeta', 'koordinator', 'user'];

export const UsersPage = () => {
  const [users, setUsers] = useState<AuthUser[]>([]);

  const load = async () => {
    const response = await api.get('/api/users');
    setUsers(response.data);
  };

  useEffect(() => {
    void api.get('/api/users').then((response) => setUsers(response.data));
  }, []);

  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">User Management</h1>
      <p className="text-sm text-slate-600">Role-based access: Admin, Pendeta, Koordinator, User Biasa.</p>
      <div className="overflow-auto rounded-lg border">
        <table className="min-w-full text-sm">
          <thead className="bg-slate-50">
            <tr>
              <th className="px-3 py-2 text-left">Nama</th>
              <th className="px-3 py-2 text-left">Email</th>
              <th className="px-3 py-2 text-left">Role</th>
            </tr>
          </thead>
          <tbody>
            {users.map((user) => (
              <tr className="border-t" key={user.id}>
                <td className="px-3 py-2">{user.name}</td>
                <td className="px-3 py-2">{user.email}</td>
                <td className="px-3 py-2">
                  <select
                    className="rounded border px-2 py-1"
                    value={user.role}
                    onChange={async (event) => {
                      const role = event.target.value as Role;
                      await api.patch(`/api/users/${user.id}`, { role });
                      await load();
                    }}
                  >
                    {roleOptions.map((role) => (
                      <option key={role} value={role}>
                        {role}
                      </option>
                    ))}
                  </select>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};
