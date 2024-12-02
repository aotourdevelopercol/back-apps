<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viaje Finalizado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #db4403;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            letter-spacing: 1px;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .content p {
            font-size: 18px;
            line-height: 1.6;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            background: #db4403;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .cta-button:hover {
            background: #db4403;
        }
        .footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #666;
        }
        .footer a {
            color: #db4403;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Usted ha llegado a tu destino</h1>
        </div>
        <div class="content">
            <p>🚗Gracias por viajar con nosotros. ¡Esperamos verte pronto!😃</p>
            <a href="https://www.upnetweb.com/viaje?token={{ $token }}" class="cta-button">Detalles de tu viaje</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Aotour. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>