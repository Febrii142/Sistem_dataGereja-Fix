@extends('layouts.app')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Kategori Jemaat</h2>
        <p class="text-sm text-slate-500">Kelola kategori umur, status, dan wilayah/kelompok jemaat.</p>
    </div>
    <a class="rounded-lg bg-[#1e40af] px-3 py-2 text-white hover:bg-[#1d4ed8]" href="{{ route('categories.create') }}">Tambah Kategori</a>
</div>

<div class="mb-4 grid gap-3 sm:grid-cols-3">
    <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-sm text-slate-500">Kategori Umur</p>
        <p class="text-2xl font-bold text-[#1e40af]">{{ $statsByType['umur'] ?? 0 }}</p>
    </div>
    <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-sm text-slate-500">Kategori Status</p>
        <p class="text-2xl font-bold text-[#1e40af]">{{ $statsByType['status'] ?? 0 }}</p>
    </div>
    <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-sm text-slate-500">Kategori Wilayah</p>
        <p class="text-2xl font-bold text-[#1e40af]">{{ $statsByType['wilayah'] ?? 0 }}</p>
    </div>
</div>

<form method="get" class="mb-4 grid gap-2 rounded-xl bg-white p-4 shadow-sm md:grid-cols-4">
    <input class="rounded-lg border border-slate-200 px-3 py-2" name="search" value="{{ request('search') }}" placeholder="Cari kategori">
    <select class="rounded-lg border border-slate-200 px-3 py-2" name="type">
        <option value="">Semua Tipe</option>
        <option value="umur" @selected(request('type')==='umur')>Umur</option>
        <option value="status" @selected(request('type')==='status')>Status</option>
        <option value="wilayah" @selected(request('type')==='wilayah')>Wilayah/Kelompok</option>
    </select>
    <button class="rounded-lg bg-[#3b82f6] px-3 py-2 font-semibold text-white hover:bg-[#2563eb]">Filter</button>
</form>

<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
        <tr>
            <th class="p-3 text-left font-semibold">Nama</th>
            <th class="p-3 text-left font-semibold">Tipe</th>
            <th class="p-3 text-left font-semibold">Jumlah Jemaat</th>
            <th class="p-3"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($categories as $category)
            <tr class="border-t border-slate-100">
                <td class="p-3">{{ $category->name }}</td>
                <td class="p-3">{{ ucfirst($category->type) }}</td>
                <td class="p-3">{{ $category->members_count }}</td>
                <td class="p-3 text-right">
                    <a class="text-[#2563eb]" href="{{ route('categories.show', $category) }}">Detail</a> |
                    <a class="text-amber-600" href="{{ route('categories.edit', $category) }}">Edit</a> |
                    <form action="{{ route('categories.destroy', $category) }}" method="post" class="inline">@csrf @method('DELETE')<button class="text-rose-600" onclick="return confirm('Hapus kategori?')">Hapus</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="p-3 text-center text-slate-500">Belum ada kategori.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $categories->links() }}</div>
@endsection
