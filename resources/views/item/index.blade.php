<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
    <h2>ürün</h2>
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>ürün numarası</th>
                <th>ürün açıklaması</th>
                <th>renk </th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->item_number }}</td>
                    <td>{{ $item->item_description }}</td>
                    <td>{{ $item->colour  }}</td>
                 
                    
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Sayfalama -->
    <div class="d-flex justify-content-center">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
</div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>