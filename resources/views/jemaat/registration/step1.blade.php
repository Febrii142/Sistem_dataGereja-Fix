@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <h2 class="text-2xl font-bold text-blue-900">Pendaftaran Jemaat - Data Pribadi</h2>
    <div class="h-2 w-full rounded-full bg-slate-200"><div class="h-2 w-1/3 rounded-full bg-blue-700"></div></div>

    <form method="post" action="{{ route('jemaat.registration.save', 1) }}" class="grid gap-4 rounded-xl bg-white p-6 shadow md:grid-cols-2">
        @csrf
        <input name="nama_lengkap" value="{{ old('nama_lengkap', $jemaat->nama_lengkap) }}" placeholder="Nama Lengkap" class="rounded border px-3 py-2 md:col-span-2" required>
        <input name="tempat_lahir" value="{{ old('tempat_lahir', $jemaat->tempat_lahir) }}" placeholder="Tempat Lahir" class="rounded border px-3 py-2" required>
        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $jemaat->tanggal_lahir) }}" class="rounded border px-3 py-2" required>
        <input name="no_telepon" value="{{ old('no_telepon', $jemaat->no_telepon) }}" placeholder="No. Telepon" class="rounded border px-3 py-2" required>
        <input type="email" name="email" value="{{ old('email', $jemaat->email) }}" placeholder="Email" class="rounded border px-3 py-2" required>
        <div class="flex flex-wrap gap-2 md:col-span-2">
            <button formaction="{{ route('jemaat.registration.draft') }}" class="rounded bg-slate-100 px-4 py-2 font-semibold text-slate-700">Simpan Draft</button>
            <button class="rounded bg-blue-700 px-4 py-2 font-semibold text-white">Lanjutkan</button>
        </div>
    </form>
</div>
@endsection
