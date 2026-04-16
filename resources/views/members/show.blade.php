@extends('layouts.app')
@section('content')
<h2 class="mb-4 text-2xl font-bold">Detail Jemaat</h2>
<div class="rounded bg-white p-4 shadow">
    <p><strong>Nama:</strong> {{ $member->nama }}</p>
    <p><strong>Alamat:</strong> {{ $member->alamat }}</p>
    <p><strong>Kontak:</strong> {{ $member->kontak }}</p>
    <p><strong>Status:</strong> {{ $member->status }}</p>
    <p><strong>Tanggal Lahir:</strong> {{ $member->tanggal_lahir }}</p>
    <p><strong>Kategori:</strong>
        @forelse($member->categories as $category)
            <span class="mr-1 inline-block rounded bg-slate-100 px-2 py-1 text-xs">{{ $category->name }}</span>
        @empty
            -
        @endforelse
    </p>
</div>
@endsection
