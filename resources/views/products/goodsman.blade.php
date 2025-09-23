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
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
    </div>
@endif

<div class="container">
    <h2>BİLDİRİLEN ÜRÜNLER</h2>
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>id</th>
                <th>store name</th>
                <th>item description</th>
                <th>colour </th>
                <th>bill of transport</th>
                <th>ean number</th>
                <th>box ean number</th>
                <th>quantity</th>
                <th>bill of transport date</th>
                <th>is send</th>
                <th>Sil</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                <tr>
                    <td>{{ $report->id }}</td>
                                        @php
                    $storeNames = [
                        '3957' => 'Emaar point',
                        '5009' => 'Ankara Cepa',
                        '6412' => 'Koru Florya',
                        '9282' => 'Emaar Magaza',
                        '9283' => 'Stefanel WEB',
                    ];
                     @endphp

                    <td>{{ $storeNames[$report->store_id_receiver] ?? $report->store_id_receiver }}</td>
                    <th>{{ $report->item_description}} </th>
                    <th>{{ $report->colour}} </th>
                    <td>{{ $report->bill_of_transport }}</td>
                    <th>{{ $report->ean_number}}</th>
                    <th>{{ $report->box_ean_number}}</th>
                    <th>{{ $report->quantity}} </th>
                    <td>{{ $report->bill_of_transport_date }}</td>
                    @if($report->isSend == 1)
                    <td class="text-center text-success">
                            <i class="bi bi-check-circle-fill"></i> BİLDİRİLDİ
                    </td>
                    @else
                    <td class="text-center text-secondary">
                            <i class="bi bi-clock-fill"></i> Beklemede
                    </td>
                    @endif

                    <td>
    <form action="{{ route('reports.delete', $report->id) }}" method="POST" 
          onsubmit="return confirm('Bu raporu silmek istediğinize emin misiniz?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">Sil</button>
    </form>
</td>


                    
                </tr>
            @endforeach
    </tbody>
  </table>

  <!-- Sayfalama -->
  <div class="d-flex justify-content-center">
    {{ $reports->links('pagination::bootstrap-5') }}
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

@endsection
