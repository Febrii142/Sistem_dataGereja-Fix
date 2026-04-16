@extends('layouts.app')
@section('content')
<h2 class="mb-4 text-2xl font-bold">Edit User</h2>
<form action="{{ route('users.update', $user) }}" method="post" class="grid gap-4 rounded bg-white p-4 shadow md:grid-cols-2">
    @csrf @method('PUT')
    <input class="rounded border px-3 py-2" name="name" value="{{ $user->name }}" required>
    <input class="rounded border px-3 py-2" name="email" type="email" value="{{ $user->email }}" required>
    <select name="role_id" class="rounded border px-3 py-2" required>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" @selected($user->role_id === $role->id)>{{ $role->name }}</option>
        @endforeach
    </select>
    <input class="rounded border px-3 py-2" name="password" type="password" placeholder="Password baru (opsional)">
    <input class="rounded border px-3 py-2" name="password_confirmation" type="password" placeholder="Konfirmasi password baru">
    <button class="rounded bg-slate-900 px-4 py-2 text-white">Update</button>
</form>
@endsection
