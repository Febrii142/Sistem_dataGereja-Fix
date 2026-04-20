<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\RegistrationStatusUpdatedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminApprovalController extends Controller
{
    public function pending(): View
    {
        $pendingUsers = User::query()
            ->pending()
            ->with(['jemaat', 'member'])
            ->latest()
            ->paginate(20);

        return view('admin.registrations.pending', compact('pendingUsers'));
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update(['status' => 'approved']);
        $user->notify(new RegistrationStatusUpdatedNotification('approved'));

        return back()->with('success', 'Pendaftaran jemaat berhasil di-approve.');
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update(['status' => 'rejected']);
        $user->notify(new RegistrationStatusUpdatedNotification('rejected', $data['reason'] ?? null));

        return back()->with('success', 'Pendaftaran jemaat ditolak.');
    }
}
