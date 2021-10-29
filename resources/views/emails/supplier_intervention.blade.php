<DOCTYPE html/>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

    <h1><img class="app-logo" src="{{asset('img/logoargcovid.jpg')}}" title="ARG Vacuna COVID"/></h1>

    <h2>Datos del voluntario</h2>
    <p><strong>Nombre y Apellido: </strong> {{$data['supplier_name']}}</p>
    <p><strong>DNI: </strong> {{$data['supplier_dni']}}</p>
    <p><strong>Teléfono celular: </strong> {{$data['supplier_mobile_phone']}}</p>
    <p><strong>Teléfono fijo: </strong> {{$data['supplier_phone']}}</p>
    <p><strong>Contacto: </strong> {{$data['supplier_contact']}}</p>
    <p><strong>Teléfono de contacto: </strong> {{$data['supplier_contact_phone']}}</p>
    <p><strong>Dirección: </strong> {{$data['supplier_address']}}</p>
    <p><strong>email:</strong> {{$data['supplier_email']}}</p>

    <p>-----</p>
    <p>No responda a este mensaje ya que el remitente es una casilla automática</p>

</body>
</html>