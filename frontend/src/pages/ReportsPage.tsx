import { useEffect, useState } from 'react';
import { Pie, PieChart, ResponsiveContainer, Tooltip } from 'recharts';
import { api } from '../lib/api';

interface PieData {
  name: string;
  value: number;
}

export const ReportsPage = () => {
  const [demografi, setDemografi] = useState<PieData[]>([]);
  const [pertumbuhan, setPertumbuhan] = useState<{ year: string; count: number }[]>([]);
  const [kehadiran, setKehadiran] = useState<{ serviceType: string; total: number }[]>([]);

  useEffect(() => {
    Promise.all([api.get('/api/reports/demografi'), api.get('/api/reports/pertumbuhan'), api.get('/api/reports/kehadiran')]).then(
      ([demografiResponse, pertumbuhanResponse, kehadiranResponse]) => {
        setDemografi(demografiResponse.data.gender);
        setPertumbuhan(pertumbuhanResponse.data);
        setKehadiran(kehadiranResponse.data);
      },
    );
  }, []);

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Laporan & Analytics</h1>

      <section className="grid gap-3 lg:grid-cols-2">
        <article className="rounded-lg border p-4">
          <h2 className="font-semibold">Laporan Demografis Jemaat</h2>
          <div className="h-56">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie data={demografi} dataKey="value" nameKey="name" outerRadius={80} fill="#0f172a" label />
                <Tooltip />
              </PieChart>
            </ResponsiveContainer>
          </div>
        </article>

        <article className="rounded-lg border p-4">
          <h2 className="font-semibold">Laporan Pertumbuhan Jemaat</h2>
          <ul className="mt-2 list-disc pl-5 text-sm">
            {pertumbuhan.map((item) => (
              <li key={item.year}>
                Tahun {item.year}: {item.count} jemaat baru
              </li>
            ))}
          </ul>
        </article>
      </section>

      <article className="rounded-lg border p-4">
        <h2 className="font-semibold">Laporan Kehadiran</h2>
        <ul className="mt-2 list-disc pl-5 text-sm">
          {kehadiran.map((item) => (
            <li key={item.serviceType}>
              {item.serviceType}: {item.total} kehadiran
            </li>
          ))}
        </ul>
        <a className="mt-3 inline-block rounded border px-3 py-2" href="http://localhost:4000/api/members/export/pdf" target="_blank" rel="noreferrer">
          Export Laporan PDF
        </a>
      </article>
    </div>
  );
};
