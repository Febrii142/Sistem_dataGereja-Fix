<?php

namespace App\Http\Controllers;

use App\Models\Jemaat;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use App\Notifications\NewRegistrationSubmittedNotification;
use App\Notifications\RegistrationCredentialsNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('registration.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_telepon' => ['required', 'string', 'max:30'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'alamat' => ['required', 'string', 'min:5'],
            'kota' => ['required', 'string', 'max:255'],
            'kode_pos' => ['required', 'string', 'max:10'],
            'status_baptis' => ['required', 'in:sudah,belum'],
            'kelas_katekisasi' => ['nullable', 'string', 'max:100'],
        ]);

        $memberRoleId = Role::query()->where('name', 'Jemaat Gereja')->value('id')
            ?? Role::query()->where('name', 'Member')->value('id');
        $generatedPassword = Str::password(12);

        $user = DB::transaction(function () use ($validated, $memberRoleId, $generatedPassword) {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $generatedPassword,
                'role' => 'jemaat',
                'role_id' => $memberRoleId,
                'status' => 'pending',
            ]);

            Jemaat::query()->create([
                'user_id' => $user->id,
                'nama_lengkap' => $validated['name'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'alamat' => $validated['alamat'],
                'kota' => $validated['kota'],
                'kode_pos' => $validated['kode_pos'],
                'no_telepon' => $validated['no_telepon'],
                'email' => $validated['email'],
                'status_baptis' => $validated['status_baptis'],
                'kelas_katekisasi' => $validated['kelas_katekisasi'] ?? null,
            ]);

            Member::query()->create([
                'user_id' => $user->id,
                'nama' => $validated['name'],
                'alamat' => $validated['alamat'],
                'kontak' => $validated['no_telepon'],
                'status' => 'aktif',
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
            ]);

            return $user;
        });

        $user->notify(new RegistrationCredentialsNotification($generatedPassword));

        $staffUsers = User::query()
            ->approved()
            ->where(function ($query) {
                $query
                    ->whereIn('role', ['admin', 'staff', 'koordinator'])
                    ->orWhereHas('role', fn ($roleQuery) => $roleQuery->whereIn('name', ['Admin', 'Staff']));
            })
            ->get();

        Notification::send($staffUsers, new NewRegistrationSubmittedNotification($user));

        return redirect()
            ->route('login')
            ->with('success', 'Pendaftaran berhasil dikirim. Password otomatis telah dikirim ke email Anda.');
    }
}
