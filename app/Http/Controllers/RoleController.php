<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin');
        $this->middleware('permission:assign_roles');
    }

    public function index()
    {
        return view('roles.index', [
            'roles' => Role::query()->with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get(),
        ]);
    }
}
