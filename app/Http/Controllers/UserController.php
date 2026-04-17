<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_users')->only(['index']);
        $this->middleware('permission:create_users')->only(['create', 'store']);
        $this->middleware('permission:edit_users')->only(['edit', 'update']);
        $this->middleware('permission:delete_users')->only(['destroy']);
    }

    public function index()
    {
        return view('users.index', [
            'users' => User::query()->with('role')->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (! auth()->user()?->hasPermission('assign_roles')) {
            abort(403);
        }

        User::create([
            ...$data,
            'role' => $this->legacyRoleValue((int) $data['role_id']),
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user->load('role'),
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        if (! auth()->user()?->hasPermission('assign_roles')) {
            abort(403);
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['role'] = $this->legacyRoleValue((int) $data['role_id']);

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Akun pengguna diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Tidak bisa menghapus akun sendiri.']);
        }

        $user->delete();

        return back()->with('success', 'Akun pengguna dihapus.');
    }

    private function legacyRoleValue(int $roleId): string
    {
        return match (Role::query()->findOrFail($roleId)->name) {
            'Admin' => 'admin',
            'Pendeta' => 'pendeta',
            'Staff' => 'koordinator',
            'Jemaat Gereja' => 'jemaat',
            default => 'user',
        };
    }
}
