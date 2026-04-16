@extends('layouts.app')
@section('content')
<h2 class="mb-4 text-2xl font-bold">Edit Kategori Jemaat</h2>
<form method="post" action="{{ route('categories.update', $category) }}" class="rounded bg-white p-4 shadow">
    @method('PUT')
    @include('categories._form')
</form>
@endsection
