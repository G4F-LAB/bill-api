<?php

namespace App\Mail\Checklist;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Log;

use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class Created extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($checklist)
    {
        $this->id = $checklist->id;
        $this->month = Carbon::parse($checklist->date_checklist)->translatedFormat('F');
        //
    //    $data =  $this->envelope();
   

    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: "Checklist $this->month já disponível",
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
            view: 'emails.checklist.created',
            with: [
                'month' => $this->month,
                'id' => $this->id,
                'url' => 'https://book.hml.g4f.com.br/checklist/create/539/' .$this->id
            ],
        );
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
