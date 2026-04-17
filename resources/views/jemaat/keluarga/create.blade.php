@extends('layouts.app')

@section('content')
<form method="post" action="{{ route('jemaat.keluarga.store') }}" class="max-w-4xl grid gap-4 rounded-xl bg-white p-6 shadow md:grid-cols-2">
    @csrf
    <h2 class="text-2xl font-bold text-blue-900 md:col-span-2">Tambah Anggota Keluarga</h2>

    <select name="mode" class="rounded border px-3 py-2 md:col-span-2" required>
        <option value="new" {{ old('mode') === 'new' ? 'selected' : '' }}>Data Baru</option>
        <option value="existing" {{ old('mode') === 'existing' ? 'selected' : '' }}>Cari Jemaat Existing</option>
    </select>

    <select name="existing_jemaat_id" class="rounded border px-3 py-2 md:col-span-2">
        <option value="">-- Pilih Jemaat Existing --</option>
        @foreach($jemaatExisting as $existing)
            <option value="{{ $existing->id }}" {{ (string) old('existing_jemaat_id') === (string) $existing->id ? 'selected' : '' }}>{{ $existing->nama_lengkap }}</option>
        @endforeach
    </select>

    <input name="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Nama Lengkap (untuk data baru)" class="rounded border px-3 py-2">
    <input name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Tempat Lahir" class="rounded border px-3 py-2">
    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="rounded border px-3 py-2">
    <input name="no_telepon" value="{{ old('no_telepon') }}" placeholder="No Telepon" class="rounded border px-3 py-2">
    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="rounded border px-3 py-2 md:col-span-2">

    <select name="hubungan_keluarga" class="rounded border px-3 py-2" required>
        @foreach(['Istri','Suami','Anak','Orangtua','Saudara'] as $hubungan)
            <option value="{{ $hubungan }}" {{ old('hubungan_keluarga') === $hubungan ? 'selected' : '' }}>{{ $hubungan }}</option>
        @endforeach
    </select>
    <select name="status" class="rounded border px-3 py-2" required>
        <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="non-aktif" {{ old('status') === 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
    </select>

    <button class="rounded bg-blue-700 px-4 py-2 font-semibold text-white md:col-span-2">Simpan</button>
</form>
@endsection
