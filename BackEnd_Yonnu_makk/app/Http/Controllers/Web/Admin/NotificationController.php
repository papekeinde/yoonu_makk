<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $patientes = User::where('role', 'patient')
            ->orderBy('prenom')
            ->get(['id', 'prenom', 'nom', 'email']);

        $historique = Notification::whereNotNull('user_id')
            ->latest()
            ->take(20)
            ->get();

        return view('admin.notifications.index', compact('patientes', 'historique'));
    }

    public function envoyer(StoreNotificationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->boolean('a_tous')) {
            $userIds = User::where('role', 'patient')->pluck('id');
        } else {
            $userIds = collect($request->user_ids ?? []);
        }

        if ($userIds->isEmpty()) {
            return back()->with('error', 'Aucune destinataire sélectionnée.')->withInput();
        }

        foreach ($userIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type'    => $data['type'],
                'titre'   => $data['titre'],
                'corps'   => $data['corps'],
            ]);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', $userIds->count() . ' notification(s) envoyée(s).');
    }
}
