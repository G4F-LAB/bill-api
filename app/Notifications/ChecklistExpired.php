<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Mail\Checklist\UpdateChecklist as UpdateChecklist;
use App\Mail\Checklist\FinishedChecklist as FinishedChecklist;
use Illuminate\Support\Facades\Mail;

class ChecklistExpired extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data, $collaborators)
    {
        $this->to = $collaborators;
        $this->checklist = $data;
        $this->toMail($this);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];

    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        try {
            if($this->checklist['status_id'] == 5){
                Mail::to($this->to)->send(new FinishedChecklist($this->checklist));
            }else{
                Mail::to($this->to)->send(new UpdateChecklist($this->checklist));

            }

        } catch (\Throwable $th) {
            throw $th;
        }

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
