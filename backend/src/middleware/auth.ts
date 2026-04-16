import type { NextFunction, Request, Response } from 'express';
import jwt from 'jsonwebtoken';
import { env } from '../config/env.js';
import { users } from '../store/memoryStore.js';
import type { Role } from '../types.js';

declare module 'express-serve-static-core' {
  interface Request {
    user?: {
      id: string;
      role: Role;
      email: string;
    };
  }
}

export const authRequired = (req: Request, res: Response, next: NextFunction) => {
  const authHeader = req.headers.authorization;
  if (!authHeader?.startsWith('Bearer ')) {
    return res.status(401).json({ message: 'Token tidak valid' });
  }

  const token = authHeader.split(' ')[1];
  try {
    const payload = jwt.verify(token, env.JWT_SECRET) as { sub: string; role: Role; email: string };
    req.user = { id: payload.sub, role: payload.role, email: payload.email };
    return next();
  } catch {
    return res.status(401).json({ message: 'Token tidak valid' });
  }
};

export const roleRequired = (...roles: Role[]) => {
  return (req: Request, res: Response, next: NextFunction) => {
    if (!req.user) {
      return res.status(401).json({ message: 'Unauthenticated' });
    }
    if (!roles.includes(req.user.role)) {
      return res.status(403).json({ message: 'Akses ditolak' });
    }
    return next();
  };
};

export const currentUser = (req: Request, res: Response) => {
  if (!req.user) {
    return res.status(401).json({ message: 'Unauthenticated' });
  }
  const user = users.find((value) => value.id === req.user?.id);
  if (!user) {
    return res.status(404).json({ message: 'User tidak ditemukan' });
  }
  const { passwordHash, ...safe } = user;
  return res.json(safe);
};
