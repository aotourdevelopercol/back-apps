<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Ruta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #db4403;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #db4403;
            color: #fff;
            text-align: center;
            padding: 20px 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .content h2 {
            color: #db4403;
            margin-top: 0;
        }
        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 12px;
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
            <h1>Confirmación de Ruta</h1>
        </div>
        <div class="content">
            <h2>Hola {{$nombre}},</h2>
            <p>Tu compañía ha solicitado un servicio de ruta para ti. En breve, <strong>AOTOUR</strong> te compartirá los detalles del colaborador al volante asignado para tu traslado.</p>
            <p>Con <strong>AOTOUR</strong>, viaja tranquilo, viaja seguro.</p>
        </div>
        <div class="footer">
            <p>&copy; 2024 AOTOUR. Todos los derechos reservados.</p>
            <p><a href="https://www.upnetweb.com/">Visita nuestro sitio web</a></p>
        </div>
    </div>
</body>
</html>