@extends('layouts.app')
@section('content')
<div class="space-y-6">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Daftar Jemaat</h2>
                <p class="mt-1 text-sm text-slate-500">Fokus pada CRUD jemaat dan pencarian dasar.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form method="get" class="flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2">
                    <span class="text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                        </svg>
                    </span>
                    <input
                        class="w-full bg-transparent px-2 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama atau alamat..."
                    >
                    <button class="rounded-full bg-[#3b82f6] px-4 py-1.5 text-xs font-semibold text-white hover:bg-[#2563eb]">Cari</button>
                </form>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-[#1d4ed8]/20 bg-[#eff6ff] p-5 text-slate-800 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-lg">🔔</div>
                <div>
                    <h3 class="text-lg font-semibold">Verifikasi Jemaat Baru</h3>
                    <p class="mt-1 text-sm text-slate-600">Ada {{ $pendingRegistrationsCount }} pendaftaran baru yang menunggu persetujuan.</p>
                </div>
            </div>
            @if(auth()->user()?->hasPermission('view_users'))
                <a href="{{ route('members.verification.index') }}" class="rounded-lg bg-[#1e40af] px-3 py-2 text-sm font-semibold text-white hover:bg-[#1d4ed8]">Mulai Verifikasi</a>
            @endif
        </div>
    </div>

    <p class="text-sm text-slate-600">Menampilkan <span class="font-semibold">{{ $members->total() }}</span> total jemaat terdaftar</p>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">NO</th>
                    <th class="px-4 py-3 text-left font-semibold">NAMA LENGKAP</th>
                    <th class="px-4 py-3 text-left font-semibold">ALAMAT DOMISILI</th>
                    <th class="px-4 py-3 text-left font-semibold">STATUS</th>
                    <th class="px-4 py-3 text-left font-semibold">TERDAFTAR</th>
                    <th class="px-4 py-3 text-left font-semibold">KELOLA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                    @php
                        $statusClass = match ($member->status) {
                            'aktif' => 'bg-emerald-100 text-emerald-700',
                            'tidak_aktif' => 'bg-slate-100 text-slate-700',
                            'pindah' => 'bg-amber-100 text-amber-700',
                            default => 'bg-indigo-100 text-indigo-700',
                        };
                        $statusLabel = match ($member->status) {
                            'aktif' => 'Jemaat Aktif',
                            'tidak_aktif' => 'Jemaat Pasif',
                            'pindah' => 'Pindah',
                            default => ucfirst(str_replace('_', ' ', $member->status)),
                        };
                        $initials = \Illuminate\Support\Str::of($member->nama)
                            ->explode(' ')
                            ->filter()
                            ->take(2)
                            ->map(fn (string $part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
                            ->join('');
                        $rowNumber = ($members->firstItem() ?? 0) + $loop->index;
                    @endphp
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-4 py-4 font-semibold text-slate-500">{{ $rowNumber }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
                                    {{ $initials }}
                                </span>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $member->nama }}</p>
                                    <p class="text-xs text-slate-500">{{ $member->email ?? 'Email tidak tersedia' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-slate-600">{{ $member->alamat }}</td>
                        <td class="px-4 py-4">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="px-4 py-4 text-slate-600">{{ $member->created_at?->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-2">
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada data jemaat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">{{ $members->links() }}</div>

    <form method="post" enctype="multipart/form-data" action="{{ route('members.import') }}" class="flex flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        @csrf
        <input type="file" name="file" class="w-full text-sm md:w-auto" required>
        <button class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Import File</button>
    </form>
</div>

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
