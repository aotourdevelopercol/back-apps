<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern HTML Email Template</title>

    <style type="text/css">
    	body {
    		margin: 0;
    		background-color: #DB4403;
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

        .center-img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-top: 15px; /* margen superior */
            margin-bottom: 15px; /* margen inferior */
        }

    	.tops {
			height: 33px;
			top: 53px;
			gap: 0px;
			border-radius: 6px 6px 6px 6px;
			opacity: 0px;
			background-color: #DB4403;
		}

		.respu {
			font-family: Sansation;
			font-size: 24px;
			font-weight: 700;
			line-height: 31.48px;
			text-align: center;
			color: #ffffff;

		}

		.footer-logo {
			width: 196.41px;
			height: 56.33px;
			top: 657px;
			left: 208px;
			gap: 0px;
			opacity: 0px;
			align: center;
		}

    	/*old*/
    	.preguntas {
    		margin-left: 10px; 
    		font-family: Sansation; 
    		font-size: 18px; 
    		font-weight: 700; 
    		line-height: 12.24px; 
    		text-align: right; 
    		color: #ffffff;
    	}
    	.solicita {
    		font-family: Sansation; 
    		font-size: 18px; 
    		font-weight: 700; 
    		line-height: 12.24px; 
    		text-align: right; 
    		color: #ffffff;
    	}

    	.lineas {
			
			left: calc(50% - 627px/2 + 10.5px);
			color: #ffffff;
			font-family: 'Sansation';
			font-style: italic;
			font-weight: 700;
			font-size: 11px;
			line-height: 102%;
			/* or 11px */
			text-align: center;	
		}
    	.names {
			font-family: Sansation;
			font-size: 20px;
			font-weight: 700;
			line-height: 10.56px;
			text-align: center;
			color: #ffffff;

		}

		.asunto {
			font-family: Sansation;
			font-size: 18px;
			font-weight: 400;
			line-height: 28.96px;
			text-align: center;
			color: #ffffff;
		}

		.elegir {
			font-family: Sansation;
			font-size: 15px;
			font-weight: 400;
			line-height: 11.22px;
			text-align: center;
			color: #ffffff;
		}

    	.wrapper {
    		width: 100%;
    		table-layout: fixed;
    		background-color: #DB4403;
    		padding-bottom: 60px;
    	}
    	.main {
    		background-color: #DB4403;
    		margin: 0 auto;
    		width: 100%;
    		max-width: 600px;
    		border-spacing: 0;
    		font-family: Sansation;
    		color: #ffffff;
    	}

    	.two-columns {
    		text-align: center;
    		font-size: 0;

    	}
    	.two-columns .column {
    		width: 100%;
    		max-width: 300px;
    		display: inline-block;
    		vertical-align: top;
    		text-align: center;
    	}
    	.three-columns {
    		text-align: center;
    		font-size: 0;
    		padding: 5px 0 25px;
    	}
    	.three-columns .column {
    		width: 100%;
    		max-width: 300px;
    		display: inline-block;
    		vertical-align: top;
    		text-align: center;
    	}
    	.three-columns .padding {
    		padding: 1px;
    	}
    	.three-columns .content {
    		font-size: 15px;
    		line-height: 20px;
    		padding: 0 5px;
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
			margin-left: 40px;
		}

		.buttonr {
			background-color: rgba(255, 255, 255, 1);
			color: #ffffff;
			text-decoration: none;
			padding: 8px 20px;
			border-radius: 5px;
			border: 1px solid black;
			font-weight: bold;
			font-size: 12px;
		}

    </style>


</head>

<body>
	<center class="wrapper">
	
		<table class="main" width="100%">
		
			<!-- TOP BORDER -->
			<tr>
				<td>
					<table width="100%">
						<tr>
                        <td style="text-align: center;">
								<img src="{{asset('asset/img/logo-fondo.png')}}"  alt="" width="250" class="center-img">
                                
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- LOGO SECTION -->
				
			<!-- BANNER IMAGE -->

			<!-- THREE COLUMN SECTION -->
			<tr>
				<td>
					<table width="100%">
						<tr>
							<td>
								<p class="names">Cuenta de cobro cerrada para el mes de: {{$fecha}}</p>
                             
								<p class="asunto">Reciban un cordial saludo de parte del equipo de <b>UP translink SAS.</b> <br><br>Gracias por confiar en nuestro trabajo, nos encargaremos de que vivas una experiencia de transporte a otro nivel.</p>
							</td>
						</tr>
						
						<br>
						
						<tr>
                        <td style="text-align: center;">
								<img src="{{asset('asset/img/imagen_fondo_rojo.png')}}"  alt="" width="200" class="center-img">
							</td>
						</tr>

					</table>
				</td>
			</tr>
			<!-- TITTLE, TEXT & BUTTON -->

			<!-- FOOTER SECTION -->
			<tr>
				<td>
					<table width="100%">
						<tr>
							<td style="text-align: center; padding: 15px 20px; color: #ffffff">
								<p class="lineas"><b style="color: #ffffff">LÍNEAS DE ATENCIÓN:</b> Bogotá: (601) 358 5555 - Nacional: 314 780 6060</p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
	 </table>

	</center>

</body>
</html>