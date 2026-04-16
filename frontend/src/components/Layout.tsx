import { Link, NavLink, Outlet } from 'react-router-dom';
import { useAuth } from '../lib/useAuth';

const menus = [
  { to: '/', label: 'Dashboard' },
  { to: '/jemaat', label: 'Data Jemaat' },
  { to: '/kehadiran', label: 'Kehadiran' },
  { to: '/users', label: 'User Management' },
  { to: '/laporan', label: 'Laporan & Analytics' },
];

export const Layout = () => {
  const { user, logout } = useAuth();

  return (
    <div className="min-h-screen bg-slate-100">
      <div className="mx-auto grid max-w-7xl grid-cols-1 gap-4 p-4 lg:grid-cols-[240px_1fr]">
        <aside className="rounded-xl bg-slate-900 p-4 text-white">
          <Link to="/" className="mb-6 block text-lg font-bold">
            SIM Jemaat
          </Link>
          <nav className="space-y-2">
            {menus.map((menu) => (
              <NavLink
                key={menu.to}
                to={menu.to}
                end={menu.to === '/'}
                className={({ isActive }) =>
                  `block rounded-lg px-3 py-2 text-sm ${isActive ? 'bg-white/20 font-semibold' : 'hover:bg-white/10'}`
                }
              >
                {menu.label}
              </NavLink>
            ))}
          </nav>
          <div className="mt-8 rounded-lg bg-white/10 p-3 text-xs">
            <p className="font-semibold">{user?.name}</p>
            <p className="opacity-80">{user?.role}</p>
            <button type="button" onClick={logout} className="mt-3 rounded bg-white px-3 py-1 text-slate-900">
              Logout
            </button>
          </div>
        </aside>
        <main className="rounded-xl bg-white p-4 shadow-sm">
          <Outlet />
        </main>
      </div>
    </div>
  );
};
