<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UP - El conductor está en camino</title>
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
            background: #db4502;
            color: #ffffff;
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
            background: #db4502;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .cta-button:hover {
            background: #0056b3;
        }
        .footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #666;
        }
        .footer a {
            color: #007BFF;
            text-decoration: none;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>UP 🚗✨</h1>
        </div>
        <div class="content">
            <p>¡El conductor ha confirmado tu viaje! 🌟</p>
            <p>Prepárate para una aventura increíble. Tu destino te espera y cada kilómetro será una experiencia única. </p>
            <p>🎉 ¡Disfruta del recorrido y que comience la diversión! 🎉</p>
            <a href="https://www.upnetweb.com/viaje?token=" class="cta-button">Detalles de tu viaje</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} UP. Todos los derechos reservados.</p>
            <p><a href="https://www.upnetweb.com/">Visita nuestro sitio web</a></p>
        </div>
    </div>
</body>
</html>