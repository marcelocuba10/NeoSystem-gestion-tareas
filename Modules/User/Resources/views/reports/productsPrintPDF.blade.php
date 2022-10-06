<html>
    <head>
        <title>Informe Productos</title>
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
        <h2>Informe de Productos - {{ date("d/m/Y") }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Inventario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{ $product->code }}</td>
                    <td>{{ $product->name }}</td>
                    <td>G$ {{number_format($product->sale_price, 0)}}</td>
                    <td>{{ $product->inventory }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>