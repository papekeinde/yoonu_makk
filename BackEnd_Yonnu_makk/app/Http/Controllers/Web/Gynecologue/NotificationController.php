<?php

namespace App\Http\Controllers\Web\Gynecologue;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Auth::guard('gynecologue_web')->user()
            ->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('pro.notifications.index', compact('notifications'));
    }

    public function marquerLu(int $id): RedirectResponse
    {
        $notification = Auth::guard('gynecologue_web')->user()
            ->notifications()
            ->findOrFail($id);

        $notification->update(['est_lu' => true, 'lu_le' => now()]);

        return back()->with('success', 'Notification marquée comme lue.');
    }
}
