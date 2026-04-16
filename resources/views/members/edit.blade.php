@extends('layouts.app')
@section('content')
<h2 class="mb-4 text-2xl font-bold">Edit Jemaat</h2>
<form method="post" action="{{ route('members.update', $member) }}" class="rounded bg-white p-4 shadow">
    @method('PUT')
    @include('members._form')
</form>
@endsection
