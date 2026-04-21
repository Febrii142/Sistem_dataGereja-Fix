@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <h2 class="text-2xl font-bold text-slate-800">Verifikasi Jemaat Baru</h2>
        <p class="mt-1 text-sm text-slate-500">Ada {{ $pendingUsers->total() }} pendaftaran baru yang memerlukan peninjauan administratif.</p>
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-600">
                <tr>
                    <th class="px-3 py-2">Nama</th>
                    <th class="px-3 py-2">Email</th>
                    <th class="px-3 py-2">Kota</th>
                    <th class="px-3 py-2">Status Baptis</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingUsers as $user)
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2">{{ $user->name }}</td>
                        <td class="px-3 py-2">{{ $user->email }}</td>
                        <td class="px-3 py-2">{{ $user->jemaat?->kota ?? '-' }}</td>
                        <td class="px-3 py-2">{{ ucfirst($user->jemaat?->status_baptis ?? '-') }}</td>
                        <td class="space-x-2 px-3 py-2">
                            <form method="post" action="{{ route('members.verification.approve', $user) }}" class="inline">
                                @csrf
                                <button class="rounded bg-emerald-600 px-3 py-1 font-semibold text-white hover:bg-emerald-700">Verifikasi</button>
                            </form>
                            <form method="post" action="{{ route('members.verification.reject', $user) }}" class="inline">
                                @csrf
                                <button class="rounded bg-rose-600 px-3 py-1 font-semibold text-white hover:bg-rose-700" title="Tolak">X</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-slate-500">Tidak ada registrasi pending.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $pendingUsers->links() }}
    </div>
</div>
@endsection
