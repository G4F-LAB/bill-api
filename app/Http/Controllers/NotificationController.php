<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function registerNotification()
    {
        try {
            $notification = new Notification();
            $notification->desc_id = 2;
            $notification->notification_cat_id = 1;
            $notification->contract_id = 4;
            $notification->date = date("Y-m-d H:i:s");
            $notification->notification_viewed_id = 2;
            $notification->notification_type_id = 2;
            $notification->save();

            return response()->json([$notification, 'message' => 'Notificação adicionado com sucesso!'], 200);
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit;
            return response()->json(['error' => $e->getMessage()]);
        }

    }
}
