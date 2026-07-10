<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(NotificationResource::collection($notifications)->response()->getData(true));
    }

    public function marquerLu(Request $request, int $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        $notification->update([
            'est_lu' => true,
            'lu_le'  => now(),
        ]);

        return response()->json(['message' => 'Notification marquée comme lue.']);
    }
}
