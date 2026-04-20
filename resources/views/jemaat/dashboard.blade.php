@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if(auth()->user()?->status === 'pending')
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-amber-800">
            <p class="font-semibold">Menunggu Approval dari Staff Gereja</p>
            <p class="text-sm">Akun Anda masih pending. Akses saat ini read-only sampai disetujui.</p>
        </div>
    @endif

    <div class="rounded-xl bg-white p-6 shadow">
        <h2 class="text-2xl font-bold text-blue-900">Dashboard Jemaat</h2>
        <p class="mt-1 text-sm text-slate-600">Shalom, {{ $jemaat->nama_lengkap }}. Ringkasan akun jemaat Anda ada di sini.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow">
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.72 9.72 0 00-12 0m12 0a9.72 9.72 0 013 7.28m-15 0a9.72 9.72 0 013-7.28m9-11.44a6 6 0 11-12 0 6 6 0 0112 0z" /></svg>
                <p>Profil Jemaat</p>
            </div>
            <p class="mt-2 text-lg font-semibold">{{ $jemaat->nama_lengkap }}</p>
            <p class="text-sm text-slate-500">{{ $jemaat->email }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow">
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0Z" /></svg>
                <p>Statistik Kehadiran</p>
            </div>
            <p class="mt-2 text-lg font-semibold">{{ $attendanceCount ?? 0 }} Kehadiran</p>
            <p class="mt-2 text-sm text-slate-600">Status Baptis: {{ ucfirst($jemaat->status_baptis) }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow">
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.5V18a3 3 0 00-3-3H6a3 3 0 00-3 3v1.5m18-1.5V18a3 3 0 00-2.25-2.906m-3.75-8.344a3 3 0 11-6 0 3 3 0 016 0Zm6 8.344A3 3 0 0018 9m0 0a3 3 0 013 3v.75" /></svg>
                <p>Keluarga Jemaat</p>
            </div>
            <p class="mt-2 text-lg font-semibold">{{ $familyCount ?? $anggotaKeluarga->count() }} Anggota</p>
            <p class="text-sm text-slate-600">Peran: {{ ($isKepalaKeluarga ?? true) ? 'Kepala Keluarga' : 'Anggota Keluarga' }}</p>
        </div>
    </div>

    <div class="rounded-xl bg-white p-6 shadow">
        <h3 class="text-lg font-semibold text-blue-900">Agenda Mendatang</h3>
        <ul class="mt-3 space-y-2 text-sm text-slate-600">
            @forelse(($upcomingEvents ?? collect()) as $event)
                <li class="rounded-lg bg-slate-50 px-3 py-2">{{ $event['title'] }} - {{ \Illuminate\Support\Carbon::parse($event['date'])->format('d M Y') }}</li>
            @empty
                <li class="rounded-lg bg-slate-50 px-3 py-2">Belum ada agenda terjadwal.</li>
            @endforelse
        </ul>
    </div>

    @if(auth()->user()?->status === 'approved')
        <div class="grid gap-3 md:grid-cols-3">
            <a href="{{ route('jemaat.profile') }}" class="rounded-lg bg-blue-700 px-4 py-3 text-center font-semibold text-white hover:bg-blue-800">Kelola Profil</a>
            <a href="{{ route('jemaat.family') }}" class="rounded-lg bg-blue-100 px-4 py-3 text-center font-semibold text-blue-800 hover:bg-blue-200">Kelola Keluarga</a>
            <a href="{{ route('jemaat.registration.show', 1) }}" class="rounded-lg bg-slate-900 px-4 py-3 text-center font-semibold text-white hover:bg-slate-800">Form Pendaftaran</a>
        </div>
    @endif
</div>
@endsection
