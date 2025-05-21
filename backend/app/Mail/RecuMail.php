<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecuMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Payment and billing details
     */
    public $amount;
    public $payment_method;
    public $service_fee;
    public $total;
    public $address;
    public $postal_code;
    public $country;
    public $name;

    /**
     * Create a new message instance.
     * 
     * @param string $name
     * @param string $amount
     * @param string $payment_method
     * @param string $service_fee
     * @param string $total
     * @param string $address
     * @param string $postal_code
     * @param string $country
     */
    public function __construct($name, $amount, $payment_method, $service_fee, $total, $address, $postal_code, $country)
    {
        $this->name = $name;
        $this->amount = $amount;
        $this->payment_method = $payment_method;
        $this->service_fee = $service_fee;
        $this->total = $total;
        $this->address = $address;
        $this->postal_code = $postal_code;
        $this->country = $country;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de Paiement - Reçu',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.RecuMail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
