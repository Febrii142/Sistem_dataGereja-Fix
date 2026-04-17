@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="rounded-xl bg-white p-6 shadow">
        <h2 class="text-2xl font-bold text-blue-900">Dashboard Jemaat</h2>
        <p class="mt-1 text-sm text-slate-600">Informasi profil dan status keanggotaan Anda.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow">
            <p class="text-sm text-slate-500">Nama Jemaat</p>
            <p class="mt-2 text-lg font-semibold">{{ $jemaat->nama_lengkap }}</p>
            <p class="text-sm text-slate-500">{{ $jemaat->email }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow">
            <p class="text-sm text-slate-500">Status Baptis</p>
            <p class="mt-2 inline-flex rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800">{{ ucfirst($jemaat->status_baptis) }}</p>
            <p class="mt-2 text-sm text-slate-600">Tanggal: {{ $jemaat->tanggal_baptis ? \Illuminate\Support\Carbon::parse($jemaat->tanggal_baptis)->format('d M Y') : '-' }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow">
            <p class="text-sm text-slate-500">Keluarga</p>
            <p class="mt-2 text-lg font-semibold">{{ $anggotaKeluarga->count() }} Anggota</p>
            <p class="text-sm text-slate-600">Peran: {{ $isKepalaKeluarga ? 'Kepala Keluarga' : 'Anggota Keluarga' }}</p>
        </div>
    </div>

    <div class="grid gap-3 md:grid-cols-3">
        <a href="{{ route('jemaat.profile.edit') }}" class="rounded-lg bg-blue-700 px-4 py-3 text-center font-semibold text-white hover:bg-blue-800">Edit Profil</a>
        <a href="{{ route('jemaat.keluarga.index') }}" class="rounded-lg bg-blue-100 px-4 py-3 text-center font-semibold text-blue-800 hover:bg-blue-200">Lihat Keluarga</a>
        <a href="{{ route('jemaat.registration.show', 1) }}" class="rounded-lg bg-slate-900 px-4 py-3 text-center font-semibold text-white hover:bg-slate-800">Lengkapi Form</a>
    </div>
</div>
@endsection
