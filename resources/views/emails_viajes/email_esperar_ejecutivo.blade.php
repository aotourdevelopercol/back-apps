<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Viaje 🚗</title>

    <style type="text/css">
        body {
            margin: 0;
            background-color: #cccccc;
            font-family: Sansation, Arial, sans-serif;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            text-align: center;
            padding: 20px;
            background: #E04403;
        }

        .logo img {
            max-width: 100%;
            height: auto;
        }

        .header {
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            color: #ffffff;
            margin: 20px 0;
            width: 100%;
        }

        .subtext {
            font-size: 18px;
            font-weight: 700;
            text-align: center;
            color: #E04403;
            margin: 10px 0;
        }

        .message {
            font-size: 16px;
            line-height: 22px;
            text-align: center;
            color: #171a1b;
            padding: 0 20px;
            margin: 10px 0;
        }

        .button-container {
            text-align: center;
            margin: 20px 0;
        }

        .button {
            background-color: #E04403;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 5px;
            border: none;
            font-weight: bold;
            font-size: 14px;
            display: inline-block;
        }

        .footer {
            font-size: 12px;
            font-style: italic;
            text-align: center;
            color: black;
            padding: 20px 0;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- LOGO SECTION -->
        <div class="logo">
            <!-- GREETING MESSAGE -->
            <div class="header" style="text-align: center;">¡El conductor ya está esperándote! 🚗</div>

        </div>

        <div style="text-align: center;">
            <img src="{{asset('asset/img/logo-bienvenida.png')}}" alt="Aotour Logo" width="200"></a>
        </div>
        <div class="subtext">Prepárate para partir y disfruta del viaje 🚙 🛣️</div>

        <!-- MAIN MESSAGE -->
        <div class="message">
            Tu destino te espera y cada kilómetro será una experiencia única. 🌟 Disfruta del recorrido y que comience
            la diversión 🎉 🚐
        </div>

        <!-- CALL TO ACTION BUTTON -->
        <div class="button-container">
            <a href="https://www.upnetweb.com/viaje?token={{ $token }}" class="button">Detalles de tu viaje</a>
        </div>

        <!-- FOOTER SECTION -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} UP. Todos los derechos reservados.</p>
            <p><a href="https://www.upnetweb.com/">Visita nuestro sitio web</a></p>
        </div>
    </div>
</body>

</html>