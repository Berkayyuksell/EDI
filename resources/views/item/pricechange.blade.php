@extends('layouts.app')

@section('title', 'Fiyat değişikliği')

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
    <h2>FİYAT DEĞİŞİKLİĞİ</h2>
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>item_numer</th>
                <th>price_start_date</th>
                <th>currency_code_1</th>
                <th>new_retail_price_1</th>
                <th>currency_code_2 </th>
                <th>new_retail_price_2</th>
                <th>currency_code_3</th>
                <th>old_retail_price_3</th>
                <th>cause_description</th>
                <th>discount_percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($priceChange as $pack)
            <tr>
                    <td>{{ $pack->item_number }}</td>
                    <td>{{ $pack->price_start_date  }}</td>
                    <td>{{ $pack->currency_code_1 }}</td>
                    <td>{{ $pack->new_retail_price_1 }}</td>
                    <td>{{ $pack->currency_code_2 }}</td>
                    <td>{{ $pack->new_retail_price_2 }}</td>
                    <td>{{ $pack->currency_code_3 }}</td>
                    <td>{{ $pack->old_retail_price_3}}</td>
                    <td>{{ $pack->cause_description}}</td>
                    <td>{{ $pack->discount_percentage}}</td>


                    
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Sayfalama -->
    <div class="d-flex justify-content-center">
        {{ $priceChange->links('pagination::bootstrap-5') }}
    </div>
</div>



    </body>
    <!-- Bootstrap JS (opsiyonel, modal vb. için) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
</html>

@endsection