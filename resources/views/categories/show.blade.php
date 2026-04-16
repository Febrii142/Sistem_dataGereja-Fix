@extends('layouts.app')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Kategori: {{ $category->name }}</h2>
        <p class="text-sm text-slate-500">Detail jemaat dan statistik kategori.</p>
    </div>
    <a class="rounded-lg bg-emerald-600 px-3 py-2 text-white hover:bg-emerald-700" href="{{ route('categories.export.excel', $category) }}">Export Excel</a>
</div>

<div class="mb-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Total Jemaat</p><p class="text-2xl font-bold text-[#1e40af]">{{ $totalMembers }}</p></div>
    <div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Laki-laki</p><p class="text-2xl font-bold text-[#1e40af]">{{ $maleMembers }}</p></div>
    <div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Perempuan</p><p class="text-2xl font-bold text-[#1e40af]">{{ $femaleMembers }}</p></div>
    <div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Aktif</p><p class="text-2xl font-bold text-[#1e40af]">{{ $activeMembers }}</p></div>
</div>

<div class="mb-4 rounded-xl bg-white p-4 text-sm text-slate-600 shadow-sm">
    <p><span class="font-semibold">Tipe:</span> {{ ucfirst($category->type) }}</p>
    <p><span class="font-semibold">Rata-rata usia jemaat:</span> {{ $ageAverage !== null ? $ageAverage.' tahun' : '-' }}</p>
    @if($category->description)
        <p><span class="font-semibold">Deskripsi:</span> {{ $category->description }}</p>
    @endif
</div>

<form method="get" class="mb-4 grid gap-2 rounded-xl bg-white p-4 shadow-sm md:grid-cols-4">
    <input class="rounded-lg border border-slate-200 px-3 py-2" name="search" value="{{ request('search') }}" placeholder="Cari nama/kontak jemaat">
    <button class="rounded-lg bg-[#3b82f6] px-3 py-2 font-semibold text-white hover:bg-[#2563eb]">Cari</button>
</form>

<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
        <tr>
            <th class="p-3 text-left font-semibold">Nama</th>
            <th class="p-3 text-left font-semibold">Kontak</th>
            <th class="p-3 text-left font-semibold">Status</th>
            <th class="p-3 text-left font-semibold">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @forelse($members as $member)
            <tr class="border-t border-slate-100">
                <td class="p-3">{{ $member->nama }}</td>
                <td class="p-3">{{ $member->kontak }}</td>
                <td class="p-3">{{ ucfirst(str_replace('_', ' ', $member->status)) }}</td>
                <td class="p-3"><a class="text-[#2563eb]" href="{{ route('members.show', $member) }}">Detail Jemaat</a></td>
            </tr>
        @empty
            <tr><td colspan="4" class="p-3 text-center text-slate-500">Belum ada jemaat pada kategori ini.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $members->links() }}</div>
@endsection
