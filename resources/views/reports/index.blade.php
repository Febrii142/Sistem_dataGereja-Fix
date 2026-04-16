@extends('layouts.app')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-2xl font-bold">Laporan & Analytics</h2>
    <a href="{{ route('reports.export.pdf') }}" class="rounded bg-rose-600 px-3 py-2 text-white">Export PDF</a>
</div>
<div class="grid gap-4 lg:grid-cols-2">
    <div class="rounded bg-white p-4 shadow">
        <h3 class="mb-2 font-semibold">Demografis Jemaat</h3>
        <ul class="text-sm">
            @foreach($demografi as $item)
                <li>{{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}: {{ $item->total }}</li>
            @endforeach
        </ul>
    </div>
    <div class="rounded bg-white p-4 shadow">
        <h3 class="mb-2 font-semibold">Pertumbuhan Jemaat</h3>
        <ul class="text-sm">
            @foreach($pertumbuhan as $item)
                <li>{{ $item->bulan }}: {{ $item->total }} jemaat</li>
            @endforeach
        </ul>
    </div>
</div>
<div class="mt-4 rounded bg-white p-4 shadow">
    <h3 class="mb-2 font-semibold">Laporan Kehadiran</h3>
    <table class="w-full text-sm">
        <thead><tr><th class="text-left">Tanggal</th><th class="text-left">Nama</th><th class="text-left">Status</th></tr></thead>
        <tbody>
        @foreach($kehadiran as $item)
            <tr><td>{{ $item->service_date->format('Y-m-d') }}</td><td>{{ $item->member->nama ?? '-' }}</td><td>{{ $item->hadir ? 'Hadir' : 'Tidak Hadir' }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
