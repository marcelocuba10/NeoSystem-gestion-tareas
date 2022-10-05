<html>
  <head>
      <title>Lista de Precios</title>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <style>
          table {
              border-collapse: collapse;
              page-break-inside:auto;
              margin: auto;
              width: 100%;
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
              text-align: center;
          }
          th, td {
              border: black 1px solid;
              padding-left: 5px;
              padding-right: 5px;
              /**min-width: 150px;**/
              text-align: center;
          }
          @page {
              size: a4 portrait;
              margin: 1cm;
          }
      </style>
  </head>
<body>
    <h2>Productos - Lista de Precios - {{ date("d/m/Y") }}</h2>
    <table class="table table-bordered mb-5">
        <thead>
            <tr class="table-danger">
                <th style="width: 20%" scope="col">Código</th>
                <th style="width: 20%" scope="col">Nombre</th>
                <th style="width: 15%" scope="col">Precio</th>
                <th style="width: 20%" scope="col">Stock</th>
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