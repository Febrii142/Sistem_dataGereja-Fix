@extends('layouts.app')
@section('content')
<div class="rounded bg-white p-6 shadow">
    <h1 class="text-2xl font-bold">SIM Jemaat Gereja</h1>
    <p class="mt-2">Silakan login untuk mengelola data jemaat.</p>
    <a href="{{ route('login') }}" class="mt-4 inline-block rounded bg-slate-900 px-4 py-2 text-white">Login</a>
</div>
@endsection
