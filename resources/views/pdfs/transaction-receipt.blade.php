<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de Transacción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .transaction-details {
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
        }
        .amount-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .amount-display {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            text-align: center;
        }
        .currency {
            font-size: 16px;
            color: #666;
        }
        .status {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status.completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status.failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #999;
            font-size: 12px;
        }
        .transaction-id {
            background-color: #f0f0f0;
            padding: 10px;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Recibo de Transacción</h1>
            <p>{{ config('app.name') }}</p>
        </div>

        <div class="transaction-details">
            <div class="detail-row">
                <span class="detail-label">ID de Transacción:</span>
                <span class="detail-value">#{{ $transaction->id }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Moneda:</span>
                <span class="detail-value">{{ $cryptocurrency->symbol }} - {{ $cryptocurrency->name }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Tipo:</span>
                <span class="detail-value">{{ ucfirst($transaction->type) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Estado:</span>
                <span class="detail-value">
                    <span class="status {{ $transaction->status }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </span>
            </div>

            <div class="amount-section">
                <div class="detail-row">
                    <span class="detail-label">Cantidad:</span>
                    <span class="detail-value">
                        <div class="amount-display">{{ number_format($transaction->amount, 8) }}</div>
                        <div class="currency">{{ $cryptocurrency->symbol }}</div>
                    </span>
                </div>
            </div>

            @if($transaction->usd_value_at_time)
            <div class="detail-row">
                <span class="detail-label">Valor USD:</span>
                <span class="detail-value">${{ number_format($transaction->usd_value_at_time, 2) }}</span>
            </div>
            @endif

            @if($transaction->fee_amount)
            <div class="detail-row">
                <span class="detail-label">Comisión:</span>
                <span class="detail-value">{{ number_format($transaction->fee_amount, 8) }} {{ $cryptocurrency->symbol }}</span>
            </div>
            @endif

            <div class="detail-row">
                <span class="detail-label">De:</span>
                <span class="detail-value">
                    @if($sender)
                        {{ $sender->name }} ({{ $sender->email }})
                    @else
                        Sistema
                    @endif
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Para:</span>
                <span class="detail-value">
                    @if($receiver)
                        {{ $receiver->name }} ({{ $receiver->email }})
                    @elseif($transaction->reference_id)
                        {{ $transaction->reference_id }}
                    @else
                        Dirección Externa
                    @endif
                </span>
            </div>

            @if($transaction->reference_id && $transaction->type === 'transfer')
            <div class="detail-row">
                <span class="detail-label">Dirección de Destino:</span>
                <span class="detail-value">
                    <div class="transaction-id">{{ $transaction->reference_id }}</div>
                </span>
            </div>
            @endif

            <div class="detail-row">
                <span class="detail-label">Fecha de Creación:</span>
                <span class="detail-value">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</span>
            </div>

            @if($transaction->completed_at)
            <div class="detail-row">
                <span class="detail-label">Fecha de Completación:</span>
                <span class="detail-value">{{ $transaction->completed_at->format('d/m/Y H:i:s') }}</span>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>Este es un recibo automático de la transacción de criptomonedas.</p>
            <p>Generado el: {{ $generatedAt->format('d/m/Y H:i:s') }}</p>
            <p>UUID: {{ $transaction->uuid }}</p>
        </div>
    </div>
</body>
</html>
