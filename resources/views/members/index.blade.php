@extends('layouts.app')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Data Jemaat</h2>
        <p class="text-sm text-slate-500">Kelola data jemaat, export, dan import data.</p>
    </div>
    <div class="flex flex-wrap gap-2 text-sm">
        <a class="rounded-lg bg-[#1e40af] px-3 py-2 text-white hover:bg-[#1d4ed8]" href="{{ route('members.create') }}">Tambah</a>
        <a class="rounded-lg bg-emerald-600 px-3 py-2 text-white hover:bg-emerald-700" href="{{ route('members.export.excel', request()->query()) }}">Excel</a>
        <a class="rounded-lg bg-rose-600 px-3 py-2 text-white hover:bg-rose-700" href="{{ route('members.export.pdf') }}">PDF</a>
    </div>
</div>
<form method="get" class="mb-4 grid gap-2 rounded-xl bg-white p-4 shadow-sm md:grid-cols-6">
    <input class="rounded-lg border border-slate-200 px-3 py-2" name="search" value="{{ request('search') }}" placeholder="Cari nama/kontak">
    <select class="rounded-lg border border-slate-200 px-3 py-2" name="status">
        <option value="">Semua Status</option>
        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
        <option value="tidak_aktif" @selected(request('status') === 'tidak_aktif')>Non-aktif</option>
        <option value="pindah" @selected(request('status') === 'pindah')>Pindah</option>
    </select>
    <select class="rounded-lg border border-slate-200 px-3 py-2" name="age_category">
        <option value="">Semua Kategori Umur</option>
        <option value="bayi" @selected(request('age_category') === 'bayi')>Bayi</option>
        <option value="anak" @selected(request('age_category') === 'anak')>Anak</option>
        <option value="remaja" @selected(request('age_category') === 'remaja')>Remaja</option>
        <option value="dewasa" @selected(request('age_category') === 'dewasa')>Dewasa</option>
        <option value="lansia" @selected(request('age_category') === 'lansia')>Lansia</option>
    </select>
    <select class="rounded-lg border border-slate-200 px-3 py-2" name="gender">
        <option value="">Semua Jenis Kelamin</option>
        <option value="L" @selected(request('gender') === 'L')>Laki-laki</option>
        <option value="P" @selected(request('gender') === 'P')>Perempuan</option>
    </select>
    @if(! empty($wilayahField))
        <select class="rounded-lg border border-slate-200 px-3 py-2" name="wilayah">
            <option value="">Semua {{ $wilayahField === 'kelompok' ? 'Kelompok' : 'Wilayah' }}</option>
            @foreach($wilayahOptions as $wilayahOption)
                <option value="{{ $wilayahOption }}" @selected(request('wilayah') === $wilayahOption)>{{ $wilayahOption }}</option>
            @endforeach
        </select>
    @endif
    <button class="rounded-lg bg-[#3b82f6] px-3 py-2 font-semibold text-white hover:bg-[#2563eb]">Filter</button>
</form>
<form method="post" enctype="multipart/form-data" action="{{ route('members.import') }}" class="mb-4 flex flex-wrap gap-2 rounded-xl bg-white p-4 shadow-sm">
    @csrf
    <input type="file" name="file" class="text-sm" required>
    <button class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Import File</button>
</form>
<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
        <tr>
            <th class="p-3 text-left font-semibold">Nama</th>
            <th class="p-3 text-left font-semibold">Kontak</th>
            <th class="p-3 text-left font-semibold">Status</th>
            <th class="p-3 text-left font-semibold">Gender</th>
            <th class="p-3 text-left font-semibold">Kategori Jemaat</th>
            <th class="p-3"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($members as $member)
            @php
                $umur = \Illuminate\Support\Carbon::parse($member->tanggal_lahir)->age;
                $kategoriUmur = match (true) {
                    $umur <= 2 => 'Bayi',
                    $umur <= 12 => 'Anak',
                    $umur <= 18 => 'Remaja',
                    $umur <= 59 => 'Dewasa',
                    default => 'Lansia',
                };
            @endphp
            <tr class="border-t border-slate-100">
                <td class="p-3">{{ $member->nama }}</td>
                <td class="p-3">{{ $member->kontak }}</td>
                <td class="p-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $member->status === 'tidak_aktif' ? 'Non-aktif' : ucfirst(str_replace('_', ' ', $member->status)) }}</span></td>
                <td class="p-3">{{ $member->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                <td class="p-3">
                    <div class="flex flex-wrap gap-1">
                        <span class="rounded-full bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-700">{{ $kategoriUmur }}</span>
                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">{{ $member->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        @if(! empty($wilayahField) && ! empty($member->{$wilayahField}))
                            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700">{{ $member->{$wilayahField} }}</span>
                        @endif
                    </div>
                </td>
                <td class="p-3 text-right">
                    <a class="text-[#2563eb]" href="{{ route('members.show',$member) }}">Detail</a> |
                    <a class="text-amber-600" href="{{ route('members.edit',$member) }}">Edit</a> |
                    <form action="{{ route('members.destroy',$member) }}" method="post" class="inline">@csrf @method('DELETE')<button class="text-rose-600" onclick="return confirm('Hapus data?')">Hapus</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="p-3 text-center text-slate-500">Belum ada data jemaat.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $members->links() }}</div>
@endsection
