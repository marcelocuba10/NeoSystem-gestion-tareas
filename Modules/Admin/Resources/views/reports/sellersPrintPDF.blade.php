<html>
    <head>
        <title>Informe Agentes</title>
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
        <h2>Informe de Agentes - {{ date("d/m/Y") }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Razón Social</th>
                    <th>Teléfono</th>
                    <th>Contacto</th>
                    <th>Dirección</th>
                    <th>Ciudad</th>
                    <th>Localidad</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sellers as $seller)
                <tr>
                    <td>{{ $seller->idReference }}</td>
                    <td>{{ $seller->name }}</td>
                    <td>{{ $seller->phone_1 }}</td>
                    <td>{{ $seller->seller_contact_1 }}</td>
                    <td>{{ $seller->address }}</td>
                    <td>{{ $seller->city }}</td>
                    <td>{{ $seller->estate }}</td>
                    <td>                      
                        @if ($seller->status == 1)
                            Activo
                        @else
                            Inactivo
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>