<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_users')->only(['index']);
        $this->middleware('permission:create_users')->only(['create', 'store']);
        $this->middleware('permission:edit_users')->only(['edit', 'update']);
        $this->middleware('permission:delete_users')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->string('search')->toString());

        $usersQuery = User::query()
            ->with('role')
            ->adminAndStaff()
            ->addSelect([
                'last_activity_at' => DB::table('sessions')
                    ->selectRaw('MAX(last_activity)')
                    ->whereColumn('user_id', 'users.id'),
            ]);

        if ($search !== '') {
            $usersQuery->where(function (Builder $query) use ($search): void {
                $query
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        return view('users.index', [
            'users' => $usersQuery->latest()->paginate(12)->withQueryString(),
            'totalUsers' => User::query()->adminAndStaff()->count(),
            'adminRolesCount' => User::query()
                ->adminAndStaff()
                ->where(function (Builder $query): void {
                    $query
                        ->whereRaw("lower(replace(role, ' ', '')) in (?, ?, ?)", ['admin', 'superadmin', 'super_admin'])
                        ->orWhereHas('role', function (Builder $roleQuery): void {
                            $roleQuery->whereIn('name', ['Admin', 'Super Admin']);
                        });
                })
                ->count(),
            'roles' => $this->assignableRoles()->get(),
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => $this->assignableRoles()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role_id' => ['required', Rule::exists('roles', 'id')->whereIn('name', ['Admin', 'Super Admin', 'Staff'])],
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
            'roles' => $this->assignableRoles()->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'role_id' => ['required', Rule::exists('roles', 'id')->whereIn('name', ['Admin', 'Super Admin', 'Staff'])],
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
            'Super Admin' => 'admin',
            'Pendeta' => 'pendeta',
            'Staff' => 'koordinator',
            'Jemaat Gereja' => 'jemaat',
            default => 'user',
        };
    }

    private function assignableRoles(): Builder
    {
        return Role::query()
            ->whereIn('name', ['Admin', 'Super Admin', 'Staff'])
            ->orderByRaw("
                case name
                    when 'Super Admin' then 0
                    when 'Admin' then 1
                    when 'Staff' then 2
                    else 99
                end
            ");
    }
}
