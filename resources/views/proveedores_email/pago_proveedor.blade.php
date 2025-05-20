<!-- resources/views/emails/pago_proveedor.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Aprobado - Up Translink</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <div style="width: 100%; padding: 20px 0;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); font-family: Arial, sans-serif; padding: 30px;">
            
            <h2 style="color: #2c3e50; margin-top: 0;">¡Tu pago ha sido aprobado!</h2>
            
            <p style="font-size: 16px; color: #555555; line-height: 1.6;">
                Estimado proveedor,
            </p>

            <p style="font-size: 16px; color: #555555; line-height: 1.6;">
                <strong>Up Translink</strong> se complace en informarte que tu pago ha sido aprobado exitosamente.
                En breve, el dinero será transferido a tu cuenta bancaria registrada.
            </p>

            <p style="font-size: 16px; color: #555555; line-height: 1.6;">
                Te agradecemos sinceramente por tu compromiso y confianza en nuestra plataforma.
                Estamos siempre a tu disposición para seguir construyendo juntos un servicio eficiente y confiable.
            </p>

            <div style="margin-top: 30px;">
                <a href="{{ url('/') }}" style="display: inline-block; background-color: #3490dc; color: #ffffff; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-size: 16px;">
                    Ir a la plataforma
                </a>
            </div>

            <p style="font-size: 14px; color: #999999; margin-top: 40px;">
                Este es un mensaje automático, por favor no respondas a este correo. Si tienes dudas o inquietudes, contáctanos a través del canal oficial de soporte.
            </p>
        </div>
    </div>
</body>
</html>
