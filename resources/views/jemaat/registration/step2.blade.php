@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <h2 class="text-2xl font-bold text-blue-900">Pendaftaran Jemaat - Alamat Lengkap</h2>
    <div class="h-2 w-full rounded-full bg-slate-200"><div class="h-2 w-2/3 rounded-full bg-blue-700"></div></div>

    <form method="post" action="{{ route('jemaat.registration.save', 2) }}" class="grid gap-4 rounded-xl bg-white p-6 shadow">
        @csrf
        <textarea name="alamat" rows="3" placeholder="Alamat" class="rounded border px-3 py-2" required>{{ old('alamat', $jemaat->alamat) }}</textarea>
        <input name="kota" value="{{ old('kota', $jemaat->kota) }}" placeholder="Kota/Kabupaten" class="rounded border px-3 py-2" required>
        <input name="kode_pos" value="{{ old('kode_pos', $jemaat->kode_pos) }}" placeholder="Kode Pos" class="rounded border px-3 py-2" required>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('jemaat.registration.show', 1) }}" class="rounded bg-slate-100 px-4 py-2 font-semibold text-slate-700">Kembali</a>
            <button formaction="{{ route('jemaat.registration.draft') }}" class="rounded bg-slate-100 px-4 py-2 font-semibold text-slate-700">Simpan Draft</button>
            <button class="rounded bg-blue-700 px-4 py-2 font-semibold text-white">Lanjutkan</button>
        </div>
    </form>
</div>
@endsection
