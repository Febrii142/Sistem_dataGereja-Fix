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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
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
            'baptism_status' => ['required', 'in:sudah_dibaptis,belum_dibaptis'],
            'baptism_date' => ['nullable', 'date', 'before_or_equal:today'],
            'baptism_location' => ['nullable', 'string', 'max:255'],
            'catechism_batch' => ['nullable', 'required_if:baptism_status,belum_dibaptis', 'string', 'max:100'],
            'parent_guardian_name' => ['nullable', 'required_if:baptism_status,belum_dibaptis', 'string', 'max:255'],
        ]);

        $memberRoleId = Role::query()->where('name', 'Jemaat Gereja')->value('id')
            ?? Role::query()->where('name', 'Member')->value('id');

        if (! $memberRoleId) {
            throw ValidationException::withMessages([
                'email' => 'Role jemaat belum tersedia. Silakan hubungi admin sistem.',
            ]);
        }

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
                'status_baptis' => $validated['baptism_status'] === 'sudah_dibaptis' ? 'sudah' : 'belum',
                'kelas_katekisasi' => $validated['catechism_batch'] ?? null,
                'tanggal_baptis' => $validated['baptism_status'] === 'sudah_dibaptis'
                    ? ($validated['baptism_date'] ?? null)
                    : null,
            ]);

            Member::query()->create([
                'user_id' => $user->id,
                'nama' => $validated['name'],
                'alamat' => $validated['alamat'],
                'kontak' => $validated['no_telepon'],
                'status' => 'aktif',
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'baptism_status' => $validated['baptism_status'],
                'baptism_date' => $validated['baptism_status'] === 'sudah_dibaptis'
                    ? ($validated['baptism_date'] ?? null)
                    : null,
                'baptism_location' => $validated['baptism_status'] === 'sudah_dibaptis'
                    ? ($validated['baptism_location'] ?? null)
                    : null,
                'catechism_batch' => $validated['baptism_status'] === 'belum_dibaptis'
                    ? ($validated['catechism_batch'] ?? null)
                    : null,
                'parent_guardian_name' => $validated['baptism_status'] === 'belum_dibaptis'
                    ? ($validated['parent_guardian_name'] ?? null)
                    : null,
            ]);

            return $user;
        });

        try {
            Notification::send($user, new RegistrationCredentialsNotification($generatedPassword));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send registration credentials email.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
        }

        $staffUsers = User::query()
            ->approved()
            ->where(function ($query) {
                $query
                    ->whereIn('role', ['admin', 'staff', 'koordinator'])
                    ->orWhereHas('role', fn ($roleQuery) => $roleQuery->whereIn('name', ['Admin', 'Staff']));
            })
            ->get();

        try {
            Notification::send($staffUsers, new NewRegistrationSubmittedNotification($user));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send staff notification for new registration.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('login')
            ->with('success', 'Pendaftaran berhasil dikirim. Password otomatis telah dikirim ke email Anda.');
    }
}
