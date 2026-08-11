<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos para la transferencia</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #166534; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .account { background-color: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .footer { margin-top: 20px; font-size: 12px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏦 Datos para la transferencia</h1>
            <p>AltoParque</p>
        </div>

        <div class="content">
            <p>Hola {{ $workOrder->serviceOrder->customer->name }}! Te pasamos los datos para la transferencia:</p>

            @foreach ($bankAccounts as $bankAccount)
                <div class="account">
                    <p><strong>{{ $bankAccount->name }}</strong></p>
                    <p>Banco: {{ $bankAccount->bank_name }}</p>
                    <p>Titular: {{ $bankAccount->account_holder }}</p>
                    <p>CBU: {{ $bankAccount->cbu }}</p>
                    @if ($bankAccount->cbu_alias)
                        <p>Alias: {{ $bankAccount->cbu_alias }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="footer">
            <p>Este es un mensaje automático, por favor no responder.</p>
            <p>&copy; {{ date('Y') }} AltoParque</p>
        </div>
    </div>
</body>
</html>
