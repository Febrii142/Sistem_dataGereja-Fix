import { Router } from 'express';
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import { z } from 'zod';
import { env } from '../config/env.js';
import { authRequired, currentUser } from '../middleware/auth.js';
import { allowedRoles, createId, safeUser, users } from '../store/memoryStore.js';

const router = Router();

const registerSchema = z.object({
  name: z.string().min(2),
  email: z.email(),
  password: z.string().min(8),
  role: z.enum(allowedRoles).default('user'),
});

const loginSchema = z.object({
  email: z.email(),
  password: z.string().min(1),
});

router.post('/register', async (req, res) => {
  const parsed = registerSchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json(parsed.error.flatten());
  }

  if (users.some((user) => user.email === parsed.data.email)) {
    return res.status(409).json({ message: 'Email sudah terdaftar' });
  }

  const passwordHash = await bcrypt.hash(parsed.data.password, 10);
  const user = {
    id: createId(),
    name: parsed.data.name,
    email: parsed.data.email,
    role: parsed.data.role,
    passwordHash,
    createdAt: new Date().toISOString(),
  };
  users.push(user);

  return res.status(201).json(safeUser(user));
});

router.post('/login', async (req, res) => {
  const parsed = loginSchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json(parsed.error.flatten());
  }

  const user = users.find((value) => value.email === parsed.data.email);
  if (!user) {
    return res.status(401).json({ message: 'Email/password salah' });
  }

  const isMatch = await bcrypt.compare(parsed.data.password, user.passwordHash);
  if (!isMatch) {
    return res.status(401).json({ message: 'Email/password salah' });
  }

  const token = jwt.sign({ role: user.role, email: user.email }, env.JWT_SECRET, {
    subject: user.id,
    expiresIn: '8h',
  });

  return res.json({ token, user: safeUser(user) });
});

router.get('/me', authRequired, currentUser);

export default router;
