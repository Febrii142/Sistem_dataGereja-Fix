@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl">
    <div class="rounded-2xl bg-white p-4 shadow sm:p-8">
        <div class="grid gap-6 lg:grid-cols-[260px_1fr]">
            <aside class="rounded-xl bg-slate-50 p-4 sm:p-6">
                <h2 class="text-lg font-semibold text-blue-900">Form Pendaftaran</h2>
                <p class="mt-1 text-sm text-slate-600">Lengkapi data pada setiap tahap.</p>

                <div class="mt-6 space-y-3">
                    <div class="step-indicator flex items-center gap-3 rounded-lg border border-blue-100 bg-blue-50 p-3" data-step-indicator="1">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-blue-700 text-sm font-semibold text-white">1</span>
                        <span class="text-sm font-medium text-blue-900">Data Pribadi</span>
                    </div>
                    <div class="step-indicator flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-3" data-step-indicator="2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-300 text-sm font-semibold text-white">2</span>
                        <span class="text-sm font-medium text-slate-700">Alamat Lengkap</span>
                    </div>
                    <div class="step-indicator flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-3" data-step-indicator="3">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-300 text-sm font-semibold text-white">3</span>
                        <span class="text-sm font-medium text-slate-700">Status Rohani</span>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="h-2 w-full rounded-full bg-slate-200">
                        <div id="registrationProgressBar" class="h-2 rounded-full bg-blue-700 transition-all duration-200" style="width: 33%;"></div>
                    </div>
                </div>
            </aside>

            <form method="post" action="{{ route('register.store') }}" data-register-form class="space-y-6">
                @csrf

                <section class="register-step space-y-4" data-step="1">
                    <h3 class="text-xl font-semibold text-blue-900">Data Pribadi</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                            <input id="name" name="name" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                        </div>
                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                        </div>
                        <div>
                            <label for="no_telepon" class="mb-1 block text-sm font-medium text-slate-700">No. Telepon</label>
                            <input id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                        </div>
                        <div>
                            <label for="jenis_kelamin" class="mb-1 block text-sm font-medium text-slate-700">Jenis Kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                                <option value="">Pilih jenis kelamin</option>
                                <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                                <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="tempat_lahir" class="mb-1 block text-sm font-medium text-slate-700">Tempat Lahir</label>
                            <input id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                        </div>
                        <div>
                            <label for="tanggal_lahir" class="mb-1 block text-sm font-medium text-slate-700">Tanggal Lahir</label>
                            <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                        </div>
                    </div>
                </section>

                <section class="register-step hidden space-y-4" data-step="2">
                    <h3 class="text-xl font-semibold text-blue-900">Alamat Lengkap</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="alamat" class="mb-1 block text-sm font-medium text-slate-700">Alamat Rumah</label>
                            <textarea id="alamat" name="alamat" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>{{ old('alamat') }}</textarea>
                        </div>
                        <div>
                            <label for="kota" class="mb-1 block text-sm font-medium text-slate-700">Kota/Kabupaten</label>
                            <input id="kota" name="kota" value="{{ old('kota') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                        </div>
                        <div>
                            <label for="kode_pos" class="mb-1 block text-sm font-medium text-slate-700">Kode Pos</label>
                            <input id="kode_pos" name="kode_pos" value="{{ old('kode_pos') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                        </div>
                    </div>
                </section>

                <section class="register-step hidden space-y-4" data-step="3">
                    <h3 class="text-xl font-semibold text-blue-900">Status Rohani</h3>

                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="mb-3 text-sm font-medium text-slate-700">Apakah Anda sudah dibaptis secara sah?</p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="cursor-pointer">
                                <input type="radio" class="peer sr-only" name="baptism_status" value="sudah_dibaptis" {{ old('baptism_status') === 'sudah_dibaptis' ? 'checked' : '' }} required>
                                <span class="block rounded-lg border border-slate-300 px-4 py-3 text-center text-sm font-semibold text-slate-700 peer-checked:border-blue-700 peer-checked:bg-blue-50 peer-checked:text-blue-800">Sudah Dibaptis</span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" class="peer sr-only" name="baptism_status" value="belum_dibaptis" {{ old('baptism_status') === 'belum_dibaptis' ? 'checked' : '' }} required>
                                <span class="block rounded-lg border border-slate-300 px-4 py-3 text-center text-sm font-semibold text-slate-700 peer-checked:border-blue-700 peer-checked:bg-blue-50 peer-checked:text-blue-800">Belum Dibaptis</span>
                            </label>
                        </div>
                    </div>

                    <div id="baptizedInfoSection" class="space-y-4 rounded-xl border border-slate-200 p-4">
                        <h4 class="font-semibold text-slate-800">Data Baptis</h4>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="baptism_date" class="mb-1 block text-sm font-medium text-slate-700">Tanggal Baptis</label>
                                <input id="baptism_date" type="date" name="baptism_date" value="{{ old('baptism_date') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                            </div>
                            <div>
                                <label for="baptism_location" class="mb-1 block text-sm font-medium text-slate-700">Tempat Baptis</label>
                                <input id="baptism_location" name="baptism_location" value="{{ old('baptism_location') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                            </div>
                        </div>
                    </div>

                    <div id="catechismSection" class="hidden space-y-4 rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <h4 class="font-semibold text-blue-900">Pendaftaran Kelas Katekisasi & Baptisan</h4>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="catechism_batch" class="mb-1 block text-sm font-medium text-slate-700">Pilihan Gelombang Baptis</label>
                                <select id="catechism_batch" name="catechism_batch" data-catechism-required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                                    <option value="">Pilih gelombang</option>
                                    <option value="Gelombang 1 (Januari - April)" @selected(old('catechism_batch') === 'Gelombang 1 (Januari - April)')>Gelombang 1 (Januari - April)</option>
                                    <option value="Gelombang 2 (Mei - Agustus)" @selected(old('catechism_batch') === 'Gelombang 2 (Mei - Agustus)')>Gelombang 2 (Mei - Agustus)</option>
                                    <option value="Gelombang 3 (September - Desember)" @selected(old('catechism_batch') === 'Gelombang 3 (September - Desember)')>Gelombang 3 (September - Desember)</option>
                                </select>
                            </div>
                            <div>
                                <label for="parent_guardian_name" class="mb-1 block text-sm font-medium text-slate-700">Nama Orang Tua/Wali</label>
                                <input id="parent_guardian_name" name="parent_guardian_name" value="{{ old('parent_guardian_name') }}" data-catechism-required class="w-full rounded-lg border border-slate-300 px-3 py-2">
                            </div>
                        </div>
                    </div>
                </section>

                <div class="flex items-center justify-between border-t border-slate-200 pt-4">
                    <button type="button" id="registerBackButton" class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Back</button>
                    <div class="flex items-center gap-2">
                        <button type="button" id="registerNextButton" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Next</button>
                        <button type="submit" id="registerSubmitButton" class="hidden rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Kirim Pendaftaran</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/register-form.js') }}" defer></script>
@endpush
