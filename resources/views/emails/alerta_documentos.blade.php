<!DOCTYPE html>
<html>

<head>
    <title>Alertas de Documentos</title>
</head>

<body>
    <h1>Documentos Vencidos o Próximos a Vencer</h1>

    @foreach ($vehiculos as $vehiculo)
        <h2>Vehículo Placa: {{ $vehiculo['vehiculo_id'] }}</h2>

        @if (!empty($vehiculo['vencidos']))
            <h3>Documentos Vencidos:</h3>
            <ul>
                @foreach ($vehiculo['vencidos'] as $vencido)
                    <li>{{ $vencido['documento'] }}: {{ $vencido['dias_vencidos'] }} días vencidos</li>
                @endforeach
            </ul>
        @endif
    @endforeach

    @foreach ($vehiculos as $vehiculo)
        @if (!empty($vehiculo['proximos_a_vencer']))
        <h2>Vehículo Placa: {{ $vehiculo['vehiculo_id'] }}</h2>
            <h3>Documentos Próximos a Vencer:</h3>
            <ul>
                @foreach ($vehiculo['proximos_a_vencer'] as $proximo)
                    <li>{{ $proximo['documento'] }}: {{ $proximo['dias_restantes'] }} días restantes</li>
                @endforeach
            </ul>
        @endif
    @endforeach
</body>

</html>