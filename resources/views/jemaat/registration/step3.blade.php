@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <h2 class="text-2xl font-bold text-blue-900">Pendaftaran Jemaat - Status Baptis</h2>
    <div class="h-2 w-full rounded-full bg-slate-200"><div class="h-2 w-full rounded-full bg-blue-700"></div></div>

    <form method="post" action="{{ route('jemaat.registration.save', 3) }}" class="grid gap-4 rounded-xl bg-white p-6 shadow">
        @csrf
        <div class="space-x-4">
            <label><input type="radio" name="status_baptis" value="sudah" {{ old('status_baptis', $jemaat->status_baptis) === 'sudah' ? 'checked' : '' }}> Sudah Dibaptis</label>
            <label><input type="radio" name="status_baptis" value="belum" {{ old('status_baptis', $jemaat->status_baptis) === 'belum' ? 'checked' : '' }}> Belum Dibaptis</label>
        </div>

        <input type="date" name="tanggal_baptis" value="{{ old('tanggal_baptis', $jemaat->baptisan->tanggal_baptis ?? $jemaat->tanggal_baptis) }}" class="rounded border px-3 py-2" placeholder="Tanggal Baptis">
        <input name="tempat_baptis" value="{{ old('tempat_baptis', $jemaat->baptisan->tempat_baptis ?? '') }}" class="rounded border px-3 py-2" placeholder="Tempat Baptis">
        <input name="nama_pendeta" value="{{ old('nama_pendeta', $jemaat->baptisan->nama_pendeta ?? '') }}" class="rounded border px-3 py-2" placeholder="Nama Pendeta">

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('jemaat.registration.show', 2) }}" class="rounded bg-slate-100 px-4 py-2 font-semibold text-slate-700">Kembali</a>
            <button formaction="{{ route('jemaat.registration.draft') }}" class="rounded bg-slate-100 px-4 py-2 font-semibold text-slate-700">Simpan Draft</button>
            <button class="rounded bg-blue-700 px-4 py-2 font-semibold text-white">Kirim Pendaftaran</button>
        </div>
    </form>
</div>
@endsection
