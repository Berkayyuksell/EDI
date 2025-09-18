<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<head>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <body>


<div class="container">
    <h2>GELEN PAKETLER</h2>
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>id</th>
                <th>store id deliverer</th>
                <th>store id reciever</th>
                <th>bill_of_transport </th>
                <th>bill_of_transport_date</th>
                <th>package grouping number</th>
                <th>box ean number</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($packageDetail as $pack)
                <tr>
                    <td>{{ $pack->id }}</td>
                    <td>{{ $pack->store_id_deliverer }}</td>
                    <td>{{ $pack->store_id_receiver  }}</td>
                    <td>{{ $pack->bill_of_transport }}</td>
                    <td>{{ $pack->bill_of_transport_date }}</td>
                    <th>{{ $pack->package_grouping_number}}</th>
                    <th>{{ $pack->box_ean_number}}</th>
                    <th> <a href="{{ route('packing.pdetail', $pack->package_grouping_number) }}" class = "btn btn-sm btn-primary">Detay</a></th>
                    
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Sayfalama -->
    <div class="d-flex justify-content-center">
        {{ $packageDetail->links('pagination::bootstrap-5') }}
    </div>
</div>



    </body>
    <!-- Bootstrap JS (opsiyonel, modal vb. için) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
</html>