<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conformidad confirmada</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #166534; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #166534; }
        .footer { margin-top: 20px; font-size: 12px; color: #666; text-align: center; }
        .button { background-color: #166534; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Conformidad confirmada</h1>
            <p>AltoParque</p>
        </div>

        <div class="content">
            <div class="field">
                <span class="label">👤 Cliente:</span>
                {{ $workOrder->serviceOrder->customer->name }}
            </div>
            <div class="field">
                <span class="label">📍 Propiedad:</span>
                {{ $workOrder->serviceOrder->property?->display_label ?? '—' }}
            </div>
            <div class="field">
                <span class="label">🔧 Orden de trabajo:</span>
                {{ $workOrder->work_order_number }}
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/admin/work-orders/'.$workOrder->id.'/edit') }}" class="button">Ver orden de trabajo</a>
            </div>
        </div>

        <div class="footer">
            <p>Este es un mensaje automático, por favor no responder.</p>
            <p>&copy; {{ date('Y') }} AltoParque</p>
        </div>
    </div>
</body>
</html>
