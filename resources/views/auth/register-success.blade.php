@extends('layouts.app')

@section('content')
<div class="mx-auto mt-12 max-w-2xl rounded-2xl bg-white p-6 shadow sm:p-8">
    <h1 class="text-2xl font-bold text-blue-900">Pendaftaran Berhasil</h1>
    <p class="mt-2 text-slate-600">Akun Anda berhasil dibuat. Simpan informasi berikut untuk login.</p>

    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <p class="text-sm text-slate-500">Email</p>
        <p class="mt-1 break-all text-lg font-semibold text-slate-900">{{ $email }}</p>
    </div>

    <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex-1">
                <p class="text-sm text-blue-700">Password Sementara</p>
                <input id="temporaryPasswordField" type="text" readonly value="{{ $password }}" class="mt-1 w-full rounded-lg border border-blue-200 bg-white px-3 py-2 text-lg font-semibold tracking-wide text-blue-900">
            </div>
            <button id="copyPasswordButton" type="button" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                Copy Password
            </button>
        </div>
    </div>

    <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">
        Simpan password ini untuk login.
    </p>

    <a href="{{ route('login') }}" class="mt-6 inline-flex w-full items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
        Login
    </a>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const copyButton = document.getElementById('copyPasswordButton');
    const passwordField = document.getElementById('temporaryPasswordField');

    if (!copyButton || !passwordField) {
        return;
    }

    copyButton.addEventListener('click', async function () {
        try {
            await navigator.clipboard.writeText(passwordField.value);
            copyButton.textContent = 'Copied!';
            setTimeout(function () {
                copyButton.textContent = 'Copy Password';
            }, 1500);
        } catch (error) {
            passwordField.select();
            document.execCommand('copy');
            copyButton.textContent = 'Copied!';
            setTimeout(function () {
                copyButton.textContent = 'Copy Password';
            }, 1500);
        }
    });
});
</script>
@endpush
