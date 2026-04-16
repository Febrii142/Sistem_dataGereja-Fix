@extends('layouts.app')
@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-2">
    <h2 class="text-2xl font-bold">Manajemen Data Jemaat</h2>
    <div class="flex gap-2 text-sm">
        <a class="rounded bg-slate-900 px-3 py-2 text-white" href="{{ route('members.create') }}">Tambah</a>
        <a class="rounded bg-emerald-600 px-3 py-2 text-white" href="{{ route('members.export.excel') }}">Excel</a>
        <a class="rounded bg-rose-600 px-3 py-2 text-white" href="{{ route('members.export.pdf') }}">PDF</a>
    </div>
</div>
<form method="get" class="mb-4 grid gap-2 md:grid-cols-4">
    <input class="rounded border px-3 py-2" name="search" value="{{ request('search') }}" placeholder="Cari nama/kontak">
    <select class="rounded border px-3 py-2" name="status">
        <option value="">Semua Status</option>
        <option value="aktif" @selected(request('status')==='aktif')>Aktif</option>
        <option value="tidak_aktif" @selected(request('status')==='tidak_aktif')>Tidak Aktif</option>
    </select>
    <button class="rounded bg-blue-600 px-3 py-2 text-white">Filter</button>
</form>
<form method="post" enctype="multipart/form-data" action="{{ route('members.import') }}" class="mb-4 flex gap-2 rounded bg-white p-3 shadow">
    @csrf
    <input type="file" name="file" class="text-sm" required>
    <button class="rounded bg-indigo-600 px-3 py-2 text-sm text-white">Import File</button>
</form>
<div class="overflow-x-auto rounded bg-white shadow">
    <table class="w-full text-sm">
        <thead class="bg-slate-50"><tr><th class="p-3 text-left">Nama</th><th class="p-3 text-left">Kontak</th><th class="p-3 text-left">Status</th><th class="p-3"></th></tr></thead>
        <tbody>
        @forelse($members as $member)
            <tr class="border-t"><td class="p-3">{{ $member->nama }}</td><td class="p-3">{{ $member->kontak }}</td><td class="p-3">{{ $member->status }}</td><td class="p-3 text-right"><a class="text-blue-600" href="{{ route('members.show',$member) }}">Detail</a> | <a class="text-amber-600" href="{{ route('members.edit',$member) }}">Edit</a> |
                    <form action="{{ route('members.destroy',$member) }}" method="post" class="inline">@csrf @method('DELETE')<button class="text-rose-600" onclick="return confirm('Hapus data?')">Hapus</button></form></td></tr>
        @empty
            <tr><td colspan="4" class="p-3 text-center text-slate-500">Belum ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $members->links() }}</div>
@endsection
