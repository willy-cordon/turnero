<DOCTYPE html/>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

    <h1><img class="app-logo" src="{{asset('img/logoargcovid.jpg')}}" title="ARG Vacuna COVID"/></h1>
    <p>Muchas gracias por tu participación en el Estudio de la Vacuna contra COVID-19</p>
    <p>Hemos confirmado tu turno para el día <strong>{{$data['appointment_date']}}</strong> a la hora <strong>{{$data['appointment_hour']}}</strong></p>
    @if($data['transportation'] == 'Propio medio')
        <p>Procure estar 15 minutos antes a la hora del turno.</p>
    @else
        <p>El día del turno pasaremos a buscarte por <strong>{{$data['address']}}</strong></p>
    @endif
    <h3>¿QUE TENGO QUE TRAER? </h3>
    <ul>
        <li>DNI</li>
    </ul>

    <h3>¿PUEDO IR ACOMPAÑADO?</h3>
    <p>Lamentablemente no está autorizada la concurrencia de acompañantes.</p>
    @if($data['transportation'] != 'Propio medio')
    <h3>¿COMO VUELVO?</h3>
    <p>Nosotros nos encargamos. Cuando finalice tu visita programaremos tu vuelta al mismo domicilio con Cabify.</p>
    @endif
    <h3>¿QUE TENGO QUE HACER ANTES DE LA VISITA?</h3>
    <p>Por favor, recordá gestionar tu permiso de circulación en el siguiente link: <a href="https://www.argentina.gob.ar/circular">www.argentina.gob.ar/circular</a></p>
    <h3>¿SI TENGO MAS PREGUNTAS?</h3>

    <p>Puedes comunicarte con {{$data['scheduler_name']}} - Tel.{{$data['scheduler_phone']}} y con mucho gusto te ayudaremos.</p>


    <p><strong>Por favor, tené en cuenta que el turno es un rango horario, el auto puede pasar a buscarte entre una hora y media (1:30 hs) o quince minutos antes del turno (este margen puede ampliarse dependiendo de la distancia o alguna contingencia que pueda presentarse). Por ese motivo, le solicitamos alistarte <span style="text-decoration: underline;">una hora y media</span> antes para no generar demoras.</strong></p>

    <p><strong><span style="text-decoration: underline;">Si pasados los quince minutos antes</span> del turno (solamente en esta situación, no antes), nadie se ha contactado contigo para confirmar el viaje, por favor envianos un mensaje de Whatsapp a CELSUR (NO LLAMAR) a los teléfonos
        {{ $data['mobile_phone_1']  }} / {{ $data['mobile_phone_2']  }}  para conocer el estado del pedido.</strong></p>

    <p><strong>Asimismo, te comentamos que debido a la complejidad de un estudio de tal magnitud, sumado a que se está efectuando en un contexto de Pandemia, el turno puede estar sujeto a <span style="text-decoration: underline;">modificaciones y/o cancelaciones de último momento</span>. En ese caso, nos comunicaremos contigo a la brevedad. Esperamos que sepas entender y disculpar.</strong></p>




    <p>No responda a este mensaje ya que el remitente es una casilla automática</p>

    <p>Un saludo,</p>
    <p><strong>El Equipo del Estudio de la Vacuna contra el COVID</strong></p>

</body>
</html>