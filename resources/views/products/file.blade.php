<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <tr>
                        <th colspan="2" class="text-center">{{ $product->name }}</th>
                    </tr>
                    @if(count($product->files)> 0)
                        @foreach($product->files as $data)
                            <tr>
                                <td>
                                    {{ $data->file_name }}
                                </td>
                                <td>
                                    <a target="_new" href="{{ asset('product_upload/'.$product->existing_attatchment_id.'/'.$data->file_name) }}">Download</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="2" class="text-center text-warning">No File Found</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
