@extends('layouts.app')
@section('content')
<div class="mx-auto mt-12 max-w-lg rounded bg-white p-6 shadow">
    <h2 class="mb-4 text-xl font-semibold">Register</h2>
    <form method="post" action="{{ route('register.store') }}" class="grid gap-4">
        @csrf
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama" class="w-full rounded border px-3 py-2" required>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="w-full rounded border px-3 py-2" required>
        <input type="text" value="Member" class="w-full rounded border bg-slate-50 px-3 py-2 text-slate-500" disabled>
        <input type="password" name="password" placeholder="Password" class="w-full rounded border px-3 py-2" required>
        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" class="w-full rounded border px-3 py-2" required>
        <button class="rounded bg-slate-900 px-3 py-2 text-white">Daftar</button>
    </form>
</div>
@endsection
