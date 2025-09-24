<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container py-5">
        <h1 class="mb-4 text-center">📦STEFANEL Paket & Ürün Yönetim Sistemi</h1>

        <div class="d-grid gap-2 mb-2">
            <a href="{{ route('packing.index') }}" class="btn btn-outline-dark">
                Gelen Paketler
            </a>
            <a href="{{ route('package.reports') }}" class="btn btn-outline-dark">
                Bildirilen Ürünler
            </a>
            <a href="{{ url('item') }}" class="btn btn-outline-dark">
                Ürün Listesi
            </a>
            <a href="{{ url('item/pricechange') }}" class="btn btn-outline-dark">
                Fiyat Değişikliği
            </a>
        </div>
    </div>

</body>
</html>
