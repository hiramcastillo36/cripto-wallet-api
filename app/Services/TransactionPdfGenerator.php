<?php

namespace App\Services;

use App\Models\Transaction;
use PDF;
use Illuminate\Support\Facades\Storage;

class TransactionPdfGenerator
{
    public function generate(Transaction $transaction): string
    {
        $data = [
            'transaction' => $transaction,
            'sender' => $transaction->sender,
            'receiver' => $transaction->receiver,
            'cryptocurrency' => $transaction->cryptocurrency,
            'generatedAt' => now(),
        ];

        $pdf = PDF::loadView('pdfs.transaction-receipt', $data);

        $filename = 'transaction_' . $transaction->id . '_' . now()->timestamp . '.pdf';
        $path = storage_path('app/transactions/' . $filename);

        if (!file_exists(storage_path('app/transactions'))) {
            mkdir(storage_path('app/transactions'), 0755, true);
        }

        $pdf->save($path);

        return $path;
    }
}
