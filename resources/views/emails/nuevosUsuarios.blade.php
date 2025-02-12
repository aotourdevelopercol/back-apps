<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Nuestro Servicio</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
    <table role="presentation" width="100%" style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <!-- Encabezado -->
        <tr>
            <td style="background: #ff4500; color: white; text-align: center; padding: 20px; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <h1 style="margin: 0;">🎉 ¡Bienvenido, {{ $nombre }}! 🚀</h1>
            </td>
        </tr>

        <!-- Contenido -->
        <tr>
            <td style="padding: 20px; text-align: center;">
                <p style="font-size: 18px; color: #333;">Estamos emocionados de tenerte con nosotros. Aquí tienes tus credenciales de acceso:</p>

                <div style="background: #ffeed9; padding: 15px; border-radius: 8px; display: inline-block; text-align: left; font-size: 16px;">
                    <strong>Usuario:</strong> {{ $user }} <br>
                    <strong>Contraseña:</strong> {{ $password }}
                </div>

            </td>
        </tr>

        <!-- Pie de página -->
        <tr>
            <td style="background: #333; color: white; text-align: center; padding: 15px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                <p style="margin: 0;">&copy; {{ date('Y') }} Up Translink. Todos los derechos reservados.</p>
                <p><a href="https://memuevoconup.com" style="color: #ff4500; text-decoration: none;">Visita nuestro sitio web</a></p>
            </td>
        </tr>
    </table>
</body>
</html>