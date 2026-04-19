@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="rounded-xl bg-white p-6 shadow">
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 class="text-2xl font-bold text-blue-900">Profil Jemaat</h2>
            <a href="{{ route('jemaat.profile.edit') }}" class="inline-flex rounded bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">Edit Profil</a>
        </div>

        <div class="grid gap-3 text-sm md:grid-cols-2">
            <p><span class="font-semibold">Nama Jemaat:</span> {{ $jemaat->nama_lengkap }}</p>
            <p><span class="font-semibold">No. Identitas (KTP/SIM):</span> {{ $jemaat->no_identitas ?? '-' }}</p>
            <p><span class="font-semibold">Tanggal Lahir:</span> {{ $jemaat->tanggal_lahir ? \Illuminate\Support\Carbon::parse($jemaat->tanggal_lahir)->format('d M Y') : '-' }}</p>
            <p><span class="font-semibold">Jenis Kelamin:</span> {{ $jemaat->jenis_kelamin === 'L' ? 'Laki-laki' : ($jemaat->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</p>
            <p><span class="font-semibold">Nomor Telepon:</span> {{ $jemaat->no_telepon ?: '-' }}</p>
            <p><span class="font-semibold">Status Pernikahan:</span> {{ $jemaat->status_perkawinan ?? '-' }}</p>
            <p class="md:col-span-2"><span class="font-semibold">Alamat:</span> {{ $jemaat->alamat ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
