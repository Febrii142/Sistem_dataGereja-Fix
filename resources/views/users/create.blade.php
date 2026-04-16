@extends('layouts.app')
@section('content')
<h2 class="mb-4 text-2xl font-bold">Tambah User</h2>
<form action="{{ route('users.store') }}" method="post" class="grid gap-4 rounded bg-white p-4 shadow md:grid-cols-2">
    @csrf
    <input class="rounded border px-3 py-2" name="name" placeholder="Nama" required>
    <input class="rounded border px-3 py-2" name="email" type="email" placeholder="Email" required>
    <select name="role" class="rounded border px-3 py-2" required>
        <option value="admin">Admin</option><option value="pendeta">Pendeta</option><option value="koordinator">Koordinator</option><option value="user">User Biasa</option>
    </select>
    <input class="rounded border px-3 py-2" name="password" type="password" placeholder="Password" required>
    <input class="rounded border px-3 py-2" name="password_confirmation" type="password" placeholder="Konfirmasi Password" required>
    <button class="rounded bg-slate-900 px-4 py-2 text-white">Simpan</button>
</form>
@endsection
