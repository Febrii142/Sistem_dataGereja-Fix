@extends('layouts.app')
@section('content')
<div class="grid gap-4 lg:grid-cols-2">
    <div class="rounded bg-white p-4 shadow">
        <h2 class="mb-3 text-xl font-bold">Roles</h2>
        <div class="space-y-3">
            @foreach($roles as $role)
                <div class="rounded border border-slate-200 p-3">
                    <p class="font-semibold text-slate-800">{{ $role->name }}</p>
                    <p class="text-xs text-slate-500">{{ $role->description }}</p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($role->permissions as $permission)
                            <span class="rounded bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ $permission->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="rounded bg-white p-4 shadow">
        <h2 class="mb-3 text-xl font-bold">Permissions</h2>
        <div class="grid gap-2">
            @foreach($permissions as $permission)
                <div class="rounded border border-slate-200 px-3 py-2">
                    <p class="text-sm font-semibold text-slate-800">{{ $permission->name }}</p>
                    <p class="text-xs text-slate-500">{{ $permission->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
