@extends('layouts.app')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Dashboard</h2>
    <p class="text-sm text-slate-500">Ringkasan data jemaat dan aktivitas terbaru.</p>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Total Jemaat</p>
        <p class="mt-1 text-3xl font-bold text-[#1e40af]">{{ $totalJemaat }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Jemaat Baru (30 Hari)</p>
        <p class="mt-1 text-3xl font-bold text-[#1e40af]">{{ $jemaatBaru }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Laki-laki</p>
        <p class="mt-1 text-3xl font-bold text-[#3b82f6]">{{ $lakiLaki }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Perempuan</p>
        <p class="mt-1 text-3xl font-bold text-[#3b82f6]">{{ $perempuan }}</p>
    </div>
</div>

<div class="mt-6 grid gap-4 xl:grid-cols-2">
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <h3 class="mb-4 font-semibold text-slate-800">Demografi</h3>
        <canvas id="demografiChart" height="140"></canvas>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <h3 class="mb-4 font-semibold text-slate-800">Proporsi Gender</h3>
        <canvas id="genderChart" height="140"></canvas>
    </div>
</div>

<div class="mt-6 rounded-xl bg-white p-5 shadow-sm">
    <h3 class="mb-4 font-semibold text-slate-800">Pendaftaran Terbaru</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold">Nama</th>
                    <th class="px-3 py-2 text-left font-semibold">Kontak</th>
                    <th class="px-3 py-2 text-left font-semibold">Gender</th>
                    <th class="px-3 py-2 text-left font-semibold">Status</th>
                    <th class="px-3 py-2 text-left font-semibold">Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentMembers as $member)
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2">{{ $member->nama }}</td>
                        <td class="px-3 py-2">{{ $member->kontak }}</td>
                        <td class="px-3 py-2">{{ $member->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        <td class="px-3 py-2">{{ ucfirst(str_replace('_', ' ', $member->status)) }}</td>
                        <td class="px-3 py-2">{{ $member->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-slate-500">Belum ada pendaftaran jemaat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const demografiCtx = document.getElementById('demografiChart');
    if (demografiCtx && window.Chart) {
        new Chart(demografiCtx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($demografi)),
                datasets: [{
                    data: @json(array_values($demografi)),
                    backgroundColor: ['#1e40af', '#3b82f6', '#93c5fd'],
                    borderRadius: 6
                }]
            },
            options: { plugins: { legend: { display: false } } }
        });
    }

    const genderCtx = document.getElementById('genderChart');
    if (genderCtx && window.Chart) {
        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [{{ $lakiLaki }}, {{ $perempuan }}],
                    backgroundColor: ['#1e40af', '#3b82f6']
                }]
            }
        });
    }
</script>
@endpush
