<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Demografi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h2 { margin: 0 0 6px; }
        .muted { color: #6b7280; margin-bottom: 16px; }
        .card { border-left: 5px solid #3b82f6; padding: 8px 12px; margin-bottom: 12px; background: #f8fafc; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .section-title { margin-top: 18px; margin-bottom: 6px; font-weight: 700; }
        ul { margin: 6px 0 0; padding-left: 18px; }
    </style>
</head>
<body>
    <h2>ANALISIS DEMOGRAFI JEMAAT</h2>
    <p class="muted">Filter: Rentang Usia {{ $filters['age_range'] ?: 'Semua' }}, Gender {{ $filters['gender'] ?: 'Semua' }}, Bulan Ulang Tahun {{ $filters['birthday_month'] ?: 'Semua' }}</p>

    <div class="card">
        <strong>Total Hasil Filter:</strong> {{ number_format($totalResults) }} jemaat
    </div>

    <p class="section-title">Distribusi Sektor</p>
    <ul>
        @forelse($distribution as $item)
            <li>{{ $item->label }}: {{ $item->total }} ({{ $item->percentage }}%)</li>
        @empty
            <li>Tidak ada data distribusi.</li>
        @endforelse
    </ul>

    <p class="section-title">Direktori Hasil</p>
    <table>
        <thead>
            <tr>
                <th>NAMA LENGKAP</th>
                <th>USIA</th>
                <th>L/P</th>
                <th>TGL LAHIR</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr>
                    <td>{{ $member->nama }}</td>
                    <td>{{ $member->tanggal_lahir ? \Illuminate\Support\Carbon::parse($member->tanggal_lahir)->age : '-' }}</td>
                    <td>{{ $member->jenis_kelamin }}</td>
                    <td>{{ $member->tanggal_lahir ? \Illuminate\Support\Carbon::parse($member->tanggal_lahir)->format('d-m-Y') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Tidak ada data sesuai filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
