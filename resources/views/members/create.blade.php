@extends('layouts.app')
@section('content')
<h2 class="mb-4 text-2xl font-bold">Tambah Jemaat</h2>
<form method="post" action="{{ route('members.store') }}" class="rounded bg-white p-4 shadow">
    @include('members._form')
</form>
@endsection
