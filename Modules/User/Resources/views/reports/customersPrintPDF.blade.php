<html>
  <head>
      <title>Reporte Clientes</title>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <style>
          table {
              border-collapse: collapse;
              page-break-inside:auto;
              margin: auto;
          }
          tr    { 
              page-break-inside:avoid; 
              page-break-after:auto;
              margin: 5px 0px 5px 0px;
          }
          thead { 
              display:table-header-group;
              background-color: black;
              color: #ffffff; 
          }
          th, td {
              border: black 1px solid;
              padding-left: 5px;
              padding-right: 5px;
              text-align: center;
          }
          @page {
              size: a4 landscape;
              margin: 1cm;
          }
      </style>
  </head>
<body>
    <h2>Reporte de Cliente - {{ date("d/m/Y") }}</h2>
    <table class="table table-bordered mb-5">
        <thead>
            <tr class="table-danger">
                <th style="width: 30%" scope="col">Cod Agente</th>
                <th style="width: 30%" scope="col">Razón Social</th>
                <th style="width: 30%" scope="col">Doc/RUC</th>
                <th style="width: 15%" scope="col">Teléfono</th>
                <th style="width: 15%" scope="col">Email</th>
                <th style="width: 15%" scope="col">¿Es Vigia?</th>
                <th style="width: 15%" scope="col">Localidad</th>
                <th style="width: 40%" scope="col">Próxima Visita</th>
                <th style="width: 40%" scope="col">Hora Prox Visita</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->idReference }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->doc_id }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->email }}</td>
                <td>                      
                    @if ($customer->is_vigia == "on")
                        Sí
                    @else
                        No
                    @endif
                <td>{{ $customer->estate }}</td>
                <td>{{ $customer->next_visit_date }}</td>
                <td>{{ $customer->next_visit_hour }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>