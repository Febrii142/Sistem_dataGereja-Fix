@extends('layouts.app')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-2xl font-bold">Manajemen Kehadiran</h2>
    <a href="{{ route('attendances.create') }}" class="rounded bg-slate-900 px-3 py-2 text-white">Input Kehadiran</a>
</div>
<div class="mb-4 grid gap-4 md:grid-cols-3">
    <div class="rounded bg-white p-4 shadow">Hadir: <strong>{{ $stats['hadir'] }}</strong></div>
    <div class="rounded bg-white p-4 shadow">Tidak Hadir: <strong>{{ $stats['tidak_hadir'] }}</strong></div>
    <div class="rounded bg-white p-4 shadow">Total Record: <strong>{{ $stats['total'] }}</strong></div>
</div>
<form method="get" class="mb-4 grid gap-2 md:grid-cols-4">
    <select name="member_id" class="rounded border px-3 py-2">
        <option value="">Semua Jemaat</option>
        @foreach($members as $member)
            <option value="{{ $member->id }}" @selected((string)request('member_id')===(string)$member->id)>{{ $member->nama }}</option>
        @endforeach
    </select>
    <input type="date" name="service_date" value="{{ request('service_date') }}" class="rounded border px-3 py-2">
    <button class="rounded bg-blue-600 px-3 py-2 text-white">Filter</button>
</form>
<div class="overflow-x-auto rounded bg-white shadow">
    <table class="w-full text-sm">
        <thead class="bg-slate-50"><tr><th class="p-3 text-left">Tanggal</th><th class="p-3 text-left">Jemaat</th><th class="p-3 text-left">Status</th><th class="p-3"></th></tr></thead>
        <tbody>
        @forelse($attendances as $attendance)
            <tr class="border-t"><td class="p-3">{{ $attendance->service_date->format('Y-m-d') }}</td><td class="p-3">{{ $attendance->member->nama }}</td><td class="p-3">{{ $attendance->hadir ? 'Hadir' : 'Tidak Hadir' }}</td><td class="p-3 text-right"><a class="text-amber-600" href="{{ route('attendances.edit', $attendance) }}">Edit</a> <form method="post" action="{{ route('attendances.destroy', $attendance) }}" class="inline">@csrf @method('DELETE')<button class="text-rose-600" onclick="return confirm('Hapus data?')">Hapus</button></form></td></tr>
        @empty
            <tr><td colspan="4" class="p-3 text-center">Belum ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $attendances->links() }}</div>
@endsection
