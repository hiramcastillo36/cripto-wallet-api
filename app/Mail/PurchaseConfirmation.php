<?php

namespace App\Mail;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Purchase $purchase)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de tu compra de ' . ($this->purchase->cryptocurrency->name ?? 'criptomoneda'),
        );
    }

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
}
