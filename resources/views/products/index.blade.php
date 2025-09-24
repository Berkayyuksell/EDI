@extends('layouts.app')

@section('title', 'Gelen Paketler')

@section('content')

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
                <th>store name</th>
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
                                        @php
                    $storeNames = [
                        '3957' => 'Emaar point',
                        '5009' => 'Ankara Cepa',
                        '6412' => 'Koru Florya',
                        '9282' => 'Emaar Magaza',
                        '9283' => 'Stefanel WEB',
                    ];
                     @endphp

                    <td>{{ $storeNames[$pack->store_id_receiver] ?? $pack->store_id_receiver }}</td>
                    <td>{{ $pack->store_id_deliverer }}</td>
                    <td>{{ $pack->store_id_receiver  }}</td>
                    <td>{{ $pack->bill_of_transport }}</td>
                    <th>{{ \Carbon\Carbon::createFromFormat('Ymd', $pack->bill_of_transport_date)->format('d.m.Y') }}</th>

                    <td>{{ $pack->recipient}}</td>
                    @if($pack->isApprove == 1)
                        <td class="text-center text-success">
                            <i class="bi bi-check-circle-fill"></i> Onaylı
                        </td>
                    @else
                        <td class="text-center text-secondary">
                            <i class="bi bi-clock-fill"></i> Yolda
                        </td>
                    @endif



           <td>
    <div class="d-flex">
        <!-- Detay butonu -->
        <a href="{{ route('packing.detail', $pack->bill_of_transport) }}" 
           class="btn btn-sm btn-primary me-2">
            Detay
        </a>

        <!-- Onayla butonu -->
        <form action="{{ route('packing.approve', $pack->bill_of_transport) }}" 
              method="POST"
              onsubmit="return confirm('Bu paketi onaylamak istediğinize emin misiniz?')">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">
                Onayla
            </button>
        </form>
    </div>
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

@endsection