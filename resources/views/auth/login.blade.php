@extends('layouts.app')
@section('content')
<div class="mx-auto mt-12 max-w-md rounded bg-white p-6 shadow">
    <h2 class="mb-4 text-xl font-semibold">Login</h2>
    <form method="post" action="{{ route('login.attempt') }}" class="space-y-4">
        @csrf
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="w-full rounded border px-3 py-2" required>
        <input type="password" name="password" placeholder="Password" class="w-full rounded border px-3 py-2" required>
        <button class="w-full rounded bg-slate-900 px-3 py-2 text-white">Masuk</button>
    </form>
    <p class="mt-4 text-sm">Belum punya akun? <a class="text-blue-600" href="{{ route('register') }}">Register</a></p>
</div>
@endsection
