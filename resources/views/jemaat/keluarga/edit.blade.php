@extends('layouts.app')

@section('content')
<form method="post" action="{{ route('jemaat.keluarga.update', $anggota->id) }}" class="max-w-2xl grid gap-4 rounded-xl bg-white p-6 shadow">
    @csrf
    @method('PUT')
    <h2 class="text-2xl font-bold text-blue-900">Edit Anggota Keluarga</h2>
    <p class="text-slate-600">{{ $anggota->jemaat->nama_lengkap }}</p>

    <select name="hubungan_keluarga" class="rounded border px-3 py-2" required>
        @foreach(['Istri','Suami','Anak','Orangtua','Saudara'] as $hubungan)
            <option value="{{ $hubungan }}" {{ old('hubungan_keluarga', $anggota->hubungan_keluarga) === $hubungan ? 'selected' : '' }}>{{ $hubungan }}</option>
        @endforeach
    </select>
    <select name="status" class="rounded border px-3 py-2" required>
        <option value="aktif" {{ old('status', $anggota->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="non-aktif" {{ old('status', $anggota->status) === 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
    </select>

    <button class="rounded bg-blue-700 px-4 py-2 font-semibold text-white">Simpan Perubahan</button>
</form>
@endsection
