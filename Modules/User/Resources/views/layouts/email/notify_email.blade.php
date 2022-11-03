<!DOCTYPE html>
<html>
<head>
    <title>Email Notificación</title>
</head>
<body>
    <p>Hola,<br>
    ¡Buenas noticias! el agente <b>{{ $emailInfo->seller_name }}</b> acaba de crear un(a) <b>{{ $type }}</b> #{{ $emailInfo->visit_number }} en el sistema web P&P. <br>
    Aquí están los detalles: <br>
    </p>

    <p><b>Información - {{ $type }}</b></p>
    Nombre: {{ $emailInfo->customer_name }} <br>
    Número telefónico: {{ $emailInfo->phone }} <br>
    Creado el: {{ date('d/m/Y - H:i', strtotime($emailInfo->visit_date)) }} <br>
    Fecha Próximo Paso:  @if ($emailInfo->next_visit_date > 9) {{ date('d/m/Y', strtotime($emailInfo->next_visit_date)) }} @else {{ $emailInfo->next_visit_date }} @endif <br>
    Hora Próximo Paso: {{ $emailInfo->next_visit_hour }}<br>
    Acción: {{ $emailInfo->action }} <br>

    <p><b>Detalles</b></p>

    Estado: {{ $emailInfo->status }} <br>
    Resultados de la Visita/Llamada: {{ $emailInfo->result_of_the_visit }} <br>
    Objetivos: {{ $emailInfo->objective }} <br>

    <p><b>Presupuesto?</b></p>

    <p>{{ $emailInfo->type }}</p>

    <img src="{{ asset('/public/adminLTE/images/logo/logo-pyp.png') }}" style="display:block;height:auto;border:0;max-width:100%;width: 190px;padding-top:20px;" width="190" alt="logo P&P" title="logo P&P">
</body>
</html>