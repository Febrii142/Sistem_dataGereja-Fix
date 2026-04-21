@extends('layouts.app')

@section('content')
@php
    $excelQuery = array_filter([
        'tab' => $activeTab ?? 'demografi',
        'search' => $filters['search'] ?? '',
        'status' => $filters['status'] ?? '',
        'gender' => $filters['gender'] ?? '',
        'age_category' => $filters['age_range'] ?? '',
    ], fn ($value) => $value !== '');

    $pdfQuery = array_filter([
        'tab' => $activeTab ?? 'demografi',
        'search' => $filters['search'] ?? '',
        'status' => $filters['status'] ?? '',
        'gender' => $filters['gender'] ?? '',
        'age_range' => $filters['age_range'] ?? '',
        'birthday_month' => $filters['birthday_month'] ?? '',
    ], fn ($value) => $value !== '');

    $segments = $distribution->values();
    $palette = ['#3b82f6', '#14b8a6', '#f97316', '#8b5cf6'];
    $from = 0;
    $conicParts = [];

    foreach ($segments as $index => $segment) {
        $deg = round(($segment->percentage / 100) * 360, 1);
        $to = $from + $deg;
        $color = $palette[$index % count($palette)];
        $conicParts[] = $color.' '.$from.'deg '.$to.'deg';
        $from = $to;
    }

    $chartBackground = count($conicParts) > 0 ? 'conic-gradient('.implode(', ', $conicParts).')' : 'conic-gradient(#e2e8f0 0deg 360deg)';
@endphp

<div class="space-y-6">
    <div class="rounded-2xl bg-white p-5 shadow-sm lg:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Laporan Jemaat</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-800">ANALISIS DEMOGRAFI</h2>
                <p class="mt-1 text-sm text-slate-500">Ringkasan data jemaat berdasarkan filter usia, gender, dan bulan ulang tahun.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('notifications.index') }}" class="rounded-full bg-slate-100 p-2 text-slate-600" aria-label="Notifikasi">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a4 4 0 00-4 4v1.38a3 3 0 01-.88 2.12L4.7 9.92A1 1 0 005.4 11.6H14.6a1 1 0 00.7-1.7l-.42-.42A3 3 0 0114 7.38V6a4 4 0 00-4-4z" /><path d="M7.5 13a2.5 2.5 0 005 0h-5z" /></svg>
                </a>
                <a href="{{ route('settings.index') }}" class="rounded-full bg-slate-100 p-2 text-slate-600" aria-label="Pengaturan">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17a1 1 0 00-1.98 0l-.1.74a1 1 0 01-.78.85l-.72.16a1 1 0 00-.66 1.5l.4.63a1 1 0 010 .99l-.4.64a1 1 0 00.66 1.49l.72.16a1 1 0 01.79.85l.09.74a1 1 0 001.98 0l.1-.74a1 1 0 01.78-.85l.72-.16a1 1 0 00.66-1.5l-.4-.63a1 1 0 010-.99l.4-.64a1 1 0 00-.66-1.49l-.72-.16a1 1 0 01-.79-.85l-.09-.74zM10.5 12a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                </a>
                <a href="{{ route('reports.export.pdf', $pdfQuery) }}" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Cetak PDF</a>
                <a href="{{ route('members.export.excel', $excelQuery) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Export Excel</a>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-2 shadow-sm">
        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
            <a href="{{ route('reports.index', ['tab' => 'demografi']) }}" class="rounded-xl px-4 py-2 text-center text-sm font-semibold {{ ($activeTab ?? 'demografi') === 'demografi' ? 'bg-[#1e40af] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Analisis Demografi
            </a>
            <a href="{{ route('reports.index', ['tab' => 'status']) }}" class="rounded-xl px-4 py-2 text-center text-sm font-semibold {{ ($activeTab ?? 'demografi') === 'status' ? 'bg-[#1e40af] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Status Jemaat
            </a>
        </div>
    </div>

    @if(($activeTab ?? 'demografi') === 'demografi')
        <form method="GET" action="{{ route('reports.index') }}" class="rounded-2xl bg-white p-5 shadow-sm lg:p-6">
            <input type="hidden" name="tab" value="demografi">
            <p class="text-sm font-semibold text-slate-700">Parameter Filter</p>
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <label class="block text-sm">
                    <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Rentang Usia</span>
                    <select name="age_range" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-[#3b82f6] focus:outline-none">
                        @foreach($filterOptions['ageRanges'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['age_range'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis Kelamin</span>
                    <select name="gender" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-[#3b82f6] focus:outline-none">
                        @foreach($filterOptions['genders'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['gender'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Bulan Ulang Tahun</span>
                    <select name="birthday_month" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-[#3b82f6] focus:outline-none">
                        @foreach($filterOptions['birthdayMonths'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['birthday_month'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="mt-4 flex flex-wrap justify-end gap-2">
                <button type="button" id="reset-report-filters" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset Filter</button>
                <button type="submit" class="rounded-lg bg-[#3b82f6] px-4 py-2 text-sm font-semibold text-white hover:bg-[#2563eb]">Terapkan Filter</button>
            </div>
        </form>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border-l-8 border-[#3b82f6] bg-white p-5 shadow-sm lg:col-span-2">
                <p class="text-sm font-semibold text-slate-500">Total Hasil Filter</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ number_format($totalResults) }}</p>
                <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold {{ $percentageChange >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    @if($percentageChange >= 0)
                        <span>▲</span>
                    @else
                        <span>▼</span>
                    @endif
                    <span>{{ number_format(abs($percentageChange), 1) }}% dari bulan lalu</span>
                </div>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700">Distribusi Sektor</h3>
                <div class="mt-4 flex items-center gap-4">
                    <div class="h-24 w-24 rounded-full" style="background: {{ $chartBackground }}"></div>
                    <ul class="space-y-1 text-xs text-slate-600">
                        @foreach($segments as $index => $segment)
                            <li class="flex items-center gap-2">
                                <span class="inline-block h-2.5 w-2.5 rounded-full" style="background-color: {{ $palette[$index % count($palette)] }}"></span>
                                <span>{{ $segment->label }} ({{ $segment->percentage }}%)</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm lg:p-6">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold text-slate-700">Direktori Hasil</h3>
                <form method="GET" action="{{ route('reports.index') }}" class="w-full sm:max-w-sm">
                    <input type="hidden" name="tab" value="demografi">
                    <input type="hidden" name="age_range" value="{{ $filters['age_range'] ?? '' }}">
                    <input type="hidden" name="gender" value="{{ $filters['gender'] ?? '' }}">
                    <input type="hidden" name="birthday_month" value="{{ $filters['birthday_month'] ?? '' }}">
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#3b82f6] focus:outline-none">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">NAMA LENGKAP</th>
                            <th class="px-3 py-2 text-left font-semibold">USIA</th>
                            <th class="px-3 py-2 text-left font-semibold">L/P</th>
                            <th class="px-3 py-2 text-left font-semibold">TGL LAHIR</th>
                            <th class="px-3 py-2 text-left font-semibold">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                            @php
                                $age = $member->tanggal_lahir ? \Illuminate\Support\Carbon::parse($member->tanggal_lahir)->age : '-';
                                $initials = collect(explode(' ', trim($member->nama)))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
                            @endphp
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#dbeafe] text-xs font-bold text-[#1e40af]">{{ strtoupper($initials) }}</span>
                                        <span class="font-medium text-slate-800">{{ $member->nama }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-slate-600">{{ $age }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $member->jenis_kelamin }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $member->tanggal_lahir ? \Illuminate\Support\Carbon::parse($member->tanggal_lahir)->format('d M Y') : '-' }}</td>
                                <td class="px-3 py-3">
                                    <a href="{{ route('members.index', ['search' => $member->nama]) }}" class="text-xs font-semibold text-[#2563eb] hover:underline">Lihat Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-slate-500">Tidak ada data sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $members->links() }}
            </div>
        </div>
    @else
        <form method="GET" action="{{ route('reports.index') }}" class="rounded-2xl bg-white p-5 shadow-sm lg:p-6">
            <input type="hidden" name="tab" value="status">
            <p class="text-sm font-semibold text-slate-700">Filter Status Jemaat</p>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="block text-sm">
                    <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</span>
                    <select name="status" class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-[#3b82f6] focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="aktif" @selected(($filters['status'] ?? '') === 'aktif')>Aktif</option>
                        <option value="tidak_aktif" @selected(($filters['status'] ?? '') === 'tidak_aktif')>Tidak Aktif</option>
                        <option value="pindah" @selected(($filters['status'] ?? '') === 'pindah')>Pindah</option>
                    </select>
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari Jemaat</span>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama/kontak/alamat..." class="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-[#3b82f6] focus:outline-none">
                </label>
            </div>
            <div class="mt-4 flex flex-wrap justify-end gap-2">
                <button type="button" id="reset-report-filters" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset Filter</button>
                <button type="submit" class="rounded-lg bg-[#3b82f6] px-4 py-2 text-sm font-semibold text-white hover:bg-[#2563eb]">Terapkan Filter</button>
            </div>
        </form>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach($statusSummary as $item)
                <div class="rounded-2xl bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">Total Jemaat {{ $item->label }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-800">{{ number_format($item->total) }}</p>
                    <p class="mt-2 text-xs text-slate-500">Distribusi: {{ $item->percentage }}%</p>
                </div>
            @endforeach
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm lg:p-6">
            <h3 class="text-sm font-semibold text-slate-700">Daftar Status Jemaat</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Nama</th>
                            <th class="px-3 py-2 text-left font-semibold">Kontak</th>
                            <th class="px-3 py-2 text-left font-semibold">Alamat</th>
                            <th class="px-3 py-2 text-left font-semibold">Status</th>
                            <th class="px-3 py-2 text-left font-semibold">Tanggal Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statusMembers as $member)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-3 py-3 font-medium text-slate-800">{{ $member->nama }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $member->kontak }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $member->alamat }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $member->status === 'tidak_aktif' ? 'Tidak Aktif' : ucfirst(str_replace('_', ' ', $member->status)) }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $member->updated_at?->format('d M Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-slate-500">Tidak ada data sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $statusMembers->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('reset-report-filters')?.addEventListener('click', function () {
        window.location.href = '{{ route('reports.index', ['tab' => $activeTab ?? 'demografi']) }}';
    });
</script>
@endpush
