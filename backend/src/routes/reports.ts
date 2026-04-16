import { Router } from 'express';
import { authRequired, roleRequired } from '../middleware/auth.js';
import { attendance, members } from '../store/memoryStore.js';

const router = Router();

router.use(authRequired, roleRequired('admin', 'pendeta', 'koordinator'));

router.get('/dashboard', (_req, res) => {
  const total = members.length;
  const aktif = members.filter((member) => member.status === 'aktif').length;
  const tidakAktif = total - aktif;

  const monthlyGrowth = members.reduce<Record<string, number>>((acc, member) => {
    const month = member.createdAt.slice(0, 7);
    acc[month] = (acc[month] ?? 0) + 1;
    return acc;
  }, {});

  const attendanceRate = attendance.length
    ? Number(((attendance.filter((item) => item.hadir).length / attendance.length) * 100).toFixed(2))
    : 0;

  return res.json({
    cards: { total, aktif, tidakAktif, attendanceRate },
    monthlyGrowth: Object.entries(monthlyGrowth).map(([month, value]) => ({ month, value })),
  });
});

router.get('/demografi', (_req, res) => {
  const gender = members.reduce<Record<string, number>>((acc, member) => {
    acc[member.jenisKelamin] = (acc[member.jenisKelamin] ?? 0) + 1;
    return acc;
  }, {});

  return res.json({
    gender: [
      { name: 'Laki-laki', value: gender.L ?? 0 },
      { name: 'Perempuan', value: gender.P ?? 0 },
    ],
  });
});

router.get('/pertumbuhan', (_req, res) => {
  const data = members.reduce<Record<string, number>>((acc, member) => {
    const year = member.createdAt.slice(0, 4);
    acc[year] = (acc[year] ?? 0) + 1;
    return acc;
  }, {});

  return res.json(Object.entries(data).map(([year, count]) => ({ year, count })));
});

router.get('/kehadiran', (_req, res) => {
  const byService = attendance.reduce<Record<string, number>>((acc, item) => {
    const key = item.serviceType;
    if (!item.hadir) return acc;
    acc[key] = (acc[key] ?? 0) + 1;
    return acc;
  }, {});

  return res.json(Object.entries(byService).map(([serviceType, total]) => ({ serviceType, total })));
});

export default router;
