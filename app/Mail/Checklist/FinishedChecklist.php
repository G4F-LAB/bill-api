<?php

namespace App\Mail\Checklist;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class FinishedChecklist extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($checklist)
    {
        // echo "<pre>";
        $this->checklist = $checklist;
        $this->month = Carbon::parse($checklist['date_checklist'])->translatedFormat('F');
        $this->contract_name = $checklist['contract']['name'];
        $this->id = $checklist['id'];
        $this->url = env('APP_URL') ? env('APP_URL') : 'https://book.hml.g4f.com.br';
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: "Checklist de $this->month foi finalizado",
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
            view: 'finished',
            with: [
                'month' => $this->month,
                'id' => $this->id,
                'contract' => $this->contract_name,
                'url' => "{$this->url}/contracts/{$this->checklist['contract_id']}/checklist/{$this->checklist['id']}/items"
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
