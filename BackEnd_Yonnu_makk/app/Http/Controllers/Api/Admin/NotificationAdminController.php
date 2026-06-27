<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class NotificationAdminController extends Controller
{
    public function envoyer(StoreNotificationRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->a_tous) {
            $userIds = User::where('role', 'patient')->pluck('id');
        } else {
            $userIds = collect($request->user_ids ?? []);
        }

        $count = 0;
        foreach ($userIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type'    => $data['type'],
                'titre'   => $data['titre'],
                'corps'   => $data['corps'],
            ]);
            $count++;
        }

        return response()->json([
            'message' => "{$count} notification(s) envoyée(s).",
        ]);
    }
}
