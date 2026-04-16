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
                <a href="{{ route('categories.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('categories.*') ? 'bg-[#3b82f6]' : 'hover:bg-[#3b82f6]' }}">Kategori Jemaat</a>
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
                <a href="{{ route('settings.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('settings.*') ? 'bg-[#3b82f6]' : 'hover:bg-[#3b82f6]' }}">Settings</a>
            </nav>
            <form action="{{ route('logout') }}" method="post" class="mt-auto pt-6">
                @csrf
                <button class="w-full rounded-lg bg-rose-500 px-3 py-2 text-sm font-semibold hover:bg-rose-600">Logout</button>
            </form>
        </aside>
        <main class="flex min-h-screen flex-col">
            <header class="border-b border-slate-200 bg-white px-4 py-3 lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <form class="w-full max-w-lg" method="get" action="{{ route('members.index') }}">
                        <label>
                            <span class="sr-only">Search</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari data jemaat..." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm focus:border-[#3b82f6] focus:outline-none">
                        </label>
                    </form>
                    <div class="ml-auto flex items-center gap-3">
                        <a href="{{ route('notifications.index') }}" class="rounded-full bg-slate-100 p-2 text-slate-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#3b82f6]" aria-label="Notifikasi">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10 2a4 4 0 00-4 4v1.38a3 3 0 01-.88 2.12L4.7 9.92A1 1 0 005.4 11.6H14.6a1 1 0 00.7-1.7l-.42-.42A3 3 0 0114 7.38V6a4 4 0 00-4-4z" />
                                <path d="M7.5 13a2.5 2.5 0 005 0h-5z" />
                            </svg>
                        </a>
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
