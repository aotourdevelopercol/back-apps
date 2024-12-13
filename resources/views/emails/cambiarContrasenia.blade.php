<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecimiento de Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            color: #db4403;
        }
        .email-body {
            margin: 20px 0;
            color: #333333;
        }
        .email-button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #db4403;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .email-footer {
            margin-top: 20px;
            font-size: 12px;
            color: #888888;
            text-align: center;
        }

        .button{
            width:  100%;
            display: flex;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1 class="email-header">Restablecimiento de Contraseña</h1>
        <div class="email-body">
            <p>Hola,</p>
            <p>Recibimos una solicitud para restablecer tu contraseña. Puedes hacerlo haciendo clic en el botón de abajo:</p>
            <div class="button">
            <a href="{{ url('https://www.upnetweb.com/auth/restore-password=' . $token) }}" class="email-button">Restablecer Contraseña</a>
            </div>
            
            <p>Si no solicitaste este cambio, puedes ignorar este correo. Tu contraseña no cambiará hasta que accedas al enlace y crees una nueva.</p>
        </div>
        <div class="email-footer">
            <p>Este enlace es válido por un tiempo limitado.</p>
            <p>Si tienes problemas, contáctanos.</p>
        </div>
    </div>
</body>
</html>