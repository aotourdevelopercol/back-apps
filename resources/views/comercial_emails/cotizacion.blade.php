<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización enviada</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #ffffff; margin: 0; padding: 2rem;">
    <table align="center" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 2rem;">
                    <tr>
                        <td style="text-align: center;">
                            <h2 style="color: #db4403;">Nueva cotización disponible</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 1rem 0;">
                            <p style="font-size: 16px; color: #333;">
                                Estimado cliente,
                            </p>
                            <p style="font-size: 16px; color: #333;">
                                Le informamos que se ha generado una nueva <strong style="color: #db4403;">cotización</strong> para su revisión.
                            </p>
                            <p style="font-size: 16px; color: #333;">
                                Adjunto a este mensaje encontrará un archivo PDF con los detalles de la cotización. Por favor, revíselo cuidadosamente. 
                                Si está de acuerdo con las condiciones, puede <strong style="color: #db4403;">aceptar</strong> o <strong style="color: #db4403;">rechazar</strong> la cotización utilizando el siguiente enlace.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 1.5rem 0;">
                            <a href="{{ $token }}" style="background-color: #db4403; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; font-size: 16px;">
                                Revisar y Aceptar Cotización
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 1rem;">
                            <p style="font-size: 14px; color: #555;">
                                Si tiene alguna duda o requiere ajustes en la cotización, no dude en contactarnos.
                            </p>
                            <p style="font-size: 14px; color: #555;">
                                Atentamente,<br>
                                <strong style="color: #db4403;">El equipo de Cotizaciones</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
