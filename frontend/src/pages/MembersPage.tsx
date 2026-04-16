import { useEffect, useMemo, useState } from 'react';
import { api } from '../lib/api';
import type { Member } from '../types';

const emptyForm = {
  nama: '',
  alamat: '',
  kontak: '',
  status: 'aktif',
  tanggalLahir: '1990-01-01',
  jenisKelamin: 'L',
  pekerjaan: '',
};

export const MembersPage = () => {
  const [items, setItems] = useState<Member[]>([]);
  const [search, setSearch] = useState('');
  const [form, setForm] = useState(emptyForm);

  const load = async (keyword = search) => {
    const response = await api.get('/api/members', { params: { search: keyword } });
    setItems(response.data);
  };

  useEffect(() => {
    void api.get('/api/members').then((response) => setItems(response.data));
  }, []);

  const activeCount = useMemo(() => items.filter((item) => item.status === 'aktif').length, [items]);

  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">Manajemen Data Jemaat</h1>
      <p className="text-sm text-slate-600">
        Total: {items.length} | Aktif: {activeCount} | Tidak Aktif: {items.length - activeCount}
      </p>
      <div className="flex flex-wrap gap-2">
        <input
          className="rounded border px-3 py-2"
          placeholder="Cari nama/alamat/kontak"
          value={search}
          onChange={(event) => setSearch(event.target.value)}
        />
        <button type="button" className="rounded bg-slate-900 px-3 py-2 text-white" onClick={() => void load(search)}>
          Cari
        </button>
        <a className="rounded border px-3 py-2" href="http://localhost:4000/api/members/export/excel" target="_blank" rel="noreferrer">
          Export Excel
        </a>
        <a className="rounded border px-3 py-2" href="http://localhost:4000/api/members/export/pdf" target="_blank" rel="noreferrer">
          Export PDF
        </a>
      </div>

      <form
        className="grid gap-2 rounded-lg border p-3 md:grid-cols-2"
        onSubmit={async (event) => {
          event.preventDefault();
          await api.post('/api/members', form);
          setForm(emptyForm);
          await load('');
        }}
      >
        <h2 className="md:col-span-2 font-semibold">Input Data Jemaat</h2>
        <input className="rounded border px-3 py-2" placeholder="Nama" value={form.nama} onChange={(e) => setForm({ ...form, nama: e.target.value })} />
        <input className="rounded border px-3 py-2" placeholder="Alamat" value={form.alamat} onChange={(e) => setForm({ ...form, alamat: e.target.value })} />
        <input className="rounded border px-3 py-2" placeholder="Kontak" value={form.kontak} onChange={(e) => setForm({ ...form, kontak: e.target.value })} />
        <input type="date" className="rounded border px-3 py-2" value={form.tanggalLahir} onChange={(e) => setForm({ ...form, tanggalLahir: e.target.value })} />
        <select className="rounded border px-3 py-2" value={form.status} onChange={(e) => setForm({ ...form, status: e.target.value as 'aktif' | 'tidak_aktif' })}>
          <option value="aktif">Aktif</option>
          <option value="tidak_aktif">Tidak Aktif</option>
        </select>
        <select className="rounded border px-3 py-2" value={form.jenisKelamin} onChange={(e) => setForm({ ...form, jenisKelamin: e.target.value as 'L' | 'P' })}>
          <option value="L">Laki-laki</option>
          <option value="P">Perempuan</option>
        </select>
        <button className="md:col-span-2 rounded bg-slate-900 px-3 py-2 text-white" type="submit">
          Simpan Jemaat
        </button>
      </form>

      <div className="overflow-auto rounded-lg border">
        <table className="min-w-full text-sm">
          <thead className="bg-slate-50">
            <tr>
              <th className="px-3 py-2 text-left">Nama</th>
              <th className="px-3 py-2 text-left">Alamat</th>
              <th className="px-3 py-2 text-left">Kontak</th>
              <th className="px-3 py-2 text-left">Status</th>
              <th className="px-3 py-2 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {items.map((item) => (
              <tr key={item.id} className="border-t">
                <td className="px-3 py-2">{item.nama}</td>
                <td className="px-3 py-2">{item.alamat}</td>
                <td className="px-3 py-2">{item.kontak}</td>
                <td className="px-3 py-2">{item.status}</td>
                <td className="px-3 py-2">
                  <button
                    className="rounded bg-red-600 px-2 py-1 text-white"
                    onClick={async () => {
                      await api.delete(`/api/members/${item.id}`);
                      await load('');
                    }}
                    type="button"
                  >
                    Hapus
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};
