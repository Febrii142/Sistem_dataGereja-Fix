<?php

namespace App\Http\Controllers;

use App\Models\Jemaat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JemaatController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_jemaat_dashboard')->only(['dashboard', 'showProfile']);
        $this->middleware('permission:edit_own_jemaat')->only(['editProfile', 'updateProfile']);
        $this->middleware('permission:view_users')->only(['verificationQueue']);
    }

    public function dashboard()
    {
        $jemaat = $this->getOrCreateCurrentJemaat()->load('baptisan');

        return view('jemaat.dashboard', [
            'jemaat' => $jemaat,
            'anggotaKeluarga' => $jemaat->getAnggotaKeluarga(),
            'isKepalaKeluarga' => $jemaat->isKepalaKeluarga(),
        ]);
    }

    public function showProfile()
    {
        return view('jemaat.profile.show', [
            'jemaat' => $this->getOrCreateCurrentJemaat()->load('baptisan'),
        ]);
    }

    public function editProfile()
    {
        return view('jemaat.profile.edit', [
            'jemaat' => $this->getOrCreateCurrentJemaat(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'min:3', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'alamat' => ['required', 'string', 'min:5'],
            'kota' => ['required', 'string', 'max:255'],
            'kode_pos' => ['required', 'numeric'],
            'no_telepon' => ['required', 'numeric'],
            'email' => ['required', 'email'],
            'status_baptis' => ['required', 'in:sudah,belum'],
            'tanggal_baptis' => ['nullable', 'date', 'required_if:status_baptis,sudah'],
        ]);

        if ($data['status_baptis'] === 'belum') {
            $data['tanggal_baptis'] = null;
        }

        $jemaat = $this->getOrCreateCurrentJemaat();
        $jemaat->update($data);

        return redirect()->route('jemaat.profile.show')->with('success', 'Profil jemaat berhasil diperbarui.');
    }

    public function verificationQueue(): View
    {
        $pendingUsers = User::query()
            ->pending()
            ->with(['jemaat', 'member'])
            ->latest()
            ->paginate(20);

        return view('members.verification', compact('pendingUsers'));
    }

    private function getOrCreateCurrentJemaat(): Jemaat
    {
        $user = auth()->user();

        return Jemaat::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap' => $user->name,
                'tempat_lahir' => '-',
                'tanggal_lahir' => now()->subYears(18)->toDateString(),
                'no_telepon' => '0',
                'email' => $user->email,
            ]
        );
    }
}
