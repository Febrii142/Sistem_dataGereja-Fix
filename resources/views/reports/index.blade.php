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
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Pusat Analitik & Wawasan</p>
                <h2 class="mt-1 text-3xl font-bold text-slate-800">Laporan Jemaat</h2>
                <p class="mt-1 text-sm text-slate-500">Akses visualisasi dan rekap data untuk mendukung pengambilan keputusan pelayanan jemaat.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('reports.export.pdf', $pdfQuery) }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cetak PDF</a>
                <a href="{{ route('members.export.excel', $excelQuery) }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Export Excel</a>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-2 shadow-sm">
        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
            <a href="{{ route('reports.index', ['tab' => 'demografi']) }}" class="rounded-xl px-4 py-2 text-center text-sm font-semibold {{ ($activeTab ?? 'demografi') === 'demografi' ? 'bg-[#1e40af] text-white' : 'text-slate-600 hover:bg-slate-50' }}">
                Analisis Demografi
            </a>
            <a href="{{ route('reports.index', ['tab' => 'status']) }}" class="rounded-xl px-4 py-2 text-center text-sm font-semibold {{ ($activeTab ?? 'demografi') === 'status' ? 'bg-[#1e40af] text-white' : 'text-slate-600 hover:bg-slate-50' }}">
                Status Jemaat
            </a>
        </div>
    </div>

    @if(($activeTab ?? 'demografi') === 'demografi')
        <form method="GET" action="{{ route('reports.index') }}" class="rounded-2xl bg-white p-5 shadow-sm lg:p-6">
            <input type="hidden" name="tab" value="demografi">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600">📊</div>
                    <div>
                        <p class="text-base font-semibold text-slate-800">Filter Analitik Lanjutan</p>
                        <p class="text-xs text-slate-500">Atur parameter usia, jenis kelamin, dan bulan ulang tahun.</p>
                    </div>
                </div>
                <button type="button" id="reset-report-filters" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Reset Filter</button>
            </div>
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
            <div class="mt-5 flex justify-center">
                <button type="submit" class="rounded-lg bg-[#1e40af] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1d4ed8]">Terapkan Filter &amp; Muat Data</button>
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
                <div>
                    <h3 class="text-sm font-semibold text-slate-700">Direktori Hasil</h3>
                    <p class="text-xs text-slate-500">Daftar jemaat sesuai filter yang dipilih.</p>
                </div>
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
                                $age = $member->tanggal_lahir ? \\Illuminate\\Support\\Carbon::parse($member->tanggal_lahir)->age : '-';
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
                                <td class="px-3 py-3 text-slate-600">{{ $member->tanggal_lahir ? \\Illuminate\\Support\\Carbon::parse($member->tanggal_lahir)->format('d M Y') : '-' }}</td>
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
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-base font-semibold text-slate-800">Filter Status Jemaat</p>
                    <p class="text-xs text-slate-500">Gunakan filter status dan pencarian untuk rekap cepat.</p>
                </div>
                <button type="button" id="reset-report-filters" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Reset Filter</button>
            </div>
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
            <div class="mt-5 flex justify-center">
                <button type="submit" class="rounded-lg bg-[#1e40af] px-6 py-3 text-sm font-semibold text-white hover:bg-[#1d4ed8]">Terapkan Filter &amp; Muat Data</button>
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
    const reportTab = @json($activeTab ?? 'demografi');
    document.getElementById('reset-report-filters')?.addEventListener('click', function () {
        window.location.href = '{{ route('reports.index') }}?tab=' + encodeURIComponent(reportTab);
    });
</script>
@endpush
