<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Eliminación de Programación</title>
    <style type="text/css">
        body {
            margin: 0;
            background-color: #cccccc;
            border-radius: 8px;
        }

        .wrapper {
            max-width: 600px;
          
            background: #ffffff;
            
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color:#db4403 ;
            font-family: Sansation;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            color: #ffffff;
            width: 100%;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            padding: 20px 0;
        }

        .content {
            font-family: Sansation;
            font-size: 16px;
            line-height: 22px;
            text-align: center;
            color: #171a1b;
            padding: 0 20px;
        }

        .message {
            font-family: Sansation;
            font-size: 16px;
            line-height: 22px;
            text-align: center;
            color: #171a1b;
            padding: 0 20px;
        }

        .footer {
            font-family: Sansation;
            font-size: 12px;
            font-style: italic;
            text-align: center;
            color: black;
            padding-top: 20px;
        }

        .cta {
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
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- HEADER -->
        <div class="header">
            ⚠️ Aotour
        </div>

        <!-- MAIN CONTENT -->
        <div class="content">
            <p>Estimado(a) <strong>{{ $nombre }}</strong>,</p>
            <p>Lamentamos informarte que has sido eliminado(a) de la programación.</p>
            <p>¡Mantente atento(a) a futuras actualizaciones! 📅</p>
        </div>

        <!-- ADDITIONAL MESSAGE -->
        <div class="message">
            Con AOTOUR, viaja tranquilo, viaja seguro.<br><br>
            Si tienes alguna duda o necesitas soporte, comunícate al <strong>3147806060</strong> o <strong>601 358 5555</strong>.
            <br><br>
            ¡Descarga nuestra app y lleva tu experiencia a otro nivel!
        </div>

        <!-- FOOTER -->
        <div class="footer">
            Aotour Tech
        </div>
    </div>
</body>

</html>