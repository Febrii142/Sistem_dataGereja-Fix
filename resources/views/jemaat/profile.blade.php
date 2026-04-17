@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <h2 class="text-2xl font-bold text-blue-900">Profil Jemaat</h2>
    <form method="post" action="{{ route('jemaat.profile.update.post') }}" class="grid gap-4 rounded-xl bg-white p-6 shadow md:grid-cols-2">
        @csrf
        <label class="space-y-1 text-sm">
            <span>Nama</span>
            <input name="nama" value="{{ old('nama', $jemaat->nama_lengkap) }}" class="w-full rounded border px-3 py-2" required>
        </label>
        <label class="space-y-1 text-sm">
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email', $jemaat->email) }}" class="w-full rounded border px-3 py-2" required>
        </label>
        <label class="space-y-1 text-sm">
            <span>No. Telepon</span>
            <input name="no_telp" value="{{ old('no_telp', $jemaat->no_telepon) }}" class="w-full rounded border px-3 py-2" required>
        </label>
        <label class="space-y-1 text-sm">
            <span>Tempat Lahir</span>
            <input name="tempat_lahir" value="{{ old('tempat_lahir', $jemaat->tempat_lahir) }}" class="w-full rounded border px-3 py-2" required>
        </label>
        <label class="space-y-1 text-sm">
            <span>Tanggal Lahir</span>
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $jemaat->tanggal_lahir) }}" class="w-full rounded border px-3 py-2" required>
        </label>
        <label class="space-y-1 text-sm">
            <span>Status Perkawinan</span>
            <select name="status_perkawinan" class="w-full rounded border px-3 py-2" required>
                @foreach(['Belum Menikah', 'Menikah', 'Janda', 'Duda'] as $status)
                    <option value="{{ $status }}" @selected(old('status_perkawinan', $jemaat->status_perkawinan) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </label>
        <label class="space-y-1 text-sm md:col-span-2">
            <span>Kategori Jemaat</span>
            <select name="kategori_jemaat" class="w-full rounded border px-3 py-2">
                <option value="">Pilih kategori</option>
                @foreach($kategoriOptions as $kategori)
                    <option value="{{ $kategori }}" @selected(old('kategori_jemaat', $jemaat->kategori_jemaat) === $kategori)>{{ $kategori }}</option>
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
