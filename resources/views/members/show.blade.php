@extends('layouts.app')
@section('content')
<div class="space-y-6">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Detail Jemaat</h2>
                <p class="mt-1 text-sm text-slate-500">Profil lengkap jemaat terdaftar.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('members.edit', $member) }}" class="rounded-lg bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100">Edit</a>
                <a href="{{ route('members.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Kembali</a>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @php
            $genderClass = $member->jenis_kelamin === 'P'
                ? 'bg-rose-100 text-rose-700'
                : 'bg-cyan-100 text-cyan-700';
            $genderLabel = $member->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
            $statusClass = match ($member->status) {
                'aktif' => 'bg-emerald-100 text-emerald-700',
                'tidak_aktif' => 'bg-slate-100 text-slate-700',
                'pindah' => 'bg-amber-100 text-amber-700',
                default => 'bg-indigo-100 text-indigo-700',
            };
            $statusLabel = $member->status === 'tidak_aktif' ? 'Jemaat Pasif' : ucfirst(str_replace('_', ' ', $member->status));
            $initials = \\Illuminate\\Support\\Str::of($member->nama)
                ->explode(' ')
                ->filter()
                ->take(2)
                ->map(fn (string $part) => \\Illuminate\\Support\\Str::upper(\\Illuminate\\Support\\Str::substr($part, 0, 1)))
                ->join('');
        @endphp

        <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
            <div class="flex w-full flex-col items-center gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-6 text-center lg:w-1/3">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 text-2xl font-bold text-blue-700">
                    {{ $initials }}
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">{{ $member->nama }}</h3>
                    <p class="text-sm text-slate-500">{{ $member->email ?? 'Email tidak tersedia' }}</p>
                </div>
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $genderClass }}">{{ $genderLabel }}</span>
                </div>
            </div>

            <div class="grid w-full gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-slate-100 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $member->alamat }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kontak</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $member->kontak }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tanggal Lahir</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $member->tanggal_lahir ? \Illuminate\Support\Carbon::parse($member->tanggal_lahir)->format('d M Y') : '-' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Status Keanggotaan</p>
                    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $statusLabel }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
