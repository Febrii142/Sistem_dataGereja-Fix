@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <h2 class="text-2xl font-bold text-blue-900">Edit Profil Jemaat</h2>
    <form method="post" action="{{ route('jemaat.profile.update') }}" class="grid gap-4 rounded-xl bg-white p-6 shadow md:grid-cols-2">
        @csrf
        <label class="space-y-1 text-sm">
            <span>Nama Jemaat</span>
            <input name="nama" value="{{ old('nama', $jemaat->nama_lengkap) }}" class="w-full rounded border px-3 py-2" required>
        </label>
        <label class="space-y-1 text-sm">
            <span>No. Identitas (KTP/SIM)</span>
            <input name="no_identitas" value="{{ old('no_identitas', $jemaat->no_identitas) }}" class="w-full rounded border px-3 py-2">
        </label>
        <label class="space-y-1 text-sm">
            <span>Tanggal Lahir</span>
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $jemaat->tanggal_lahir) }}" class="w-full rounded border px-3 py-2" required>
        </label>
        <label class="space-y-1 text-sm">
            <span>Jenis Kelamin</span>
            <select name="jenis_kelamin" class="w-full rounded border px-3 py-2">
                <option value="">Pilih jenis kelamin</option>
                <option value="L" @selected(old('jenis_kelamin', $jemaat->jenis_kelamin) === 'L')>Laki-laki</option>
                <option value="P" @selected(old('jenis_kelamin', $jemaat->jenis_kelamin) === 'P')>Perempuan</option>
            </select>
        </label>
        <label class="space-y-1 text-sm">
            <span>Nomor Telepon</span>
            <input name="nomor_telepon" value="{{ old('nomor_telepon', $jemaat->no_telepon) }}" class="w-full rounded border px-3 py-2" required>
        </label>
        <label class="space-y-1 text-sm">
            <span>Status Pernikahan</span>
            <select name="status_pernikahan" class="w-full rounded border px-3 py-2">
                <option value="">Pilih status</option>
                @foreach(['Belum Menikah', 'Menikah', 'Janda', 'Duda'] as $status)
                    <option value="{{ $status }}" @selected(old('status_pernikahan', $jemaat->status_perkawinan) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1 text-sm md:col-span-2">
            <span>Alamat</span>
            <textarea name="alamat" rows="3" class="w-full rounded border px-3 py-2" required>{{ old('alamat', $jemaat->alamat) }}</textarea>
        </label>
        <button class="rounded bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800 md:col-span-2">Simpan Profil</button>
    </form>
</div>
@endsection
