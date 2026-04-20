@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="rounded-xl bg-white p-6 shadow">
        <h2 class="text-2xl font-bold text-blue-900">Form Pendaftaran Jemaat</h2>
        <p class="mt-1 text-sm text-slate-600">Isi data lengkap, akun akan dibuat otomatis dengan status pending approval.</p>
    </div>

    <form method="post" action="{{ route('register.store') }}" class="space-y-6 rounded-xl bg-white p-6 shadow">
        @csrf

        <div class="step space-y-4" data-step="1">
            <h3 class="text-lg font-semibold text-blue-900">1. Data Pribadi</h3>
            <div class="grid gap-4 md:grid-cols-2">
                <input name="name" value="{{ old('name') }}" placeholder="Nama lengkap" class="rounded border px-3 py-2" required>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="rounded border px-3 py-2" required>
                <input name="no_telepon" value="{{ old('no_telepon') }}" placeholder="No. Telepon" class="rounded border px-3 py-2" required>
                <select name="jenis_kelamin" class="rounded border px-3 py-2" required>
                    <option value="">Jenis kelamin</option>
                    <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                </select>
                <input name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Tempat lahir" class="rounded border px-3 py-2" required>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="rounded border px-3 py-2" required>
            </div>
        </div>

        <div class="step hidden space-y-4" data-step="2">
            <h3 class="text-lg font-semibold text-blue-900">2. Alamat Lengkap</h3>
            <div class="grid gap-4 md:grid-cols-2">
                <textarea name="alamat" rows="3" placeholder="Alamat rumah" class="rounded border px-3 py-2 md:col-span-2" required>{{ old('alamat') }}</textarea>
                <input name="kota" value="{{ old('kota') }}" placeholder="Kota" class="rounded border px-3 py-2" required>
                <input name="kode_pos" value="{{ old('kode_pos') }}" placeholder="Kode pos" class="rounded border px-3 py-2" required>
            </div>
        </div>

        <div class="step hidden space-y-4" data-step="3">
            <h3 class="text-lg font-semibold text-blue-900">3. Status Rohani</h3>
            <div class="grid gap-4 md:grid-cols-2">
                <select name="status_baptis" class="rounded border px-3 py-2" required>
                    <option value="">Status baptis</option>
                    <option value="sudah" @selected(old('status_baptis') === 'sudah')>Sudah</option>
                    <option value="belum" @selected(old('status_baptis') === 'belum')>Belum</option>
                </select>
                <input name="kelas_katekisasi" value="{{ old('kelas_katekisasi') }}" placeholder="Kelas katekisasi (opsional)" class="rounded border px-3 py-2">
            </div>
        </div>

        <div class="flex justify-between">
            <button type="button" id="prevStep" class="rounded bg-slate-200 px-4 py-2 font-semibold text-slate-700">Kembali</button>
            <button type="button" id="nextStep" class="rounded bg-blue-700 px-4 py-2 font-semibold text-white">Lanjut</button>
            <button type="submit" id="submitStep" class="hidden rounded bg-emerald-700 px-4 py-2 font-semibold text-white">Kirim Pendaftaran</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const steps = Array.from(document.querySelectorAll('.step'));
    let currentStep = 1;
    const prevButton = document.getElementById('prevStep');
    const nextButton = document.getElementById('nextStep');
    const submitButton = document.getElementById('submitStep');

    const updateSteps = () => {
        steps.forEach((step) => step.classList.toggle('hidden', Number(step.dataset.step) !== currentStep));
        prevButton.classList.toggle('invisible', currentStep === 1);
        nextButton.classList.toggle('hidden', currentStep === steps.length);
        submitButton.classList.toggle('hidden', currentStep !== steps.length);
    };

    prevButton.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateSteps();
        }
    });

    nextButton.addEventListener('click', () => {
        if (currentStep < steps.length) {
            currentStep++;
            updateSteps();
        }
    });

    updateSteps();
</script>
@endpush
