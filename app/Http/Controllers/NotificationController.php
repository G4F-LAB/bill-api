<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Collaborator;
use App\Models\Notification_viewer;
use App\Models\Notification;
use App\Models\Contract;

class NotificationController extends Controller
{


    public function __construct(Collaborator $collaborator)
    {
        $this->auth_user = $collaborator->getAuthUser();
    }



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
            var_dump($e->getMessage());
            exit;
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function notifications()
    {



        try {

            $id_user = $this->auth_user->id;
            $notifications = Notification::leftJoin('notification_name', 'notification_name.id', '=', 'notifications.notification_type_id')
            ->leftJoin('notification_categories', 'notification_categories.id', '=', 'notifications.notification_cat_id')
            // ->leftJoin('contracts', 'contracts.id', '=', 'notifications.contract_id')
            ->select(
                'notifications.id',
                'notification_categories.category_name',
                'notification_name.desc',
                // 'contracts.name',
                'notifications.created_at'
                )
                ->limit(10)
                ->orderBy('notifications.id', 'DESC')
                ->get();
                
                return Contract::get();
























            $id_notifications_viewer = Notification_viewer::where('collaborator_id', $id_user)->pluck('notification_id')->toArray();

            foreach ($notifications as $indice => $value) {
                if (in_array($value->id, $id_notifications_viewer)) {
                    $notifications[$indice]['viewer'] = true;
                } else {
                    $notifications[$indice]['viewer'] = false;
                }
            }

            foreach ($notifications as $index => $value) {
                $createdDate = Carbon::parse($value->created_at);
                if ($createdDate->isToday()) {
                    $notifications[$index]['date'] = 'Hoje';
                } elseif ($createdDate->isYesterday()) {
                    $notifications[$index]['date'] = 'Ontem';
                } else {
                    $notifications[$index]['date'] = $createdDate->diffForHumans();
                }
            }
            return response()->json(['status' => 'ok', 'data' => $notifications], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function notificationsViewer(Request $request)
    {

        try {
            $ids_notifications = $request->id_notifications;
            $id_user = $this->auth_user->id;

            foreach ($ids_notifications as $value) {
                $notification_viewer = new Notification_viewer();
                $notification_viewer->user_id = $id_user;
                $notification_viewer->notification_id = $value;
                $notification_viewer->save();
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
