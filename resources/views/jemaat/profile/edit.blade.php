@extends('layouts.app')

@section('content')
<form method="post" action="{{ route('jemaat.profile.update') }}" class="max-w-4xl grid gap-4 rounded-xl bg-white p-6 shadow md:grid-cols-2">
    @csrf
    @method('PUT')
    <h2 class="text-2xl font-bold text-blue-900 md:col-span-2">Edit Profil Jemaat</h2>
    <input name="nama_lengkap" value="{{ old('nama_lengkap', $jemaat->nama_lengkap) }}" placeholder="Nama Lengkap" class="rounded border px-3 py-2" required>
    <input name="tempat_lahir" value="{{ old('tempat_lahir', $jemaat->tempat_lahir) }}" placeholder="Tempat Lahir" class="rounded border px-3 py-2" required>
    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $jemaat->tanggal_lahir) }}" class="rounded border px-3 py-2" required>
    <input name="no_telepon" value="{{ old('no_telepon', $jemaat->no_telepon) }}" placeholder="No. Telepon" class="rounded border px-3 py-2" required>
    <input type="email" name="email" value="{{ old('email', $jemaat->email) }}" placeholder="Email" class="rounded border px-3 py-2" required>
    <input name="kota" value="{{ old('kota', $jemaat->kota) }}" placeholder="Kota" class="rounded border px-3 py-2" required>
    <input name="kode_pos" value="{{ old('kode_pos', $jemaat->kode_pos) }}" placeholder="Kode Pos" class="rounded border px-3 py-2" required>
    <select name="status_baptis" class="rounded border px-3 py-2" required>
        <option value="belum" {{ old('status_baptis', $jemaat->status_baptis) === 'belum' ? 'selected' : '' }}>Belum</option>
        <option value="sudah" {{ old('status_baptis', $jemaat->status_baptis) === 'sudah' ? 'selected' : '' }}>Sudah</option>
    </select>
    <input type="date" name="tanggal_baptis" value="{{ old('tanggal_baptis', $jemaat->tanggal_baptis) }}" class="rounded border px-3 py-2">
    <textarea name="alamat" rows="3" class="rounded border px-3 py-2 md:col-span-2" placeholder="Alamat" required>{{ old('alamat', $jemaat->alamat) }}</textarea>
    <button class="rounded bg-blue-700 px-4 py-2 font-semibold text-white md:col-span-2">Simpan Perubahan</button>
</form>
@endsection
