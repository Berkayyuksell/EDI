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
                <th>STORE ID</th>
                <th>bill of transport</th>
                <th>ean number</th>
                <th>item number </th>
                <th>item description</th>
                <th>colour</th>
                <th>retail price</th>
                <th>İşlemler</th>
            </tr>
        </thead>

        <tbody>
            @foreach($packagepDetail as $pack)
                <tr>
                    <td>{{ $pack->store_id_receiver }}</td>
                    <td>{{ $pack->bill_of_transport }}</td>
                    <td>{{ $pack->ean_number  }}</td>
                    <td>{{ $pack->item_number }}</td>
                    <td>{{ $pack->item_description }}</td>
                    <th>{{ $pack->colour}}</th>
                    <th> {{$pack->retail_price_1 }}</th>
                    <th> <a href="{{ route('packing.pdetail', $pack->bill_of_transport) }}" class = "btn btn-sm btn-primary">ÜRÜNÜ BİLDİR</a></th>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Sayfalama -->
    <div class="d-flex justify-content-center">
        {{ $packagepDetail->links('pagination::bootstrap-5') }}
    </div>
</div>



    </body>
    <!-- Bootstrap JS (opsiyonel, modal vb. için) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
</html>