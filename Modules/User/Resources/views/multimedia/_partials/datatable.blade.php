  <table class="table top-selling-table table-hover">
    <thead>
      <tr>
        <th><h6>#</h6></th>
        <th><h6>Archivo</h6></th>
        <th><h6>Descripción</h6></th>
        <th><h6>Categoría</h6></th>
        <th><h6>Tamaño</h6></th>
        <th><h6>Fecha Creación</h6></th>
        <th><h6>Acciones</h6></th>
      </tr>
    </thead>
    <tbody>
      @if (count($multimedias) > 0 )
        @foreach ($multimedias as $multimedia)
          <tr>
            <td class="text-sm"><h6 class="text-sm">{{ ++$i }}</h6></td>
            <td>
              <div class="product">
                <div class="image">
                  @if ($multimedia->filename)
                    <img src="{{ asset('/public/images/products/'.$multimedia->filename) }}" alt="{{ Str::limit($multimedia->filename, 15) }}">
                  @else
                    <img src="{{ asset('/public/adminLTE/images/products/no-image.jpg') }}" alt="{{ Str::limit($multimedia->filename, 15) }}">
                  @endif
                </div>
                <h5 class="text-bold text-dark"><a href="{{ url('/user/multimedia/show/'.$multimedia->id ) }}">{{ $multimedia->filename }}</a></h5>
              </div>
            </td>
            <td class="min-width"><p>{{ $multimedia->description }}</p></td>
            <td class="min-width">
              <span class="status-btn 
              @if($multimedia->type == 'Imágenes') secondary-btn
              @elseIf($multimedia->type == 'Documentos') close-btn
              @elseIf($multimedia->type == 'Manuales') warning-btn
              @elseIf($multimedia->type == 'Lista de Precios') success-btn
              @endif">
                {{ $multimedia->type }}
              </span>
            </td>
            <td class="min-width"><p>{{ $multimedia->size }}</p></td>
            <td class="min-width"><p>{{ $multimedia->created_at }}</p></td>
            <td class="text-right">
              <div class="btn-group">
                <div class="action">
                  <a href="{{ url('/user/multimedia/show/'.$multimedia->id) }}" data-toggle="tooltip" data-placement="bottom" title="Ver">
                    <button class="text-active"><i class="lni lni-eye"></i></button>
                  </a>
                </div>
                <div class="action">
                  <a href="#">
                    <button class="text-success"><i class="lni lni-download"></i></button>
                  </a>
                </div>
              </div>
            </td>
          </tr>
        @endforeach
      @else
        <tr>
          <td class="text-sm"></td>
          <td class="min-width"></td>
          <td class="min-width"></td>
          <td class="min-width"></td>
          <td class="min-width"> Sin resultados encontrados</td>
          <td class="min-width"></td>
          <td class="min-width"></td>
          <td class="min-width"></td>
        </tr>
      @endif

    </tbody>
  </table>