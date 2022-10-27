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
                table-layout: auto;
            }
            .table th h6 {
                    font-size: 12px;
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
                font-size: 12px;
            }
            table thead th:nth-child(0),
            table thead th:nth-child(1){
            width: 10%;
            }

            table thead th:nth-child(2){
            width: 50%;
            }
            table thead th:nth-child(3){
            width: 15%;
            }
            table thead th:nth-child(3){
            width: 15%;
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
                    <th>Precio .A</th>
                    <th>Precio .P</th>
                    <th>Actualizado el</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{ $product->custom_code }}</td>
                    <td>{{ $product->name }}</td>
                    <td>G$ {{number_format($product->purchase_price, 0)}}</td>
                    <td>G$ {{number_format($product->sale_price, 0)}}</td>
                    <td>{{ $product->updated_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>