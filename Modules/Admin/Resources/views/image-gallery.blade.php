<!doctype html>
<html lang="en">
  <head>
    <title>Upload Image and Remove in Laravel 8 </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>

  <style>
      .table tr td {
          vertical-align: middle;
      }
  </style>
  <body>

    <div class="container py-5">

        <h4 class="text-center font-weight-bold">Image Upload and Remove</h4>

        <div class="row py-2">
            <div class="col-xl-6">
                @if(Session::get('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>{{Session::get('success')}}
                    </div>

                @elseif(Session::get('failed'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>{{Session::get('failed')}}
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title-wrapper d-flex justify-content-between">
                    <h5 class="card-title font-weight-bold">Images</h5>

                    <a href="javascript:void(0)" data-toggle="modal" data-target="#imageModal" class="btn btn-sm btn-primary pt-2 add-new-btn">Add New </a>
                </div>

            </div>

            {{-- table --}}
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if ($images)
                            @foreach($images as $image)
                                <tr>
                                    <td>{{$image->filename}}</td>
                                    <td>
                                        <a class="thumbnail fancybox" rel="ligthbox" href="{{ asset('/public/images/products/'.$image->filename) }}">
                                            <img class="img-responsive" width="150" height="150" alt="{{ $image->filename }}" src="{{ asset('/public/images/products/'.$image->filename) }}" /> 
                                        </a>
                                    </td>
                                    <td class="d-flex">
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#imageModal" data-id="{{$image->id}}" data-image="{{$image->filename}}" class="btn btn-sm btn-success btn-edit">Edit</a>
                                        <form action="{{ url('/admin/products/image-delete/'.$image->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="id" value="{{$image->id}}"/>
                                            <button type="submit" class="btn btn-sm btn-danger ml-2">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <h4 class="text-danger text-center">No image found.</h4>
                        @endif
                    </tbody>
                </table>
            </div>
        </div> {{-- card ends --}}



        {{-- modal --}}
        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form action="{{ url('/admin/products/upload-image') }}" class="w-100" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold">Upload Image</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="col-xl-12 m-auto">
                                <input type="hidden" name="imageId" id="imageId">
                                <input type="hidden" name="oldImage" id="oldImage">
                                <div class="form-group file-input">
                                    <input type="file" name="filename" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {

            $(".add-new-btn").click(function() {
                $("#imageId").val("");
                $("#oldImage").val("");
                $("div .old-img").remove();
            });


            $(".btn-edit").click(function() {

                var id = $(this).attr("data-id");
                var imageName = $(this).attr("data-image");

                $("#imageId").val(id);
                $("#oldImage").val(imageName);

                if(imageName !== undefined) {

                    $(".modal-title").text("Update Image");

                    $(".file-input").after("<div class='form-group old-img'><img src='/public/images/products/"+imageName+"' style='height:100px;'></div>");
                }
            });

        });

    </script>

<script type="text/javascript">
    $(document).ready(function(){
        $(".fancybox").fancybox({
            openEffect: "none",
            closeEffect: "none"
        });
    });
  </script>
  </body>
</html>