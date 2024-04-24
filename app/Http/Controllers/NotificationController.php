<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function registerNotification(Object $data_notification)
    {
        try {
            $notification = new Notification();
            $notification->desc_id = $data_notification->desc_id;
            $notification->notification_cat_id = $data_notification->notification_cat_id;
            $notification->contract_id = $data_notification->contract_uuid;
            $notification->date = date("Y-m-d H:i:s");
            $notification->notification_type_id = $data_notification->notification_type_id;
            $notification->save();

            return response()->json([$notification, 'message' => 'Notificação adicionado com sucesso!'], 200);
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit;
            return response()->json(['error' => $e->getMessage()]);
        }

    }
}
