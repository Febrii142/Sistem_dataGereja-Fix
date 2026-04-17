@extends('layouts.app')

@section('content')
<div class="max-w-3xl space-y-4 rounded-xl bg-white p-6 shadow">
    <h2 class="text-2xl font-bold text-blue-900">Profil Jemaat</h2>
    <div class="grid gap-3 md:grid-cols-2">
        <p><span class="font-semibold">Nama:</span> {{ $jemaat->nama_lengkap }}</p>
        <p><span class="font-semibold">TTL:</span> {{ $jemaat->tempat_lahir }}, {{ \Illuminate\Support\Carbon::parse($jemaat->tanggal_lahir)->format('d M Y') }}</p>
        <p><span class="font-semibold">No Telepon:</span> {{ $jemaat->no_telepon }}</p>
        <p><span class="font-semibold">Email:</span> {{ $jemaat->email }}</p>
        <p class="md:col-span-2"><span class="font-semibold">Alamat:</span> {{ $jemaat->alamat ?? '-' }}, {{ $jemaat->kota ?? '-' }} {{ $jemaat->kode_pos ?? '' }}</p>
        <p><span class="font-semibold">Status Baptis:</span> {{ ucfirst($jemaat->status_baptis) }}</p>
        <p><span class="font-semibold">Tanggal Baptis:</span> {{ $jemaat->tanggal_baptis ? \Illuminate\Support\Carbon::parse($jemaat->tanggal_baptis)->format('d M Y') : '-' }}</p>
    </div>
    <a href="{{ route('jemaat.profile') }}" class="inline-flex rounded bg-blue-700 px-4 py-2 font-semibold text-white">Edit Profil</a>
</div>
@endsection
