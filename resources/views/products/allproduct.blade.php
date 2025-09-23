@extends('layouts.app')

@section('title', 'Gelen Paketler')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tüm Ürünler</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>TÜM GELEN ÜRÜNLER</h2>

    <form action="{{ route('packing.allproduct') }}" method="GET" class="mb-3 d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Ara..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Ara</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>STORE ID</th>
                <th>Bill of Transport</th>
                <th>Transport Date</th>
                <th>Box ean number</th>
                <th>EAN Number</th>
                <th>Item Number</th>
                <th>Item Description</th>
                <th>Colour</th>
                <th>Retail Price</th>
                <th>quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allProduct as $pack)
            <tr>
                <td>{{ $pack->store_id_receiver }}</td>
                <td>{{ $pack->bill_of_transport }}</td>
                <th>{{ $pack->bill_of_transport_date}}</th>
                <td>{{ $pack->box_ean_number }} </td>
                <td>{{ $pack->ean_number }}</td>
                <td>{{ $pack->item_number }}</td>
                <td>{{ $pack->item_description }}</td>
                <td>{{ $pack->colour }}</td>
                <td>{{ $pack->retail_price_1 }}</td>
                <td>{{ $pack->quantity}}</td>

            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $allProduct->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST">
        @csrf
        <input type="hidden" name="bill_of_transport" id="modal_bill">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Ürünü Bildir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="transaction_code" class="form-label">Transaction Tipi</label>
                    <select name="transaction_code" id="transaction_code" class="form-control" required>
                        <option value="">Seçiniz</option>
                        <option value="0303">0303 - Goods returned to Coin/Oviesse</option>
                        <option value="0105">0105 - Corrections to arrivals from Coin/Oviesse</option>
                        <option value="0151">0151 - Damaged/Faulty Items</option>
                        <option value="0351">0351 - Transfer between stores/warehouses</option>
                    </select>
                </div>

                <div class="mb-3" id="cause_div" style="display:none;">
                    <label for="cause_code" class="form-label">Cause (Sadece 0151 için)</label>
                    <select name="cause_code" id="cause_code" class="form-control">
                        <option value="">Seçiniz</option>
                        <option value="0070">0070 - Unsellable goods</option>
                        <option value="0071">0071 - Flood</option>
                        <option value="0072">0072 - Theft</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">Açıklama</label>
                    <textarea name="comment" id="comment" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Kaydet</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Modal açma
    document.querySelectorAll('.report-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            let bill = this.dataset.bill;
            document.getElementById('modal_bill').value = bill;
            let modal = new bootstrap.Modal(document.getElementById('reportModal'));
            modal.show();
        });
    });

    // Transaction seçimine göre cause dropdown göster/gizle
    document.getElementById('transaction_code').addEventListener('change', function() {
        let causeDiv = document.getElementById('cause_div');
        if(this.value === '0151') {
            causeDiv.style.display = 'block';
        } else {
            causeDiv.style.display = 'none';
            document.getElementById('cause_code').value = '';
        }
    });
</script>
</body>
</html>


@endsection