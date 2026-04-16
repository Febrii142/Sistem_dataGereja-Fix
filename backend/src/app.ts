import cors from 'cors';
import express from 'express';
import helmet from 'helmet';
import morgan from 'morgan';
import attendanceRoutes from './routes/attendance.js';
import authRoutes from './routes/auth.js';
import membersRoutes from './routes/members.js';
import reportsRoutes from './routes/reports.js';
import usersRoutes from './routes/users.js';

export const createApp = () => {
  const app = express();

  app.use(helmet());
  app.use(cors());
  app.use(morgan('dev'));
  app.use(express.json({ limit: '2mb' }));

  app.get('/health', (_req, res) => {
    res.json({ status: 'ok' });
  });

  app.use('/api/auth', authRoutes);
  app.use('/api/members', membersRoutes);
  app.use('/api/attendance', attendanceRoutes);
  app.use('/api/reports', reportsRoutes);
  app.use('/api/users', usersRoutes);

  return app;
};
