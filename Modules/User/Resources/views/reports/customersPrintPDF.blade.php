<html>
    <head>
        <title>Informe Clientes</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style>
            table {
                border-collapse: collapse;
                page-break-inside:auto;
                margin: auto;
                width: 100%;
                font-family:  'Times New Roman', Times, serif;
            }
            .table th h6 {
                    font-size: 14px;
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
                height: 40px !important;
            }
            th, td {
                border: rgb(59, 59, 59) 1px solid;
                padding: 5px;
                text-align: center;
            }
            @page {
                size: a4 landscape;
                margin: 1cm;
            }
        </style>
    </head>
    <body>
        <h2>Informe de Clientes - {{ date("d/m/Y") }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Razón Social</th>
                    <th>Doc/RUC</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>¿Es Vigia?</th>
                    <th>Ciudad</th>
                    <th>Localidad</th>
                    <th>Próxima Visita</th>
                    <th>Hora Prox Visita</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr>
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
                    </td>
                    <td>{{ $customer->city }}</td>
                    <td>{{ $customer->estate }}</td>
                    <td>{{ $customer->next_visit_date }}</td>
                    <td>{{ $customer->next_visit_hour }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>