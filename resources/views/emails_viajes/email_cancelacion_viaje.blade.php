<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Eliminación de Programación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            color: #d9534f;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .content {
            margin: 20px 0;
        }

        .content p {
            margin: 10px 0;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .footer a {
            color: #0275d8;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Aotour</h1>
        </div>
        <div class="content">
            <p>Estimado(a) <strong>{{ $nombre }}</strong>,</p>
            <p>Lamentamos informarte que has sido eliminado(a) de la programación.</p>
            <p>¡Mantente atento(a) a futuras actualizaciones! 📅</p>
        </div>
        <div class="footer">
            <p>Si tienes alguna duda o necesitas soporte, no dudes en comunicarte con nosotros:</p>
            <ul>
                <li><strong>Teléfono 1:</strong> 314 780 6060</li>
                <li><strong>Teléfono 2:</strong> 601 358 5555</li>
            </ul>
        </div>
    </div>
</body>

</html>