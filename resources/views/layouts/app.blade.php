<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIM Jemaat Gereja' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
<div class="min-h-screen lg:grid lg:grid-cols-[240px_1fr]">
    <aside class="bg-slate-900 p-4 text-white">
        <h1 class="mb-6 text-xl font-bold">SIM Jemaat</h1>
        @auth
            <nav class="space-y-2 text-sm">
                <a href="{{ route('dashboard') }}" class="block rounded px-3 py-2 hover:bg-slate-800">Dashboard</a>
                <a href="{{ route('members.index') }}" class="block rounded px-3 py-2 hover:bg-slate-800">Data Jemaat</a>
                <a href="{{ route('attendances.index') }}" class="block rounded px-3 py-2 hover:bg-slate-800">Kehadiran</a>
                @if(in_array(auth()->user()->role, ['admin','pendeta','koordinator'], true))
                    <a href="{{ route('reports.index') }}" class="block rounded px-3 py-2 hover:bg-slate-800">Laporan</a>
                @endif
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('users.index') }}" class="block rounded px-3 py-2 hover:bg-slate-800">User Management</a>
                @endif
                <form action="{{ route('logout') }}" method="post" class="pt-4">
                    @csrf
                    <button class="w-full rounded bg-rose-500 px-3 py-2 text-sm">Logout</button>
                </form>
            </nav>
        @endauth
    </aside>
    <main class="p-4 lg:p-8">
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
</div>
</body>
</html>
