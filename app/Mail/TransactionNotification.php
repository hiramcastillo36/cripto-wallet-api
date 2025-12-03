<?php

namespace App\Mail;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class TransactionNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Transaction $transaction,
        public User $receiver,
        public string $pdfPath
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Transacción de Criptomonedas Recibida - ' . $this->transaction->cryptocurrency->symbol,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.transaction-notification',
            with: [
                'receiver' => $this->receiver,
                'transaction' => $this->transaction,
                'sender' => $this->transaction->sender,
                'cryptocurrency' => $this->transaction->cryptocurrency,
                'amount' => $this->transaction->amount,
                'appName' => config('app.name'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('transaccion_' . $this->transaction->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
