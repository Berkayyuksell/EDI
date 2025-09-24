@extends('layouts.app')

@section('title', 'Gelen Paketler')

@section('content')


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
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
    <div class="row mb-3">
        <div class="col-md-12">
            <form method="GET" action="{{ url()->current() }}">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Bill of Transport veya EAN Ara..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Ara</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <h2>GELEN ÜRÜNLER</h2>
    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>STORE ID</th>
                <th>STORE Name</th>
                <th>bill of transport</th>
                <th>barcode</th>
                <th>item number </th>
                <th>box_ean_number</th>
                <th>item description</th>
                <th>colour</th>
                <th>retail price</th>
                <th>quantity</th>
                <th>bill_of_transport_date</th>
                <th>İşlemler</th>
            </tr>
        </thead>

        <tbody>
            @foreach($packagepDetail as $pack)
                <tr>
                    <td>{{ $pack->store_id_receiver }}</td>
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

                    <td>{{ $pack->bill_of_transport }}</td>
                    <td>{{ $pack->ean_number  }}</td>
                    <td>{{ $pack->item_number }}</td>
                    <th>{{ $pack->box_ean_number }}</th>
                    <td>{{ $pack->item_description }}</td>
                    <td>{{ $pack->colour}}</td>
                    <td>{{ intval($pack->retail_price_1) }}</td>
                    <td>{{ $pack->quantity }}</td>
                    <th>{{ $pack->bill_of_transport_date }}</th>


                    <td>
                        <button class="btn btn-sm btn-warning openModalBtn"
                            data-bs-toggle="modal"
                            data-bs-target="#reportModal"
                            data-bill="{{ $pack->bill_of_transport }}"
                            data-ean="{{ $pack->ean_number }}"
                            data-item-number="{{ $pack->item_number }}"
                            data-description="{{ $pack->item_description }}"
                            data-colour="{{ $pack->colour }}"
                            data-price="{{ $pack->retail_price_1 }}"
                            data-store-id-receiver="{{ $pack->store_id_receiver }}"
                            data-box-ean="{{ $pack->box_ean_number }}"
                            data-quantity="{{ $pack->quantity }}"
                            data-date="{{ $pack->bill_of_transport_date }}"
                            data-store-id="{{ $pack->store_id_receiver }}">
                            Ürünü Bildir
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
   </table>



    <!-- Sayfalama -->
    <div class="d-flex justify-content-center">
        {{ $packagepDetail->links('pagination::bootstrap-5') }}
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('package.report') }}">
        @csrf
        <input type="hidden" name="store_id_receiver" id="modal_store_id_receiver">
        <input type="hidden" name="bill_of_transport" id="modal_bill_of_transport">
        <input type="hidden" name="ean_number" id="modal_ean_number">
        <input type="hidden" name="item_number" id="modal_item_number">
        <input type="hidden" name="box_ean_number" id="modal_box_ean_number">
        <input type="hidden" name="item_description" id="modal_item_description">
        <input type="hidden" name="colour" id="modal_colour">
        <input type="hidden" name="retail_price" id="modal_retail_price">
        <input type="hidden" name="quantity" id="modal_quantity">
        <input type="hidden" name="bill_of_transport_date" id="modal_bill_of_transport_date">

        
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Ürünü Bildir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            
            <div class="modal-body">
                <!-- Ürün Bilgileri Bölümü -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Ürün Bilgileri</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Bill of Transport:</strong> <span id="display_bill"></span></p>
                                <p><strong>EAN Number:</strong> <span id="display_ean"></span></p>
                                <p><strong>Item Number:</strong> <span id="display_item_number"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Description:</strong> <span id="display_description"></span></p>
                                <p><strong>Colour:</strong> <span id="display_colour"></span></p>
                                <p><strong>Price:</strong> <span id="display_price"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Alanları -->
                <div class="mb-3">
                    <label for="transaction_code" class="form-label">Transaction Tipi *</label>
                    <select name="transaction_code" id="transaction_code" class="form-control" required>
                        <option value="">Seçiniz</option>
                        <option value="0303">0303 - Goods returned to Coin/Oviesse</option>
                        <option value="0105">0105 - Corrections to arrivals from Coin/Oviesse</option>
                        <option value="0151">0151 - Damaged/Faulty Items</option>
                        <option value="0351">0351 - Transfer between stores/warehouses</option>
                    </select>
                </div>

                <div class="mb-3" id="cause_div" style="display:none;">
                    <label for="cause_code" class="form-label">Cause (Sadece 0151 için) *</label>
                    <select name="cause_code" id="cause_code" class="form-control">
                        <option value="">Seçiniz</option>
                        <option value="0070">0070 - Unsellable goods</option>
                        <option value="0071">0071 - Flood</option>
                        <option value="0072">0072 - Theft</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Miktar *</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1" required>
                </div>

     


            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Kaydet
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> İptal
                </button>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Modal açılma eventi
document.querySelectorAll('.openModalBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Veri bilgilerini al
        const bill = this.dataset.bill;
        const itemId = this.dataset.itemId;
        const ean = this.dataset.ean;
        const itemNumber = this.dataset.itemNumber;
        const description = this.dataset.description;
        const colour = this.dataset.colour;
        const price = this.dataset.price;
        const storeId = this.dataset.storeId;

        // Hidden input'lara değerleri set et
        document.getElementById('modal_store_id_receiver').value = this.dataset.storeIdReceiver;
        document.getElementById('modal_bill_of_transport').value = this.dataset.bill;
        document.getElementById('modal_ean_number').value = this.dataset.ean;
        document.getElementById('modal_item_number').value = this.dataset.itemNumber;
        document.getElementById('modal_box_ean_number').value = this.dataset.boxEan;
        document.getElementById('modal_item_description').value = this.dataset.description;
        document.getElementById('modal_colour').value = this.dataset.colour;
        document.getElementById('modal_retail_price').value = this.dataset.price;
        document.getElementById('modal_quantity').value = this.dataset.quantity;
        document.getElementById('modal_bill_of_transport_date').value = this.dataset.date;

        // Modal'da görünen bilgileri güncelle
        document.getElementById('display_bill').textContent = bill;
        document.getElementById('display_ean').textContent = ean;
        document.getElementById('display_item_number').textContent = itemNumber;
        document.getElementById('display_description').textContent = description;
        document.getElementById('display_colour').textContent = colour;
        document.getElementById('display_price').textContent = price + ' TL';

        // Form alanlarını temizle
        document.getElementById('transaction_code').value = '';
        document.getElementById('cause_code').value = '';
        document.getElementById('quantity').value = '1';
        document.getElementById('comment').value = '';
        document.getElementById('cause_div').style.display = 'none';
    });
});

document.getElementById('transaction_code').addEventListener('change', function() {
    const causeDiv = document.getElementById('cause_div');
    const causeSelect = document.getElementById('cause_code');
    const commentDiv = document.getElementById('comment_div');
    const commentTextarea = document.getElementById('comment');

    if(this.value === '0151') {
        causeDiv.style.display = 'block';
        causeSelect.setAttribute('required', 'required');

        commentDiv.style.display = 'block';
        commentTextarea.setAttribute('required', 'required'); // Açıklamayı zorunlu yap
    } else {
        causeDiv.style.display = 'none';
        causeSelect.removeAttribute('required');
        causeSelect.value = '';

        commentDiv.style.display = 'none';
        commentTextarea.removeAttribute('required'); // Zorunluluğu kaldır
        commentTextarea.value = ''; // textarea temizle
    }
});



// Form validasyonu
document.querySelector('form').addEventListener('submit', function(e) {
    const transactionCode = document.getElementById('transaction_code').value;
    const causeCode = document.getElementById('cause_code').value;
    
    if(transactionCode === '0151' && !causeCode) {
        e.preventDefault();
        alert('0151 seçimi için Cause kodu zorunludur!');
        return false;
    }
});
</script>

</body>
</html>

@endsection