<?php

namespace App\Mail;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseShipped extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Purchase $purchase)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Purchase Shipped',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.purchase',
            with: [
                'user' => $this->purchase->user,
                'appName' => config('app.name'),
                'name' => $this->purchase->user->name,
                'orderId' => $this->purchase->id,
                'order' => $this->purchase,
                'amount' => $this->purchase->amount_crypto,
                'currency' => $this->purchase->cryptocurrency->symbol,
                'date' => $this->purchase->created_at->toDateTimeString(),
                'txid' => $this->purchase->uuid,
                'txUrl' => null,
                'link' => null,
                'supportUrl' => 'support@example.com',
                'items' => [
                    [
                        'name' => $this->purchase->cryptocurrency->name,
                        'qty' => $this->purchase->amount_crypto,
                        'price' => $this->purchase->amount_usd / $this->purchase->amount_crypto,
                    ]
                ],
            ],
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
