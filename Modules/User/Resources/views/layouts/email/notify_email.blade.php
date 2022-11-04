<!DOCTYPE html>
<html>
    <head>
        <title>Email Notificación</title>
    </head>
    <body>
        <p>Hola,<br>
        El agente <b>{{ $bodyEmail->seller_name }}</b> acaba de <b>{{ $head }}</b> en el sistema web P&P. <br>
        Aquí están los detalles: <br>
        </p>

        <p><b>Información</b></p>
        Nombre: {{ $bodyEmail->customer_name }} <br>
        Número telefónico: {{ $bodyEmail->phone }} <br>

        @if ($type == 'Visita Cliente')

        Creado el: {{ date('d/m/Y - H:i', strtotime($bodyEmail->visit_date)) }} <br>
        Fecha Próximo Paso:  @if ($bodyEmail->next_visit_date > 9) {{ date('d/m/Y', strtotime($bodyEmail->next_visit_date)) }} @else {{ $bodyEmail->next_visit_date }} @endif <br>
        Hora Próximo Paso: {{ $bodyEmail->next_visit_hour }}<br>
        Acción: {{ $bodyEmail->action }} <br>   
        Resultados de la Visita/Llamada: {{ $bodyEmail->result_of_the_visit }} <br>
        Objetivos: {{ $bodyEmail->objective }} <br> 

        @elseif ($type == 'Venta')

        Creado el: {{ date('d/m/Y - H:i', strtotime($bodyEmail->sale_date)) }} <br> 
        Monto Total: G$ {{number_format($bodyEmail->total, 0)}} <br> 

        @elseif ($type == 'Agenda')

        Creado el: {{ date('d/m/Y - H:i', strtotime($bodyEmail->created_at)) }} <br> 
        Fecha de Agenda: {{ date('d/m/Y', strtotime($bodyEmail->date)) }} <br>
        Hora de Agenda: {{ $bodyEmail->hour }}<br>
        Acción: {{ $bodyEmail->action }} <br>   

        @endif

        Estado: 
        @if ($bodyEmail->status == 'Pendiente')
            <b style="color: #fff;background: #fb7d33;padding: 1px 7px;font-weight: 500;">{{ $bodyEmail->status }}</b>
        @elseif($bodyEmail->status == 'Procesado')
            <b style="color: #fff;background: #25a55a;padding: 1px 7px;font-weight: 500;">{{ $bodyEmail->status }}</b>
        @elseif($bodyEmail->status == 'Cancelado')     
            <b style="color: #262d3f;background: #c2c2c2;padding: 1px 7px;font-weight: 500;">{{ $bodyEmail->status }}</b>
        @elseif($bodyEmail->status == 'No Procesado')     
            <b style="color: #fff;background: #d50100c7;padding: 1px 7px;font-weight: 500;">{{ $bodyEmail->status }}</b>
        @endif
        <br>

        <p><b>Documentos Adjuntos</b></p>

        @if ($linkOrderPDF)
            <a href="{{ $linkOrderPDF }}">Visualizar Presupuesto</a><br>
        @else
            <p>--</p>
        @endif

        <hr style="margin-top: 15px;">
        <img src="{{ asset('/public/adminLTE/images/logo/logo-pyp.png') }}" style="display:block;height:auto;border:0;max-width:100%;width: 190px;padding-top:20px;margin-top:20px" width="190" alt="logo P&P" title="logo P&P">
    </body>
</html>