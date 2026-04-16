import { Router } from 'express';
import { z } from 'zod';
import { authRequired, roleRequired } from '../middleware/auth.js';
import { attendance, createId, members } from '../store/memoryStore.js';

const router = Router();

const schema = z.object({
  memberId: z.string().min(1),
  date: z.iso.datetime(),
  serviceType: z.enum(['minggu', 'pemuda', 'doa']),
  hadir: z.boolean(),
  note: z.string().optional(),
});

router.use(authRequired);

router.get('/', (_req, res) => {
  const mapped = attendance.map((record) => ({
    ...record,
    member: members.find((member) => member.id === record.memberId) ?? null,
  }));
  return res.json(mapped);
});

router.post('/', roleRequired('admin', 'koordinator', 'pendeta'), (req, res) => {
  const parsed = schema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json(parsed.error.flatten());
  }

  if (!members.some((member) => member.id === parsed.data.memberId)) {
    return res.status(404).json({ message: 'Jemaat tidak ditemukan' });
  }

  const payload = {
    id: createId(),
    ...parsed.data,
  };

  attendance.push(payload);
  return res.status(201).json(payload);
});

router.get('/stats', (_req, res) => {
  const grouped = attendance.reduce<Record<string, { hadir: number; total: number }>>((acc, item) => {
    if (!acc[item.memberId]) {
      acc[item.memberId] = { hadir: 0, total: 0 };
    }
    acc[item.memberId].total += 1;
    if (item.hadir) acc[item.memberId].hadir += 1;
    return acc;
  }, {});

  const stats = Object.entries(grouped).map(([memberId, value]) => {
    const member = members.find((item) => item.id === memberId);
    const percentage = value.total === 0 ? 0 : Number(((value.hadir / value.total) * 100).toFixed(2));
    return {
      memberId,
      nama: member?.nama ?? 'Unknown',
      hadir: value.hadir,
      total: value.total,
      percentage,
    };
  });

  return res.json(stats);
});

export default router;
