@extends('layouts.app')
@section('content')
<h2 class="mb-4 text-2xl font-bold">Dashboard Admin</h2>
<div class="grid gap-4 md:grid-cols-4">
    <div class="rounded bg-white p-4 shadow"><p class="text-sm text-slate-500">Total Jemaat</p><p class="text-2xl font-bold">{{ $totalJemaat }}</p></div>
    <div class="rounded bg-white p-4 shadow"><p class="text-sm text-slate-500">Aktif</p><p class="text-2xl font-bold text-emerald-600">{{ $aktif }}</p></div>
    <div class="rounded bg-white p-4 shadow"><p class="text-sm text-slate-500">Tidak Aktif</p><p class="text-2xl font-bold text-rose-600">{{ $tidakAktif }}</p></div>
    <div class="rounded bg-white p-4 shadow"><p class="text-sm text-slate-500">Quick Actions</p><div class="mt-2 flex flex-wrap gap-2 text-xs"><a class="rounded bg-slate-900 px-2 py-1 text-white" href="{{ route('members.create') }}">Tambah Jemaat</a><a class="rounded bg-blue-600 px-2 py-1 text-white" href="{{ route('attendances.create') }}">Input Kehadiran</a></div></div>
</div>
<div class="mt-6 grid gap-4 lg:grid-cols-2">
    <div class="rounded bg-white p-4 shadow">
        <h3 class="mb-3 font-semibold">Grafik Pertumbuhan Jemaat Bulanan</h3>
        <div class="space-y-2">
            @forelse($monthlyGrowth as $item)
                <div>
                    <div class="mb-1 flex justify-between text-xs"><span>{{ $item['month'] }}</span><span>{{ $item['total'] }}</span></div>
                    <div class="h-2 rounded bg-slate-200"><div class="h-2 rounded bg-slate-900" style="width: {{ min(100, $item['total'] * 20) }}%"></div></div>
                </div>
            @empty
                <p class="text-sm text-slate-500">Belum ada data pertumbuhan.</p>
            @endforelse
        </div>
    </div>
    <div class="rounded bg-white p-4 shadow">
        <h3 class="mb-3 font-semibold">Grafik Rata-rata Kehadiran (%)</h3>
        <div class="space-y-2">
            @forelse($attendanceStats as $item)
                <div>
                    <div class="mb-1 flex justify-between text-xs"><span>{{ $item['month'] }}</span><span>{{ $item['attendance_rate'] }}%</span></div>
                    <div class="h-2 rounded bg-slate-200"><div class="h-2 rounded bg-blue-600" style="width: {{ min(100, $item['attendance_rate']) }}%"></div></div>
                </div>
            @empty
                <p class="text-sm text-slate-500">Belum ada data kehadiran.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
