<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viaje Finalizado</title>
    <style>
        .button {
            background-color: rgba(224, 68, 3, 1);
            color: white;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 5px;
            border: 1px solid rgba(224, 68, 3, 1);
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
            margin-top: 20px;
        }
       
        .container {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-block;
            text-align: left;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <h1>El viaje ha finalizado</h1>
            <p>Gracias por viajar con nosotros. ¡Esperamos verte pronto!</p>
            <tr>
                <td style="text-align: center;">
                    <a href="https://www.upnetweb.com/viaje?token={{ $token }}" class="button">Detalles de tu viaje</a>
                </td>
            </tr>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Aotour. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>