<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIM Jemaat Gereja' }}</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-slate-100 text-slate-900">
@auth
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <aside class="flex flex-col bg-[#1e40af] p-4 text-white">
            <h1 class="mb-6 text-xl font-bold">SIM Jemaat</h1>
            <a href="{{ route('members.create') }}" class="mb-4 inline-flex items-center justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-[#1e40af] hover:bg-slate-100">
                + Tambah Data Baru
            </a>
            <nav class="space-y-2 text-sm">
                <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-[#3b82f6]' : 'hover:bg-[#3b82f6]' }}">Dashboard</a>
                <a href="{{ route('members.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('members.*') ? 'bg-[#3b82f6]' : 'hover:bg-[#3b82f6]' }}">Jemaat</a>
                <a href="{{ route('attendances.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('attendances.*') ? 'bg-[#3b82f6]' : 'hover:bg-[#3b82f6]' }}">Kehadiran</a>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('users.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('users.*') ? 'bg-[#3b82f6]' : 'hover:bg-[#3b82f6]' }}">User Management</a>
                @else
                    <span class="block cursor-not-allowed rounded-lg px-3 py-2 text-slate-200">User Management</span>
                @endif
                @if(in_array(auth()->user()->role, ['admin','pendeta','koordinator'], true))
                    <a href="{{ route('reports.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('reports.*') ? 'bg-[#3b82f6]' : 'hover:bg-[#3b82f6]' }}">Laporan</a>
                @else
                    <span class="block cursor-not-allowed rounded-lg px-3 py-2 text-slate-200">Laporan</span>
                @endif
                <span class="block cursor-not-allowed rounded-lg px-3 py-2 text-slate-200">Settings</span>
            </nav>
            <form action="{{ route('logout') }}" method="post" class="mt-auto pt-6">
                @csrf
                <button class="w-full rounded-lg bg-rose-500 px-3 py-2 text-sm font-semibold hover:bg-rose-600">Logout</button>
            </form>
        </aside>
        <main class="flex min-h-screen flex-col">
            <header class="border-b border-slate-200 bg-white px-4 py-3 lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <label class="w-full max-w-lg">
                        <span class="sr-only">Search</span>
                        <input type="text" placeholder="Cari data jemaat..." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm focus:border-[#3b82f6] focus:outline-none">
                    </label>
                    <div class="ml-auto flex items-center gap-3">
                        <button type="button" class="rounded-full bg-slate-100 p-2 text-lg" aria-label="Notifikasi">🔔</button>
                        <div class="rounded-lg bg-slate-100 px-3 py-2 text-sm">
                            <p class="font-semibold">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                    </div>
                </div>
            </header>
            <section class="flex-1 p-4 lg:p-8">
                @if(session('success'))
                    <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
                        <ul class="list-inside list-disc">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </section>
        </main>
    </div>
@else
    <main class="min-h-screen p-4 lg:p-8">
        @if(session('success'))
            <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
                <ul class="list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
@endauth
@stack('scripts')
</body>
</html>
