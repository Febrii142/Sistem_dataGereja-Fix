<?php

namespace App\Http\Controllers;

use App\Models\Jemaat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('profile.show', [
            'jemaat' => $this->getOrCreateCurrentJemaat(),
        ]);
    }

    public function edit(): View
    {
        return view('profile.edit', [
            'jemaat' => $this->getOrCreateCurrentJemaat(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'min:3', 'max:255'],
            'no_identitas' => ['nullable', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'alamat' => ['required', 'string', 'min:5'],
            'nomor_telepon' => ['required', 'string', 'max:30'],
            'status_pernikahan' => ['nullable', 'string', 'max:100'],
        ]);

        $jemaat = $this->getOrCreateCurrentJemaat();
        $jemaat->update([
            'nama_lengkap' => $validated['nama'],
            'no_identitas' => $validated['no_identitas'] ?: null,
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'] ?: null,
            'alamat' => $validated['alamat'],
            'no_telepon' => $validated['nomor_telepon'],
            'status_perkawinan' => $validated['status_pernikahan'] ?: null,
        ]);

        return redirect()->route('jemaat.profile')->with('success', 'Profil jemaat berhasil diperbarui.');
    }

    private function getOrCreateCurrentJemaat(): Jemaat
    {
        /** @var \App\Models\User $user */
        $user = auth()->user()->loadMissing('jemaat');

        if ($user->jemaat) {
            return $user->jemaat->loadMissing('user');
        }

        return $user->jemaat()->create([
            'nama_lengkap' => $user->name,
            'tempat_lahir' => '-',
            'tanggal_lahir' => now()->subYears(18)->toDateString(),
            'no_telepon' => '-',
            'email' => $user->email,
            'status_perkawinan' => 'Belum Menikah',
        ])->load('user');
    }
}
