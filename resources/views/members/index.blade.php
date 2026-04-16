@extends('layouts.app')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Data Jemaat</h2>
        <p class="text-sm text-slate-500">Kelola data jemaat, export, dan import data.</p>
    </div>
    <div class="flex flex-wrap gap-2 text-sm">
        <a class="rounded-lg bg-[#1e40af] px-3 py-2 text-white hover:bg-[#1d4ed8]" href="{{ route('members.create') }}">Tambah</a>
        <a class="rounded-lg bg-emerald-600 px-3 py-2 text-white hover:bg-emerald-700" href="{{ route('members.export.excel') }}">Excel</a>
        <a class="rounded-lg bg-rose-600 px-3 py-2 text-white hover:bg-rose-700" href="{{ route('members.export.pdf') }}">PDF</a>
    </div>
</div>
<form method="get" class="mb-4 grid gap-2 rounded-xl bg-white p-4 shadow-sm md:grid-cols-4">
    <input class="rounded-lg border border-slate-200 px-3 py-2" name="search" value="{{ request('search') }}" placeholder="Cari nama/kontak">
    <select class="rounded-lg border border-slate-200 px-3 py-2" name="status">
        <option value="">Semua Status</option>
        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
        <option value="tidak_aktif" @selected(request('status') === 'tidak_aktif')>Tidak Aktif</option>
    </select>
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
            <th class="p-3"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($members as $member)
            <tr class="border-t border-slate-100">
                <td class="p-3">{{ $member->nama }}</td>
                <td class="p-3">{{ $member->kontak }}</td>
                <td class="p-3">{{ ucfirst(str_replace('_', ' ', $member->status)) }}</td>
                <td class="p-3">{{ $member->jenis_kelamin === 'L' ? 'L' : 'P' }}</td>
                <td class="p-3 text-right">
                    <a class="text-[#2563eb]" href="{{ route('members.show',$member) }}">Detail</a> |
                    <a class="text-amber-600" href="{{ route('members.edit',$member) }}">Edit</a> |
                    <form action="{{ route('members.destroy',$member) }}" method="post" class="inline">@csrf @method('DELETE')<button class="text-rose-600" onclick="return confirm('Hapus data?')">Hapus</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="p-3 text-center text-slate-500">Belum ada data jemaat.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $members->links() }}</div>
@endsection
