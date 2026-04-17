@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-bold text-blue-900">Manajemen Keluarga</h2>

    <form method="post" action="{{ route('jemaat.family.store') }}" class="grid gap-3 rounded-xl bg-white p-6 shadow md:grid-cols-4">
        @csrf
        <input name="nama" value="{{ old('nama') }}" placeholder="Nama anggota" class="rounded border px-3 py-2 md:col-span-2" required>
        <input name="hubungan" value="{{ old('hubungan') }}" placeholder="Hubungan keluarga" class="rounded border px-3 py-2" required>
        <input name="no_telp" value="{{ old('no_telp') }}" placeholder="No. telepon" class="rounded border px-3 py-2">
        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="rounded border px-3 py-2">
        <button class="rounded bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800 md:col-span-4">Tambah Anggota</button>
    </form>

    <div class="overflow-x-auto rounded-xl bg-white shadow">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-slate-700">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Hubungan</th>
                    <th class="px-4 py-3">No. Telp</th>
                    <th class="px-4 py-3">Tanggal Lahir</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($familyMembers as $member)
                    <tr class="border-t align-top">
                        <td class="px-4 py-3">
                            <input form="update-family-{{ $member->id }}" name="nama" value="{{ $member->nama }}" class="w-full rounded border px-2 py-1" required>
                        </td>
                        <td class="px-4 py-3">
                            <input form="update-family-{{ $member->id }}" name="hubungan" value="{{ $member->hubungan }}" class="w-full rounded border px-2 py-1" required>
                        </td>
                        <td class="px-4 py-3">
                            <input form="update-family-{{ $member->id }}" name="no_telp" value="{{ $member->no_telp }}" class="w-full rounded border px-2 py-1">
                        </td>
                        <td class="px-4 py-3">
                            <input form="update-family-{{ $member->id }}" type="date" name="tanggal_lahir" value="{{ $member->tanggal_lahir }}" class="w-full rounded border px-2 py-1">
                        </td>
                        <td class="px-4 py-3">
                            <form id="update-family-{{ $member->id }}" method="post" action="{{ route('jemaat.family.update', $member->id) }}" class="inline">
                                @csrf
                                <button class="rounded bg-blue-100 px-3 py-1 text-blue-700 hover:bg-blue-200">Simpan</button>
                            </form>
                            <form method="post" action="{{ route('jemaat.family.delete', $member->id) }}" class="ml-1 inline" onsubmit="return confirm('Hapus anggota keluarga ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded bg-rose-100 px-3 py-1 text-rose-700 hover:bg-rose-200">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada anggota keluarga.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
