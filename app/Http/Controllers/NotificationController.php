<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function registerNotification(Request $request)
    {
        try {
            $notification = new Notification();
            $notification->notification_type = $request->notification_type;
            $notification->operation_id = $request->operation_id;
            $notification->notification_cat_id = $request->notification_cat_id;
            $notification->save();
            return response()->json([$notification, 'message' => 'Notificação adicionado com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

    }
}
