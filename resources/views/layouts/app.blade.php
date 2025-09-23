<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My App')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Ortak Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">📦 Paket Yönetimi</a>
            <div>
                <a class="btn btn-sm btn-light" href="{{ route('packing.index') }}">Paketler</a>
                <a class="btn btn-sm btn-light" href="{{ url('item')  }}">Ürünler</a>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')   <!-- Sayfa içeriği buraya gelecek -->
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-2 mt-3">
        <small>© {{ date('Y') }} Paket Sistemi</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
