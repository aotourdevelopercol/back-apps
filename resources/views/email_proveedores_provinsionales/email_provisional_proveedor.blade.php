<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Proveedor Provisional</title>

    <style type="text/css">
        body {
            margin: 0;
            background-color: #cccccc;
        }
        table {
            border-spacing: 0;
        }
        td {
            padding: 0;
        }
        img {
            border: 0;
        }

        .header {
            font-family: Sansation;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            color: #E04403;
        }

        .subtext {
            font-family: Sansation;
            font-size: 18px;
            font-weight: 700;
            text-align: center;
            color: #E04403;
            margin: 10px;
        }

        .message {
            font-family: Sansation;
            font-size: 16px;
            line-height: 22px;
            text-align: left;
            color: #171a1b;
            padding: 0 20px;
        }

        motivo{
            font-family: Sansation;
            font-size: 16px;
            line-height: 22px;
            text-align: center;
            color: #171a1b;
            padding: 0 20px;
        }

        .button {
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

        .footer {
            font-family: Sansation;
            font-size: 12px;
            font-style: italic;
            text-align: center;
            color: black;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <!-- LOGO SECTION -->
            <tr>
                <td style="text-align: center; padding: 15px 20px; color: #ffffff">
                    <a href="#"><img src="{{asset('asset/img/logo.png')}}" alt="Logo" width="200" style="max-width: 100%;"></a>
                </td>
            </tr>

            <!-- HEADER MESSAGE -->
            <tr>
                <td>
                    <p class="header">🚗 Proveedor Provisional Asignado</p>
                </td>
            </tr>

            <!-- MESSAGE BODY -->
            <tr>
                <td>
                    <p class="message">
                        Estimado/a, <br><br>
                        Se ha asignado un proveedor provisional a las siguientes rutas:
                        <ul>
                           
                            @foreach ($ruta as $ruta1)
                            <li>{{$ruta1}}</li>
                            @endforeach
                            
                         
                           
                        </ul>
                        <br>
                        
                        
                     
                    </p>

                    <p class="motivo">
                        Motivo: {{$motivo}}<br><br>
                    </p>
                </td>
            </tr>

            <!-- FOOTER SECTION -->
            <tr>
                <td>
                    <p class="footer">Equipo de Aotour</p>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>