<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Nuestro Servicio</title>
    <style>
        .cta-button {
            display: inline-flex;
            align-items: center;
            background: #db4502;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .cta-button img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .download-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .download-buttons a {
            display: inline-flex;
            align-items: center;
            background: #000;
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 5px;
            margin: 5px;
        }

        .download-buttons img {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }

        .ios-button {
            background: #007aff;
        }

        .android-button {
            background: #34a853;
        }
    </style>
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

                <!-- Botón de detalles del viaje con icono -->
                <div>
                    <a href="" class="cta-button">
                        <img src="https://cdn-icons-png.flaticon.com/512/732/732228.png" alt="icono-viaje">
                        Detalles de tu viaje
                    </a>
                </div>

                <!-- Sección de descarga de la app -->
                <div class="download-buttons">
                    <h3>📲 Descarga nuestra app</h3>
                    <a href="https://play.google.com/store/apps/details?id=com.aotour.cliente&pcampaignid=web_share" class="android-button">
                        <img src="https://cdn-icons-png.flaticon.com/512/300/300218.png" alt="Google Play">
                        Google Play
                    </a>
                    <a href="https://apps.apple.com/co/app/up/id1496087652" class="ios-button">
                        <img src="https://cdn-icons-png.flaticon.com/512/732/732217.png" alt="App Store">
                        App Store
                    </a>
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
