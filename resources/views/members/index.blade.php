@extends('layouts.app')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="space-y-2">
        <h2 class="text-2xl font-bold text-slate-800">Daftar Jemaat</h2>
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                Verifikasi Jemaat Baru
                <span class="ml-2 rounded-full bg-amber-200 px-2 py-0.5 text-[11px]">{{ $verificationQueueCount }}</span>
            </span>
            <a href="{{ route('notifications.index') }}" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Lihat Antrian
            </a>
        </div>
    </div>
</div>

<form method="get" class="mb-4 grid gap-3 rounded-xl bg-white p-4 shadow-sm md:grid-cols-5">
    <select class="rounded-lg border border-slate-200 px-3 py-2 text-sm" name="status">
        <option value="">Filter Status</option>
        @foreach($statusOptions as $statusValue => $statusLabel)
            <option value="{{ $statusValue }}" @selected(request('status') === $statusValue)>{{ $statusLabel }}</option>
        @endforeach
    </select>
    <select class="rounded-lg border border-slate-200 px-3 py-2 text-sm" name="tahun_bergabung">
        <option value="">Tahun Bergabung</option>
        @foreach($availableJoinYears as $joinYear)
            <option value="{{ $joinYear }}" @selected((string)request('tahun_bergabung') === (string)$joinYear)>{{ $joinYear }}</option>
        @endforeach
    </select>
    <select class="rounded-lg border border-slate-200 px-3 py-2 text-sm" name="category_id">
        <option value="">Semua Kategori</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected((string)request('category_id') === (string)$category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <input class="rounded-lg border border-slate-200 px-3 py-2 text-sm" name="search" value="{{ request('search') }}" placeholder="Cari nama/kontak">
    <div class="flex gap-2">
        <button class="flex-1 rounded-lg bg-[#3b82f6] px-3 py-2 text-sm font-semibold text-white hover:bg-[#2563eb]">Filter</button>
        <a class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700" href="{{ route('members.export.excel') }}">Export Excel</a>
    </div>
</form>

<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
        <tr>
            <th class="p-3 text-left font-semibold">NAMA LENGKAP</th>
            <th class="p-3 text-left font-semibold">ALAMAT DOMISILI</th>
            <th class="p-3 text-left font-semibold">STATUS KEANGGOTAAN</th>
            <th class="p-3 text-left font-semibold">TANGGAL MASUK</th>
            <th class="p-3 text-right font-semibold">AKSI</th>
        </tr>
        </thead>
        <tbody>
        @forelse($members as $member)
            @php
                $memberStatus = strtolower((string) $member->status);
                $statusClass = match ($memberStatus) {
                    'aktif' => 'bg-emerald-100 text-emerald-700',
                    'pengurus' => 'bg-indigo-100 text-indigo-700',
                    'proses_verifikasi' => 'bg-amber-100 text-amber-700',
                    'pasif', 'tidak_aktif' => 'bg-rose-100 text-rose-700',
                    default => 'bg-slate-100 text-slate-700',
                };
                $nameParts = preg_split('/\s+/', trim($member->nama)) ?: [];
                $initials = collect($nameParts)->take(2)->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))->implode('');
            @endphp
            <tr class="border-t border-slate-100">
                <td class="p-3 align-top">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#dbeafe] text-xs font-semibold text-[#1d4ed8]">{{ $initials }}</span>
                        <div class="min-w-0 space-y-1">
                            <p class="font-semibold text-slate-800">{{ $member->nama }}</p>
                            <p class="text-xs text-slate-500">{{ $member->kontak }}</p>
                            <div class="flex flex-wrap gap-1">
                                @forelse($member->categories as $category)
                                    @php
                                        $categoryClass = match ($category->type) {
                                            'umur' => 'bg-sky-100 text-sky-700',
                                            'status' => 'bg-violet-100 text-violet-700',
                                            'wilayah' => 'bg-teal-100 text-teal-700',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp
                                    <span class="rounded-full px-2 py-1 text-[11px] font-medium {{ $categoryClass }}">{{ $category->name }}</span>
                                @empty
                                    <span class="text-xs text-slate-400">Tanpa kategori</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </td>
                <td class="p-3 align-top text-slate-700">{{ $member->alamat }}</td>
                <td class="p-3 align-top">
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $member->status)) }}</span>
                </td>
                <td class="p-3 align-top text-slate-700">{{ optional($member->created_at)->translatedFormat('d M Y') }}</td>
                <td class="p-3 text-right align-top">
                    <div class="inline-flex items-center gap-2">
                        <a class="rounded-lg border border-amber-200 px-3 py-1.5 text-xs font-medium text-amber-700 hover:bg-amber-50" href="{{ route('members.edit',$member) }}">Edit</a>
                        <form action="{{ route('members.destroy',$member) }}" method="post" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50" onclick="return confirm('Hapus data?')">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="p-3 text-center text-slate-500">Belum ada data jemaat.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $members->links() }}</div>

<details class="mt-6 rounded-xl bg-white p-4 shadow-sm">
    <summary class="cursor-pointer text-sm font-semibold text-slate-700">Kelola Kategori Jemaat</summary>
    <form action="{{ route('categories.store') }}" method="post" class="mt-4 grid gap-3 md:grid-cols-4">
        @csrf
        <input type="text" name="name" required placeholder="Nama kategori" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <select name="type" required class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
            <option value="umur">Umur</option>
            <option value="status">Status</option>
            <option value="wilayah">Wilayah/Kelompok</option>
        </select>
        <input type="text" name="description" placeholder="Deskripsi (opsional)" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <button class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Tambah Kategori</button>
    </form>
</details>

<form method="post" enctype="multipart/form-data" action="{{ route('members.import') }}" class="mt-4 flex flex-wrap gap-2 rounded-xl bg-white p-4 shadow-sm">
    @csrf
    <input type="file" name="file" class="text-sm" required>
    <button class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Import File</button>
</form>
@endsection
