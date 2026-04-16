import { Router } from 'express';
import { z } from 'zod';
import { authRequired, roleRequired } from '../middleware/auth.js';
import { allowedRoles, safeUser, users } from '../store/memoryStore.js';

const router = Router();

const updateSchema = z.object({
  name: z.string().min(2).optional(),
  role: z.enum(allowedRoles).optional(),
});

router.use(authRequired, roleRequired('admin', 'pendeta'));

router.get('/', (_req, res) => {
  return res.json(users.map(safeUser));
});

router.patch('/:id', (req, res) => {
  const parsed = updateSchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json(parsed.error.flatten());
  }

  const user = users.find((value) => value.id === req.params.id);
  if (!user) {
    return res.status(404).json({ message: 'User tidak ditemukan' });
  }

  if (parsed.data.name) user.name = parsed.data.name;
  if (parsed.data.role) user.role = parsed.data.role;

  return res.json(safeUser(user));
});

export default router;
