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
    <style>
        .sidebar-gradient {
            background: linear-gradient(180deg, #4b7ec9 0%, #5a8dd8 60%, #6b98e0 100%);
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: rgba(255,255,255,0.85);
            transition: all 0.15s ease;
            border-left: 2.5px solid transparent;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.12);
            color: #ffffff;
        }
        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: #ffffff;
            border-left-color: #ffffff;
            font-weight: 600;
        }
        .nav-link svg {
            flex-shrink: 0;
            opacity: 0.9;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900">
@auth
    @php($user = auth()->user())
    <div class="min-h-screen lg:grid lg:grid-cols-[200px_1fr]">
        <aside class="sidebar-gradient flex flex-col px-2.5 py-3.5 text-white">
            {{-- Brand / Logo --}}
            <div class="mb-4 flex items-center gap-2 px-1">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3L2 9l10 6 10-6-10-6z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 17l10 6 10-6" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h1 class="text-sm font-bold leading-tight text-white truncate">SIM</h1>
                    <p class="text-[10px] text-white/60 leading-none">Gereja</p>
                </div>
            </div>

            {{-- Divider --}}
            <div class="mb-3 border-t border-white/10"></div>

            <nav class="flex flex-col gap-0.5 text-xs flex-1 overflow-y-auto">
                @if($user->hasRole(['Jemaat Gereja', 'jemaat']))
                    <a href="{{ route('jemaat.dashboard') }}"
                       class="nav-link {{ request()->routeIs('jemaat.dashboard') ? 'active' : '' }}"
                       title="Dashboard">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4" />
                        </svg>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>
                    <a href="{{ route('jemaat.profile') }}"
                       class="nav-link {{ request()->routeIs('jemaat.profile*') ? 'active' : '' }}"
                       title="Profil">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="hidden sm:inline">Profil</span>
                    </a>
                    <a href="{{ route('jemaat.keluarga.index') }}"
                       class="nav-link {{ request()->routeIs('jemaat.keluarga*') ? 'active' : '' }}"
                       title="Keluarga">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 2a3 3 0 110 6 3 3 0 010-6z" />
                        </svg>
                        <span class="hidden sm:inline">Keluarga</span>
                    </a>
                    @if($user->status === 'approved')
                        <a href="{{ route('jemaat.profile.edit') }}"
                           class="nav-link {{ request()->routeIs('jemaat.profile.edit') ? 'active' : '' }}"
                           title="Pengaturan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="hidden sm:inline">Pengaturan</span>
                        </a>
                    @endif
                @else
                    @if($user->hasPermission('view_dashboard'))
                        <a href="{{ route('dashboard') }}"
                           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           title="Dashboard">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4" />
                            </svg>
                            <span class="hidden sm:inline">Dashboard</span>
                        </a>
                    @endif
                    @if($user->hasPermission('view_members'))
                        <a href="{{ route('members.index') }}"
                           class="nav-link {{ request()->routeIs('members.*') && !request()->routeIs('members.verification.*') ? 'active' : '' }}"
                           title="Jemaat">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 2a3 3 0 110 6 3 3 0 010-6z" />
                            </svg>
                            <span class="hidden sm:inline">Jemaat</span>
                        </a>
                    @endif
                    @if($user->hasPermission('view_users'))
                        <a href="{{ route('users.index') }}"
                           class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                           title="User">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="hidden sm:inline">User</span>
                        </a>
                    @endif
                    @if($user->hasPermission('view_reports'))
                        <a href="{{ route('reports.index') }}"
                           class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                           title="Laporan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="hidden sm:inline">Laporan</span>
                        </a>
                    @endif
                    @if($user->hasPermission('view_settings'))
                        <a href="{{ route('settings.index') }}"
                           class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                           title="Pengaturan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="hidden sm:inline">Pengaturan</span>
                        </a>
                    @endif
                @endif
            </nav>

            {{-- Logout --}}
            <div class="border-t border-white/10 pt-3">
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button title="Logout" class="flex w-full items-center justify-center gap-1.5 rounded-md bg-white/15 px-2 py-1.5 text-xs font-medium text-white transition hover:bg-white/25">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </div>
        </aside>
        <main class="flex min-h-screen flex-col">
            <header class="border-b border-slate-200 bg-white px-4 py-3 shadow-sm lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <form class="w-full max-w-lg" method="get" action="{{ $user->hasPermission('view_members') ? route('members.index') : ($user->hasPermission('view_jemaat_dashboard') ? route('jemaat.dashboard') : '#') }}">
                        <label class="relative block">
                            <span class="sr-only">Search</span>
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari data jemaat..." class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-10 pr-4 text-sm transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </label>
                    </form>
                    <div class="ml-auto flex items-center gap-3">
                        <a href="{{ route('notifications.index') }}" class="relative rounded-full bg-slate-100 p-2 text-slate-500 transition hover:bg-slate-200 hover:text-slate-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-9.33-4.993M15 17H9m6 0a3 3 0 01-6 0m-2.67-1.405A2.032 2.032 0 006 14.158V11a6 6 0 009.33 4.993" />
                            </svg>
                        </a>
                    </div>
                </div>
            </header>
            <section class="flex-1 p-4 lg:p-8">
                @if(session('success'))
                    <div class="mb-4 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm">{{ session('success') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 flex items-start gap-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <ul class="list-inside list-disc text-sm">
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
            <div class="mb-4 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 flex items-start gap-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <ul class="list-inside list-disc text-sm">
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