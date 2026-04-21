@extends('layouts.app')
@section('content')
<div class="mb-6 rounded-xl bg-white p-5 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Administrative Staff</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola hak akses dan identitas staf operasional Digital Vestry.</p>
        </div>
        <form method="get" class="w-full md:w-80">
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Cari nama atau email..."
                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#1e40af] focus:outline-none focus:ring-2 focus:ring-blue-100"
            >
        </form>
    </div>
    <div class="mt-4 grid gap-3 sm:grid-cols-2">
        <div class="rounded-lg bg-blue-50 p-4">
            <p class="text-xs font-semibold tracking-wide text-blue-700">TOTAL USER</p>
            <p class="mt-1 text-2xl font-bold text-[#1e40af]">{{ $totalUsers }}</p>
        </div>
        <div class="rounded-lg bg-teal-50 p-4">
            <p class="text-xs font-semibold tracking-wide text-teal-700">ADMIN ROLES</p>
            <p class="mt-1 text-2xl font-bold text-teal-700">{{ $adminRolesCount }}</p>
        </div>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <section class="lg:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800">Staff Directory</h3>
            <a href="{{ route('users.create') }}" class="text-sm font-semibold text-[#1e40af] hover:underline">Buka form klasik</a>
        </div>
        <div class="space-y-3">
            @forelse($users as $user)
                @php
                    $initials = \Illuminate\Support\Str::of($user->name)
                        ->explode(' ')
                        ->filter()
                        ->take(2)
                        ->map(fn (string $part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
                        ->join('');
                @endphp
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-[#1e40af]">
                                {{ $initials }}
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-slate-900">{{ $user->name }}</h4>
                                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $user->role_badge_class }}">{{ $user->role_display_label }}</span>
                                    <span class="text-xs text-slate-500">Last active: {{ $user->last_active ?? 'Belum tercatat' }}</span>
                                </div>
                            </div>
                        </div>
                        <details class="relative">
                            <summary class="cursor-pointer list-none rounded-lg bg-slate-100 px-2 py-1 text-slate-600 hover:bg-slate-200">⋯</summary>
                            <div class="absolute right-0 z-10 mt-2 w-36 rounded-lg border border-slate-200 bg-white p-1 text-sm shadow-lg">
                                <a href="{{ route('users.edit', $user) }}" class="block rounded px-2 py-1 text-slate-700 hover:bg-slate-100">Edit</a>
                                <a href="{{ route('users.edit', ['user' => $user, 'focus' => 'role']) }}" class="block rounded px-2 py-1 text-slate-700 hover:bg-slate-100">Change Role</a>
                                <form method="post" action="{{ route('users.destroy', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="block w-full rounded px-2 py-1 text-left text-rose-600 hover:bg-rose-50" onclick="return confirm('Hapus user?')">Delete</button>
                                </form>
                            </div>
                        </details>
                    </div>
                </article>
            @empty
                <div class="rounded-xl bg-white p-6 text-center text-slate-500 shadow-sm">Belum ada user admin/staff.</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </section>

    <aside class="space-y-4">
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-800">Tambah User Baru</h3>
            <p class="mt-1 text-sm text-slate-500">Daftarkan staff baru ke dalam platform Digital Vestry.</p>
            <form action="{{ route('users.store') }}" method="post" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" name="name" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                    <input class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                    <select name="role_id" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) old('role_id') === (string) $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                    <input class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" type="password" name="password" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Password Confirmation</label>
                    <input class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" type="password" name="password_confirmation" required>
                </div>
                <button class="w-full rounded-lg bg-[#1e40af] px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Simpan &amp; Undang User</button>
            </form>
            <p class="mt-3 text-xs text-slate-500">User akan menerima email undangan untuk melakukan aktivasi kata sandi mandiri.</p>
            @if($errors->any())
                <ul class="mt-3 list-disc space-y-1 pl-5 text-xs text-rose-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <h3 class="text-base font-semibold text-slate-800">Panduan Hak Akses</h3>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <li class="flex items-start gap-2"><span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-xs text-blue-700">✓</span> <span><strong>Admin:</strong> Kontrol penuh sistem &amp; pengaturan finansial</span></li>
                <li class="flex items-start gap-2"><span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-teal-100 text-xs text-teal-700">✓</span> <span><strong>Staff:</strong> Manajemen data jemaat &amp; laporan umum</span></li>
            </ul>
        </div>
    </aside>
</div>
@endsection
