<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Viaje UP</title>

    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
            border-radius: 10px;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            overflow: scroll;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            color: #ffffff;
            padding: 20px;
            width: 100%;
            background-color: #db4403;
        }

        .message {
            font-size: 18px;
            line-height: 1.6;
            padding: 20px;
            text-align: center;
        }

        .button {
            display: inline-block;
            background: #db4403;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }

        .button:hover {
            background: #c33d03;
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
        logo img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <div class="header" style="text-align: center;">
                ¡Asignación de viaje! 🚗</div>
            <!-- LOGO SECTION -->
        </div>

        <div style="text-align: center; padding: 15px 20px;">
            <a href="">
                <img src="{{asset('asset/img/logo-bienvenida.png')}}" alt="Aotour Logo" width="200"
                    style="max-width: 100%;">
            </a>
        </div>

        <table width="100%">

            <!-- MAIN MESSAGE -->
            <tr>
                <td>
                    <p class="message">
                        Hola {{$nombre}}, UP te confirma que tu colaborador al volante asignado para tu traslado
                        es:
                    </p>
                    <p class="message">
                        <strong>Nombre:</strong> {{$conductor}}<br>
                        <strong>Placa:</strong> {{$placa}}<br>
                        <strong>Teléfono:</strong> 📲 {{$telefonoConductor}}
                    </p>
                </td>
            </tr>

            <!-- TRIP DETAILS -->
            <tr>
                <td>
                    <p class="message">
                        🔴 {{$puntoA}}<br>
                        🟢 {{$puntoB}}<br><br>
                        📅 <strong>Fecha:</strong> {{$fecha}} - {{$hora}}
                    </p>
                </td>
            </tr>

            <!-- FOOTER MESSAGE -->
            <tr>
                <td>
                    <p class="message">
                        Me muevo con UP.<br><br>
                        Si tienes alguna duda o necesitas soporte, comunícate al <strong>3147806060</strong> o
                        <strong>601 358 5555</strong>.
                    </p>
                    <p class="message">¡Descarga nuestra app y lleva tu experiencia a otro nivel!</p>
                </td>
            </tr>

            <br>
            <!-- FOOTER SECTION -->
            <tr>
                <td class="footer">
                    <p>&copy; {{ date('Y') }} Up. Todos los derechos reservados.</p>
                    <p><a href="https://www.upnetweb.com/">Visita nuestro sitio web</a></p>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>