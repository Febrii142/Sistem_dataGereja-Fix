@extends('layouts.app')
@section('content')
<h2 class="mb-4 text-2xl font-bold">Tambah Kategori Jemaat</h2>
<form method="post" action="{{ route('categories.store') }}" class="rounded bg-white p-4 shadow">
    @include('categories._form')
</form>
@endsection
