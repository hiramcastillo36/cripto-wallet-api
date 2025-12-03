<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Confirmación de compra - {{ $appName ?? config('app.name', 'Aplicación') }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background:#f6f6f6; margin:0; padding:20px; color:#333; }
        .email-wrap { max-width:600px; margin:0 auto; background:#fff; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
        .header { background:#0b69ff; color:#fff; padding:18px 24px; }
        .header h1 { margin:0; font-size:18px; }
        .content { padding:20px 24px; }
        .button { display:inline-block; background:#0b69ff; color:#fff; padding:10px 16px; text-decoration:none; border-radius:4px; }
        .meta { background:#f2f4f7; padding:12px; border-radius:4px; margin:12px 0; font-size:14px; }
        table { width:100%; border-collapse:collapse; margin-top:8px; }
        th, td { text-align:left; padding:8px 6px; border-bottom:1px solid #eee; }
        .footer { font-size:13px; color:#777; padding:16px 24px; background:#fafafa; text-align:center; }
    </style>
</head>
<body>
    <div class="email-wrap">
        <div class="header">
            <h1>{{ $appName ?? config('app.name', 'Mi Cripto Wallet') }}</h1>
        </div>

        <div class="content">
            <p>Hola {{ $user->name ?? $name ?? 'Cliente' }},</p>

            <p>Gracias por tu compra. Esta es la confirmación de tu transacción:</p>

            <div class="meta">
                <strong>Número de orden:</strong> {{ $order->id ?? $orderId ?? '—' }}<br>
                <strong>Monto:</strong> {{ number_format($amount ?? ($order->amount ?? 0), 8) }} {{ $currency ?? ($order->currency ?? 'BTC') }}<br>
                <strong>Fecha:</strong> {{ $date ?? now()->toDateTimeString() }}<br>
                <strong>ID de transacción:</strong> {{ $txid ?? '—' }}
            </div>

            @if(!empty($items ?? ($order->items ?? [])))
                <p>Detalles:</p>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th style="text-align:right">Cantidad</th>
                            <th style="text-align:right">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items ?? ($order->items ?? []) as $item)
                            <tr>
                                <td>{{ $item['name'] ?? $item->name ?? 'Item' }}</td>
                                <td style="text-align:right">{{ $item['qty'] ?? $item->quantity ?? 1 }}</td>
                                <td style="text-align:right">
                                    {{ number_format($item['price'] ?? $item->price ?? 0, 8) }} {{ $currency ?? ($order->currency ?? 'BTC') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if(!empty($link ?? $orderUrl ?? $txUrl))
                <p style="margin-top:14px;">
                    <a href="{{ $link ?? $orderUrl ?? $txUrl }}" class="button" target="_blank" rel="noopener">Ver transacción / Detalles</a>
                </p>
            @endif

            <p style="margin-top:14px">Si tienes alguna pregunta o no reconoces esta transacción, contáctanos:</p>
            <p>
                <a href="{{ $supportUrl ?? 'mailto:support@example.com' }}">{{ $supportUrl ?? 'support@example.com' }}</a>
            </p>

            <p>Saludos,<br>{{ $appName ?? config('app.name', 'Mi Cripto Wallet') }}</p>
        </div>

        <div class="footer">
            Este correo es una confirmación automática. Por favor, no respondas a este mensaje.
        </div>
    </div>
</body>
</html></tr></tbody>
