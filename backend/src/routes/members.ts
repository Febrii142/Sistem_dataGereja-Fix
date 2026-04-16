import { Router } from 'express';
import ExcelJS from 'exceljs';
import multer from 'multer';
import PDFDocument from 'pdfkit';
import { z } from 'zod';
import { authRequired, roleRequired } from '../middleware/auth.js';
import { createId, members } from '../store/memoryStore.js';

const router = Router();
const upload = multer({ storage: multer.memoryStorage() });

const memberSchema = z.object({
  nama: z.string().min(2),
  alamat: z.string().min(5),
  kontak: z.string().min(8),
  status: z.enum(['aktif', 'tidak_aktif']),
  tanggalLahir: z.string().date(),
  jenisKelamin: z.enum(['L', 'P']),
  pekerjaan: z.string().optional(),
});

router.use(authRequired);

router.get('/', (req, res) => {
  const search = (req.query.search as string | undefined)?.toLowerCase();
  const status = req.query.status as 'aktif' | 'tidak_aktif' | undefined;

  const data = members.filter((member) => {
    const searchMatched =
      !search ||
      member.nama.toLowerCase().includes(search) ||
      member.alamat.toLowerCase().includes(search) ||
      member.kontak.includes(search);
    const statusMatched = !status || member.status === status;
    return searchMatched && statusMatched;
  });

  return res.json(data);
});

router.post('/', roleRequired('admin', 'koordinator'), (req, res) => {
  const parsed = memberSchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json(parsed.error.flatten());
  }

  const payload = {
    id: createId(),
    ...parsed.data,
    createdAt: new Date().toISOString(),
  };

  members.push(payload);
  return res.status(201).json(payload);
});

router.put('/:id', roleRequired('admin', 'koordinator'), (req, res) => {
  const parsed = memberSchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json(parsed.error.flatten());
  }

  const member = members.find((value) => value.id === req.params.id);
  if (!member) {
    return res.status(404).json({ message: 'Data jemaat tidak ditemukan' });
  }

  Object.assign(member, parsed.data);
  return res.json(member);
});

router.delete('/:id', roleRequired('admin'), (req, res) => {
  const index = members.findIndex((value) => value.id === req.params.id);
  if (index === -1) {
    return res.status(404).json({ message: 'Data jemaat tidak ditemukan' });
  }
  members.splice(index, 1);
  return res.status(204).send();
});

router.get('/export/excel', roleRequired('admin', 'koordinator', 'pendeta'), async (_req, res) => {
  const workbook = new ExcelJS.Workbook();
  const sheet = workbook.addWorksheet('Jemaat');
  sheet.columns = [
    { header: 'Nama', key: 'nama', width: 30 },
    { header: 'Alamat', key: 'alamat', width: 30 },
    { header: 'Kontak', key: 'kontak', width: 20 },
    { header: 'Status', key: 'status', width: 15 },
    { header: 'Tanggal Lahir', key: 'tanggalLahir', width: 18 },
  ];
  sheet.addRows(members);

  res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  res.setHeader('Content-Disposition', 'attachment; filename="data-jemaat.xlsx"');
  await workbook.xlsx.write(res);
  res.end();
});

router.get('/export/pdf', roleRequired('admin', 'koordinator', 'pendeta'), (_req, res) => {
  const doc = new PDFDocument({ margin: 30, size: 'A4' });
  res.setHeader('Content-Type', 'application/pdf');
  res.setHeader('Content-Disposition', 'attachment; filename="laporan-jemaat.pdf"');
  doc.pipe(res);

  doc.fontSize(16).text('Laporan Data Jemaat', { underline: true });
  doc.moveDown();
  members.forEach((member, index) => {
    doc
      .fontSize(11)
      .text(`${index + 1}. ${member.nama} - ${member.status.toUpperCase()} - ${member.kontak}`)
      .text(`   Alamat: ${member.alamat}`)
      .text(`   Tanggal Lahir: ${member.tanggalLahir}`)
      .moveDown(0.5);
  });

  doc.end();
});

router.post('/import', roleRequired('admin', 'koordinator'), upload.single('file'), (req, res) => {
  if (!req.file) {
    return res.status(400).json({ message: 'File wajib diunggah' });
  }

  const content = req.file.buffer.toString('utf8').trim();
  if (!content) {
    return res.status(400).json({ message: 'File kosong' });
  }

  const lines = content.split('\n');
  const imported = lines
    .slice(1)
    .map((line) => line.split(','))
    .map((parts) => ({
      nama: parts[0]?.trim() ?? '',
      alamat: parts[1]?.trim() ?? '',
      kontak: parts[2]?.trim() ?? '',
      status: (parts[3]?.trim() as 'aktif' | 'tidak_aktif') ?? 'aktif',
      tanggalLahir: parts[4]?.trim() ?? '1990-01-01',
      jenisKelamin: (parts[5]?.trim() as 'L' | 'P') ?? 'L',
      pekerjaan: parts[6]?.trim(),
    }))
    .filter((item) => memberSchema.safeParse(item).success)
    .map((item) => ({ id: createId(), ...item, createdAt: new Date().toISOString() }));

  members.push(...imported);
  return res.json({ imported: imported.length });
});

export default router;
