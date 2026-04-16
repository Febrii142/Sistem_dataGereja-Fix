import { useEffect, useState } from 'react';
import { api } from '../lib/api';
import type { Attendance, Member } from '../types';

export const AttendancePage = () => {
  const [members, setMembers] = useState<Member[]>([]);
  const [attendance, setAttendance] = useState<Attendance[]>([]);
  const [form, setForm] = useState({ memberId: '', date: new Date().toISOString(), serviceType: 'minggu', hadir: true, note: '' });

  const load = async () => {
    const [memberResponse, attendanceResponse] = await Promise.all([api.get('/api/members'), api.get('/api/attendance')]);
    setMembers(memberResponse.data);
    setAttendance(attendanceResponse.data);
  };

  useEffect(() => {
    void Promise.all([api.get('/api/members'), api.get('/api/attendance')]).then(([memberResponse, attendanceResponse]) => {
      setMembers(memberResponse.data);
      setAttendance(attendanceResponse.data);
    });
  }, []);

  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">Manajemen Kehadiran</h1>

      <form
        className="grid gap-2 rounded-lg border p-3 md:grid-cols-2"
        onSubmit={async (event) => {
          event.preventDefault();
          await api.post('/api/attendance', form);
          setForm({ memberId: '', date: new Date().toISOString(), serviceType: 'minggu', hadir: true, note: '' });
          await load();
        }}
      >
        <h2 className="md:col-span-2 font-semibold">Pencatatan Kehadiran Ibadah</h2>
        <select className="rounded border px-3 py-2" value={form.memberId} onChange={(e) => setForm({ ...form, memberId: e.target.value })} required>
          <option value="">Pilih Jemaat</option>
          {members.map((member) => (
            <option value={member.id} key={member.id}>
              {member.nama}
            </option>
          ))}
        </select>
        <select className="rounded border px-3 py-2" value={form.serviceType} onChange={(e) => setForm({ ...form, serviceType: e.target.value as 'minggu' | 'pemuda' | 'doa' })}>
          <option value="minggu">Ibadah Minggu</option>
          <option value="pemuda">Ibadah Pemuda</option>
          <option value="doa">Persekutuan Doa</option>
        </select>
        <input type="datetime-local" className="rounded border px-3 py-2" onChange={(e) => setForm({ ...form, date: new Date(e.target.value).toISOString() })} />
        <label className="flex items-center gap-2 rounded border px-3 py-2">
          <input type="checkbox" checked={form.hadir} onChange={(e) => setForm({ ...form, hadir: e.target.checked })} />
          Hadir
        </label>
        <input className="md:col-span-2 rounded border px-3 py-2" placeholder="Catatan" value={form.note} onChange={(e) => setForm({ ...form, note: e.target.value })} />
        <button className="md:col-span-2 rounded bg-slate-900 px-3 py-2 text-white" type="submit">
          Simpan Kehadiran
        </button>
      </form>

      <div className="overflow-auto rounded-lg border">
        <table className="min-w-full text-sm">
          <thead className="bg-slate-50">
            <tr>
              <th className="px-3 py-2 text-left">Nama Jemaat</th>
              <th className="px-3 py-2 text-left">Tanggal</th>
              <th className="px-3 py-2 text-left">Ibadah</th>
              <th className="px-3 py-2 text-left">Hadir</th>
            </tr>
          </thead>
          <tbody>
            {attendance.map((item) => (
              <tr key={item.id} className="border-t">
                <td className="px-3 py-2">{item.member?.nama ?? '-'}</td>
                <td className="px-3 py-2">{new Date(item.date).toLocaleString('id-ID')}</td>
                <td className="px-3 py-2">{item.serviceType}</td>
                <td className="px-3 py-2">{item.hadir ? 'Ya' : 'Tidak'}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};
