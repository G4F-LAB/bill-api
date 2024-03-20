<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Mail\Checklist\Created as ChecklistCreated;
use Illuminate\Support\Facades\Mail;

class CheckChecklistExpired extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
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
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Check Checklist Expired',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'view.name',
        );
    }

    public function via($notifiable)
    {
        return ['mail'];
       
    }


    public function toMail($notifiable)
    {
        try {
            Mail::to($this->to)->send(new ChecklistCreated($this->checklist));
         
        } catch (\Throwable $th) {
            throw $th;
        }
       
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
