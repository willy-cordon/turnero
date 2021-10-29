<DOCTYPE html/>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

    <h1><img class="app-logo" src="{{asset('img/logoargcovid.jpg')}}" title="ARG Vacuna COVID"/></h1>

    <p>Hemos cancelado el turno de <strong>{{ $data['supplier_wms_name'] }},{{ $data['supplier_wms_id']  }}</strong> para el día <strong>{{$data['appointment_date']}}</strong> a la hora <strong>{{$data['appointment_hour']}}</strong></p>

    <p>No responda a este mensaje ya que el remitente es una casilla automática</p>

    <p>Un saludo,</p>
    <p><strong>El Equipo del Estudio de la Vacuna contra el COVID</strong></p>

</body>
</html>