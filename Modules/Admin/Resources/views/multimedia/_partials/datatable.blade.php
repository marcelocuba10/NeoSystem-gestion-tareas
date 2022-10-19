  <table class="table top-selling-table table-hover">
    <thead>
      <tr>
        <th><h6>Nombre de Archivo</h6></th>
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
            <td>
              <div class="product">
                <div class="image">
                  @if ($multimedia->type == "Imágenes")
                    <img src="{{ asset('/public/files/'.$multimedia->filename) }}" alt="{{ Str::limit($multimedia->filename, 15) }}">
                  @else
                    <img height="40" width="30" src="{{ asset('/public/images/image-docs-small.png') }}" alt="{{ Str::limit($multimedia->filename, 15) }}">
                  @endif
                </div>
                <h5 class="text-bold text-dark"><a href="{{ url('/admin/multimedia/show/'.$multimedia->id ) }}">{{ $multimedia->filename }}</a></h5>
              </div>
            </td>
            <td class="min-width">
              <span class="status-btn 
              @if($multimedia->type == 'Imágenes') success-btn
              @elseIf($multimedia->type == 'Documentos') orange-btn
              @elseIf($multimedia->type == 'Manuales') active-btn
              @elseIf($multimedia->type == 'Lista de Precios') purple-btn
              @endif">
                {{ $multimedia->type }}
              </span>
            </td>
            <td class="min-width"><p>{{ $multimedia->size }}</p></td>
            <td class="min-width"><p>{{ $multimedia->created_at }}</p></td>
            <td class="text-right">
              <div class="btn-group">
                <div class="action">
                  <a href="{{ url('/admin/multimedia/show/'.$multimedia->id) }}" data-toggle="tooltip" data-placement="bottom" title="Ver">
                    <button class="text-active"><i class="lni lni-eye"></i></button>
                  </a>
                </div>
                <div class="action">
                  <a href="{{ asset('/public/files/'.$multimedia->filename) }}" download="{{ $multimedia->filename }}">
                    <button class="text-success"><i class="lni lni-download"></i></button>
                  </a>
                </div>
                @can('multimedia-sa-edit')
                  <div class="action">
                    <a href="javascript:void(0)" class="btn-edit" data-toggle="modal" data-target="#imageModal" data-id="{{$multimedia->id}}" data-image="{{$multimedia->filename}}">
                      <button class="text-info"><i class="lni lni-pencil"></i></button>
                    </a>
                  </div>
                @endcan
                @can('multimedia-sa-delete')
                  <form method="POST" action="{{ url('/admin/multimedia/delete-file/'.$multimedia->id) }}">
                    <div class="action">
                      @csrf
                      @method('DELETE')
                      <input type="hidden" name="id" value="{{$multimedia->id}}"/>
                      <button type="submit" class="text-danger"><i class="lni lni-trash-can"></i></button>
                    </div>
                  </form>
                @endcan
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