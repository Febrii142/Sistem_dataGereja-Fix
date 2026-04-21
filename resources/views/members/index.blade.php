@extends('layouts.app')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Data Jemaat</h2>
        <p class="text-sm text-slate-500">Kelola data jemaat dan impor data.</p>
        <p class="mt-1 text-sm font-medium text-slate-600">Total jemaat: {{ $members->total() }}</p>
    </div>
</div>
<div class="mb-4 rounded-xl bg-[#1e40af] p-5 text-white shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold">Verifikasi Jemaat Baru</h3>
            <p class="mt-1 text-sm text-blue-100">Ada {{ $pendingRegistrationsCount }} pendaftaran baru yang memerlukan peninjauan administratif.</p>
        </div>
        @if(auth()->user()?->hasPermission('view_users'))
            <a href="{{ route('members.verification.index') }}" class="rounded-lg bg-white px-3 py-2 text-sm font-semibold text-[#1e40af] hover:bg-slate-100">Lihat Antrean</a>
        @endif
    </div>
</div>
<form method="get" class="mb-4 grid gap-2 rounded-xl bg-white p-4 shadow-sm md:grid-cols-7">
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
    <select class="rounded-lg border border-slate-200 px-3 py-2" name="year">
        <option value="">Semua Tahun</option>
        @foreach($yearOptions as $yearOption)
            <option value="{{ $yearOption }}" @selected((string) request('year') === (string) $yearOption)>{{ $yearOption }}</option>
        @endforeach
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
<div class="space-y-3">
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
            $statusClass = match ($member->status) {
                'aktif' => 'bg-emerald-100 text-emerald-700',
                'tidak_aktif' => 'bg-slate-100 text-slate-700',
                'pindah' => 'bg-amber-100 text-amber-700',
                default => 'bg-indigo-100 text-indigo-700',
            };
            $initials = \Illuminate\Support\Str::of($member->nama)
                ->explode(' ')
                ->filter()
                ->take(2)
                ->map(fn (string $part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
                ->join('');
        @endphp
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                        {{ $initials }}
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-semibold text-slate-900">{{ $member->nama }}</h3>
                        <p class="text-sm text-slate-600">{{ $member->kontak }}</p>
                        <p class="text-sm text-slate-500">{{ $member->alamat }}</p>
                        <div class="flex flex-wrap gap-1">
                            <span class="rounded-full bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-700">{{ $kategoriUmur }}</span>
                            <span class="rounded-full bg-cyan-100 px-2 py-1 text-xs font-semibold text-cyan-700">{{ $member->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                            @if(! empty($wilayahField) && ! empty($member->{$wilayahField}))
                                <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700">{{ $member->{$wilayahField} }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="space-y-2 md:text-right">
                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $statusClass }}">
                        {{ $member->status === 'tidak_aktif' ? 'Non-aktif' : ucfirst(str_replace('_', ' ', $member->status)) }}
                    </span>
                    <p class="text-xs text-slate-500">Diperbarui {{ $member->updated_at?->format('d M Y') }}</p>
                    <div class="flex flex-wrap gap-2 md:justify-end">
                        <a class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100" href="{{ route('members.show', $member) }}">Lihat</a>
                        <a class="rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100" href="{{ route('members.edit', $member) }}">Edit</a>
                        @if(auth()->user()?->hasRole(['Admin', 'Super Admin', 'Staff']))
                            <button
                                type="button"
                                class="open-status-modal rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100"
                                data-member-id="{{ $member->id }}"
                                data-member-name="{{ $member->nama }}"
                                data-member-status="{{ $member->status }}"
                                data-update-url="{{ route('members.update-status', $member) }}"
                            >
                                Ubah Status
                            </button>
                        @endif
                        <form action="{{ route('members.destroy', $member) }}" method="post" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100" onclick="return confirm('Hapus data?')">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-xl bg-white p-6 text-center text-slate-500 shadow-sm">Belum ada data jemaat.</div>
    @endforelse
</div>
<div class="mt-4">{{ $members->links() }}</div>

@if(auth()->user()?->hasRole(['Admin', 'Super Admin', 'Staff']))
    <div id="status-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 px-4">
        <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Ubah Status Jemaat</h3>
                    <p id="status-modal-member-name" class="text-sm text-slate-500"></p>
                </div>
                <button type="button" id="close-status-modal" class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">✕</button>
            </div>
            <form id="status-modal-form" method="post" action="">
                @csrf
                @method('PATCH')
                <label class="block text-sm">
                    <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</span>
                    <select id="status-modal-input" name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-[#3b82f6] focus:outline-none" required>
                        <option value="aktif">Aktif</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                        <option value="pindah">Pindah</option>
                    </select>
                </label>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" id="cancel-status-modal" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-[#3b82f6] px-4 py-2 text-sm font-semibold text-white hover:bg-[#2563eb]">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@push('scripts')
@if(auth()->user()?->hasRole(['Admin', 'Super Admin', 'Staff']))
<script>
    const statusModal = document.getElementById('status-modal');
    const statusModalForm = document.getElementById('status-modal-form');
    const statusModalInput = document.getElementById('status-modal-input');
    const statusModalMemberName = document.getElementById('status-modal-member-name');
    const closeStatusModal = () => {
        statusModal.classList.add('hidden');
        statusModal.classList.remove('flex');
    };

    document.querySelectorAll('.open-status-modal').forEach((button) => {
        button.addEventListener('click', function () {
            const memberName = this.dataset.memberName;
            const memberStatus = this.dataset.memberStatus;
            const updateUrl = this.dataset.updateUrl;

            statusModalForm.action = updateUrl;
            statusModalInput.value = memberStatus;
            statusModalMemberName.textContent = memberName;
            statusModal.classList.remove('hidden');
            statusModal.classList.add('flex');
        });
    });

    document.getElementById('close-status-modal')?.addEventListener('click', closeStatusModal);
    document.getElementById('cancel-status-modal')?.addEventListener('click', closeStatusModal);
    statusModal?.addEventListener('click', function (event) {
        if (event.target === statusModal) {
            closeStatusModal();
        }
    });
</script>
@endif
@endpush
