<a href="{{ route('jemaat.dashboard') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 {{ request()->routeIs('jemaat.dashboard') ? 'bg-[#3b82f6]' : 'hover:bg-[#3b82f6]' }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3h16.5v6h-16.5V3Zm0 12h16.5v6h-16.5v-6Zm0-6h7.5v6h-7.5V9Zm9 0h7.5v6h-7.5V9Z" /></svg>
    <span>Dashboard Jemaat</span>
</a>
<a href="{{ route('jemaat.profile') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 {{ request()->routeIs('jemaat.profile*') ? 'bg-[#3b82f6]' : 'hover:bg-[#3b82f6]' }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0A9 9 0 1112 3a9 9 0 019.964 15.725Z" /></svg>
    <span>Profil Saya</span>
</a>
<a href="{{ route('jemaat.family') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 {{ request()->routeIs('jemaat.family*') ? 'bg-[#3b82f6]' : 'hover:bg-[#3b82f6]' }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.5V18a3 3 0 00-3-3H6a3 3 0 00-3 3v1.5m18-1.5V18a3 3 0 00-2.25-2.906m-3.75-8.344a3 3 0 11-6 0 3 3 0 016 0Zm6 8.344A3 3 0 0018 9m0 0a3 3 0 013 3v.75" /></svg>
    <span>Keluarga</span>
</a>
