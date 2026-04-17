@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-blue-900">Anggota Keluarga</h2>
        @if($canManage)
            <a href="{{ route('jemaat.keluarga.create') }}" class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Tambah Anggota</a>
        @endif
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-slate-700">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Hubungan</th>
                    <th class="px-4 py-3">Umur</th>
                    <th class="px-4 py-3">Status</th>
                    @if($canManage)<th class="px-4 py-3">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($anggotaKeluarga as $anggota)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $anggota->jemaat->nama_lengkap }}</td>
                        <td class="px-4 py-3">{{ $anggota->hubungan_keluarga }}</td>
                        <td class="px-4 py-3">{{ \Illuminate\Support\Carbon::parse($anggota->jemaat->tanggal_lahir)->age }} tahun</td>
                        <td class="px-4 py-3">{{ $anggota->status }}</td>
                        @if($canManage)
                            <td class="px-4 py-3">
                                <a href="{{ route('jemaat.keluarga.edit', $anggota->id) }}" class="mr-2 text-blue-700">Edit</a>
                                <form method="post" action="{{ route('jemaat.keluarga.destroy', $anggota->id) }}" class="inline" onsubmit="return confirm('Hapus anggota?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-rose-600">Hapus</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada data anggota keluarga.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
