import { useEffect, useState } from 'react';
import { Bar, BarChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { api } from '../lib/api';

interface DashboardResponse {
  cards: {
    total: number;
    aktif: number;
    tidakAktif: number;
    attendanceRate: number;
  };
  monthlyGrowth: { month: string; value: number }[];
}

export const DashboardPage = () => {
  const [data, setData] = useState<DashboardResponse | null>(null);

  useEffect(() => {
    api.get('/api/reports/dashboard').then((response) => setData(response.data));
  }, []);

  const cards = [
    { label: 'Total Jemaat', value: data?.cards.total ?? 0 },
    { label: 'Jemaat Aktif', value: data?.cards.aktif ?? 0 },
    { label: 'Jemaat Tidak Aktif', value: data?.cards.tidakAktif ?? 0 },
    { label: 'Rasio Kehadiran', value: `${data?.cards.attendanceRate ?? 0}%` },
  ];

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Dashboard Admin</h1>
      <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        {cards.map((card) => (
          <article key={card.label} className="rounded-lg border p-4">
            <p className="text-sm text-slate-500">{card.label}</p>
            <p className="mt-1 text-2xl font-bold">{card.value}</p>
          </article>
        ))}
      </div>

      <section className="rounded-lg border p-4">
        <h2 className="mb-3 font-semibold">Pertumbuhan Jemaat Bulanan</h2>
        <div className="h-72">
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={data?.monthlyGrowth ?? []}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip />
              <Bar dataKey="value" fill="#0f172a" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </section>

      <section className="grid gap-3 md:grid-cols-3">
        <button className="rounded-lg border p-3 text-left">+ Tambah Data Jemaat</button>
        <button className="rounded-lg border p-3 text-left">+ Catat Kehadiran Ibadah</button>
        <button className="rounded-lg border p-3 text-left">+ Buat Laporan Cepat</button>
      </section>
    </div>
  );
};
