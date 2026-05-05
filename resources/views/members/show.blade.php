@extends('layouts.app')
@section('content')
<h2 class="mb-4 text-2xl font-bold">Detail Jemaat</h2>
<div class="rounded bg-white p-4 shadow">
    @php
        $genderClass = $member->jenis_kelamin === 'P'
            ? 'bg-rose-100 text-rose-700'
            : 'bg-cyan-100 text-cyan-700';
        $genderLabel = $member->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    @endphp
    <p><strong>Nama:</strong> {{ $member->nama }}</p>
    <p><strong>Alamat:</strong> {{ $member->alamat }}</p>
    <p><strong>Kontak:</strong> {{ $member->kontak }}</p>
    <p><strong>Status:</strong> {{ $member->status }}</p>
    <p><strong>Tanggal Lahir:</strong> {{ $member->tanggal_lahir }}</p>
    <p><strong>Jenis Kelamin:</strong> <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $genderClass }}">{{ $genderLabel }}</span></p>
</div>
@endsection
