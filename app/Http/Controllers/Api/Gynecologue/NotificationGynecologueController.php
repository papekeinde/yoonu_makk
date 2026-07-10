<?php

namespace App\Http\Controllers\Api\Gynecologue;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationGynecologueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user('gynecologue')
            ->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(NotificationResource::collection($notifications)->response()->getData(true));
    }

    public function marquerLu(Request $request, int $id): JsonResponse
    {
        $notification = $request->user('gynecologue')->notifications()->findOrFail($id);

        $notification->update([
            'est_lu' => true,
            'lu_le'  => now(),
        ]);

        return response()->json(['message' => 'Notification marquée comme lue.']);
    }
}
