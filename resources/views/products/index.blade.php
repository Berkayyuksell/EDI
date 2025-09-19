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
                <th>ADRESS</th>
                <th>Approve</th>
                <th>Aksiyon</th>
            </tr>
        </thead>
        <tbody>
            @foreach($package as $pack)
                <tr>
                    <td>{{ $pack->id }}</td>
                    <td>{{ $pack->store_id_deliverer }}</td>
                    <td>{{ $pack->store_id_receiver  }}</td>
                    <td>{{ $pack->bill_of_transport }}</td>
                    <td>{{ $pack->bill_of_transport_date }}</td>
                    <td>{{ $pack->recipient}}</td>
                    <th>{{ $pack->isApprove}}</th>
                    <td><a href="{{ route('packing.detail', $pack->bill_of_transport) }}" class="btn btn-sm btn-primary">
                        Detay
                    </a>
                <a class = "btn btn-sm btn-primary "> ONAYLA</a>
            </td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Sayfalama -->
    <div class="d-flex justify-content-center">
        {{ $package->links('pagination::bootstrap-5') }}
    </div>
</div>



    </body>
    <!-- Bootstrap JS (opsiyonel, modal vb. için) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
</html>