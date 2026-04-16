@extends('layouts.app')
@section('content')
<h2 class="mb-4 text-2xl font-bold">Input Kehadiran</h2>
<form method="post" action="{{ route('attendances.store') }}" class="grid gap-4 rounded bg-white p-4 shadow md:grid-cols-2">
    @csrf
    <select name="member_id" class="rounded border px-3 py-2" required>
        @foreach($members as $member)
            <option value="{{ $member->id }}">{{ $member->nama }}</option>
        @endforeach
    </select>
    <input type="date" name="service_date" value="{{ date('Y-m-d') }}" class="rounded border px-3 py-2" required>
    <select name="hadir" class="rounded border px-3 py-2" required>
        <option value="1">Hadir</option>
        <option value="0">Tidak Hadir</option>
    </select>
    <input type="text" name="keterangan" placeholder="Keterangan" class="rounded border px-3 py-2">
    <button class="rounded bg-slate-900 px-4 py-2 text-white">Simpan</button>
</form>
@endsection
