
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Realizado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f8fa;
            padding: 30px;
        }
        .card {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            border: 1px solid #e1e4e8;
        }
        .header {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .details {
            font-size: 16px;
            color: #444;
            line-height: 1.6;
        }
        .details strong {
            color: #111;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">Pago Realizado</div>

        <div class="details">
            <p>Cordial saludo,</p>

            <p>
                Nos permitimos informarle que el pago correspondiente a la 
                <strong>Cuenta de Cobro No. {{ $numeroCuenta }}</strong> 
                ha sido realizado exitosamente.
            </p>

            <p>A continuación, se detallan los datos del pago:</p>

            <ul>
                <li><strong>Número de Cuenta de Cobro:</strong> {{ $numeroCuenta }}</li>
                <li><strong>Valor Pagado:</strong> ${{ number_format($monto, 0, ',', '.') }}</li>
                <li><strong>Fecha de Pago:</strong> {{ \Carbon\Carbon::parse($fechaPago)->format('d/m/Y') }}</li>
                <li><strong>Medio de Pago:</strong> {{ $medioPago }}</li>
            </ul>

            <p>
                Agradecemos su gestión y quedamos atentos a cualquier inquietud adicional.
            </p>
        </div>

        <div class="footer">
            Atentamente,<br>
            <strong>{{ $empresaNombre }}</strong><br>
            {{ $empresaCorreo }}
        </div>
    </div>
</body>
</html>
