@extends('layouts.app')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-2xl font-bold">User Management</h2>
    <a href="{{ route('users.create') }}" class="rounded bg-slate-900 px-3 py-2 text-white">Tambah User</a>
</div>
<div class="overflow-x-auto rounded bg-white shadow">
    <table class="w-full text-sm">
        <thead class="bg-slate-50"><tr><th class="p-3 text-left">Nama</th><th class="p-3 text-left">Email</th><th class="p-3 text-left">Role</th><th class="p-3"></th></tr></thead>
        <tbody>
        @foreach($users as $user)
            <tr class="border-t"><td class="p-3">{{ $user->name }}</td><td class="p-3">{{ $user->email }}</td><td class="p-3">{{ ucfirst($user->role) }}</td><td class="p-3 text-right"><a class="text-amber-600" href="{{ route('users.edit', $user) }}">Edit</a> <form class="inline" method="post" action="{{ route('users.destroy', $user) }}">@csrf @method('DELETE')<button class="text-rose-600" onclick="return confirm('Hapus user?')">Hapus</button></form></td></tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
