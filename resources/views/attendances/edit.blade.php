@extends('layouts.app')
@section('content')
<h2 class="mb-4 text-2xl font-bold">Edit Kehadiran</h2>
<form method="post" action="{{ route('attendances.update', $attendance) }}" class="grid gap-4 rounded bg-white p-4 shadow md:grid-cols-2">
    @csrf @method('PUT')
    <select name="member_id" class="rounded border px-3 py-2" required>
        @foreach($members as $member)
            <option value="{{ $member->id }}" @selected($attendance->member_id === $member->id)>{{ $member->nama }}</option>
        @endforeach
    </select>
    <input type="date" name="service_date" value="{{ $attendance->service_date->format('Y-m-d') }}" class="rounded border px-3 py-2" required>
    <select name="hadir" class="rounded border px-3 py-2" required>
        <option value="1" @selected($attendance->hadir)>Hadir</option>
        <option value="0" @selected(!$attendance->hadir)>Tidak Hadir</option>
    </select>
    <input type="text" name="keterangan" value="{{ $attendance->keterangan }}" placeholder="Keterangan" class="rounded border px-3 py-2">
    <button class="rounded bg-slate-900 px-4 py-2 text-white">Update</button>
</form>
@endsection
