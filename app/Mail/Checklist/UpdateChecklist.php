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

class UpdateChecklist extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($checklist)
    {
        $this->checklist = $checklist;
        $this->month = Carbon::parse($checklist['date_checklist'])->translatedFormat('F');
        $this->id = $checklist['id'];
        $this->contract_name = $checklist['contract']['name'];
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
            subject: "Checklist de $this->month alterado",
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
            view: 'welcome',
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
