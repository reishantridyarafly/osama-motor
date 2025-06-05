@extends('layouts.backend.main')
@section('title', 'Barang Keluar')
@section('content')
    <div class="body-wrapper">
        <div class="container-fluid">
            <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
                <div class="card-body px-4 py-3">
                    <div class="row align-items-center">
                        <div class="col-9">
                            <h4 class="fw-semibold mb-8">@yield('title')</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a class="text-muted text-decoration-none"
                                            href="{{ route('dashboard.index') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-3">
                            <div class="text-center mb-n5">
                                <img src="{{ asset('assets') }}/images/breadcrumb/ChatBc.png" alt="modernize-img"
                                    class="img-fluid mb-n4" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="datatables">
                <div class="card">
                    <div class="card-body">
                        @if (auth()->user()->role == 'cashier')
                            <div class="ms-auto">
                                <div class="d-flex justify-content-end mb-3">
                                    <button class="btn btn-primary" id="btnAdd">
                                        <i class="ti ti-plus me-1"></i>
                                        <span>Tambah @yield('title')</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="datatable" class="table text-nowrap align-middle">
                                <thead>
                                    <tr>
                                        <th width="3%">#</th>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                        <th>Harga Jual</th>
                                        <th>Total Harga</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal" class="modal fade" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="form">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalLabel"></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <div id="item-container">
                            <div class="item-row mb-4 border-bottom pb-3">
                                <div class="row">
                                    <div class="col-md-12 mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Item #1</h5>
                                            <button type="button" class="btn btn-sm btn-danger btn-remove-item d-none">
                                                <i class="ti ti-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="item-0" class="form-label">Barang <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control item-select" data-index="0" id="item-0"
                                            name="items[0][id]">
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger error-items.0.id"></small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="quantity-0" class="form-label">Qty <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="items[0][quantity]" id="quantity-0"
                                                class="form-control item-quantity" data-index="0">
                                            <span class="input-group-text stock-info" id="stock-info-0">Stok: -</span>
                                        </div>
                                        <small class="text-danger error-items.0.quantity"></small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="price_sale-0" class="form-label">Harga Jual <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="items[0][price_sale]" id="price_sale-0"
                                            class="form-control" data-index="0">
                                        <small class="text-danger error-items.0.price_sale"></small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="total_price-0" class="form-label">Total Harga</label>
                                        <input type="number" class="form-control total-price" id="total_price-0"
                                            name="items[0][total_price]" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-info" id="add-item-btn">
                                <i class="ti ti-plus me-1"></i> Tambah Item
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
</div>@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let itemCount = 1; // Mengikuti jumlah item yang ada di UI, dimulai dari 1 (Item #1)

            $('#datatable').DataTable({
                processing: true,
                serverside: true,
                ajax: "{{ route('stockOut.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'item',
                        name: 'item'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'price_sale',
                        name: 'price_sale'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#btnAdd').click(function() {
                $('#modalLabel').html("Tambah Barang Keluar");
                $('#modal').modal('show');
                resetForm();
            });

            $('#add-item-btn').click(function() {
                addItemRow();
            });

            $(document).on('click', '.btn-remove-item', function() {
                $(this).closest('.item-row').remove();
                updateItemNumbers();
                if (itemCount === 1) {
                    $('.btn-remove-item').addClass('d-none');
                }
            });

            // Check stock availability and get sale price when item is selected
            $(document).on('change', '.item-select', function() {
                const index = $(this).data('index');
                const itemId = $(this).val();

                if (itemId) {
                    checkItemStock(itemId, index);
                } else {
                    $(`#stock-info-${index}`).text('Stok: -');
                    $(`#stock-info-${index}`).removeData(
                        'available-stock'); // Hapus data stok yang tersedia
                    $(`#stock-info-${index}`).removeData(
                        'sellable-stock'); // Hapus data stok yang bisa dijual
                    $(`#stock-info-${index}`).removeData('safety-stock'); // Hapus data safety stock

                    $(`#price_sale-${index}`).val('');
                    $(`#total_price-${index}`).val('');
                }
            });

            // Calculate total price and validate quantity when quantity or price changes
            $(document).on('input', '.item-quantity, [id^=price_sale-]', function() {
                const index = $(this).data('index') || $(this).attr('id').split('-')[1];
                calculateTotalPrice(index);
                validateQuantity(index); // Validasi kuantitas saat berubah
            });

            $('#form').submit(function(e) {
                e.preventDefault();

                // Clear all previous error messages
                $('.text-danger').html('');

                const formData = $(this).serializeArray();
                let items = [];
                let currentItem = {};
                let currentIndex = -1;
                let hasValidationErrors = false; // Flag untuk melacak kesalahan validasi di sisi klien

                formData.forEach(field => {
                    const match = field.name.match(/items\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = parseInt(match[1]);
                        const key = match[2];

                        if (index !== currentIndex) {
                            if (currentIndex !== -1) {
                                items.push(currentItem);
                            }
                            currentItem = {};
                            currentIndex = index;
                        }

                        currentItem[key] = field.value;
                    }
                });

                // Push the last item
                if (Object.keys(currentItem).length > 0) {
                    items.push(currentItem);
                }

                // Client-side validation before sending to server
                items.forEach((item, index) => {
                    if (!item.id) {
                        $(`.error-items\\.${index}\\.id`).html('Barang harus diisi.');
                        hasValidationErrors = true;
                    }
                    if (!item.quantity || parseInt(item.quantity) <= 0) {
                        $(`.error-items\\.${index}\\.quantity`).html('Jumlah barang minimal 1.');
                        hasValidationErrors = true;
                    } else {
                        // Re-validate quantity against sellable stock
                        const sellableStock = $(`#stock-info-${index}`).data('sellable-stock');
                        if (sellableStock !== undefined && parseInt(item.quantity) >
                            sellableStock) {
                            const totalAvailable = $(`#stock-info-${index}`).data(
                                'available-stock');
                            const safetyStock = $(`#stock-info-${index}`).data('safety-stock');
                            $(`.error-items\\.${index}\\.quantity`).html(
                                `Kuantitas melebihi stok yang aman untuk dijual. Stok aman: ${sellableStock} (Total: ${totalAvailable}, Safety Stock: ${safetyStock}).`
                            );
                            hasValidationErrors = true;
                        }
                    }
                    if (!item.price_sale || parseFloat(item.price_sale) <= 0) {
                        $(`.error-items\\.${index}\\.price_sale`).html('Harga jual minimal 0.');
                        hasValidationErrors = true;
                    }
                });


                if (hasValidationErrors) {
                    toastr.error('Harap perbaiki kesalahan pada formulir.', 'Validasi Gagal', {
                        closeButton: true,
                        progressBar: true,
                        timeOut: 3000
                    });
                    return; // Stop form submission
                }

                $.ajax({
                    url: "{{ route('stockOut.store') }}",
                    type: "POST",
                    data: {
                        items: items
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save').attr('disabled', 'disabled');
                        $('#save').text('Proses...');
                    },
                    complete: function() {
                        $('#save').removeAttr('disabled');
                        $('#save').text('Simpan');
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#modal').modal('hide');
                            resetForm();
                            toastr.success(response.message, 'Sukses', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 2000
                            });
                            $('#datatable').DataTable().ajax.reload();
                        } else if (response.status === 'partial_success') {
                            let errorMsg = 'Penjualan sebagian berhasil. ';
                            if (response.failed_items.length > 0) {
                                errorMsg += 'Beberapa barang tidak dapat diproses karena: <br>';
                                response.failed_items.forEach(item => {
                                    errorMsg +=
                                        `- <strong>${item.item_name}</strong>: ${item.message}<br>`;
                                });
                            }

                            toastr.warning(errorMsg, 'Sebagian Berhasil', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 7000, // Durasi lebih lama untuk pesan yang lebih panjang
                                escapeHtml: false // Izinkan HTML dalam pesan
                            });

                            if (response.success_items.length > 0) {
                                $('#datatable').DataTable().ajax.reload();
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const [key, messages] of Object.entries(errors)) {
                                const keyParts = key.split('.');
                                if (keyParts.length === 3) {
                                    const index = keyParts[1];
                                    const field = keyParts[2];
                                    $(`.error-${key}`).html(messages.join(
                                        '<br>')); // Menggunakan key lengkap untuk error
                                } else {
                                    // Untuk error non-item-specific, tampilkan di toastr
                                    toastr.error(messages.join('<br>'), 'Validasi Gagal', {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 3000
                                    });
                                }
                            }
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message, 'Kesalahan', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 2000
                            });
                        } else {
                            toastr.error(
                                'Terjadi kesalahan, silakan coba lagi.',
                                'Kesalahan', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 2000
                                });
                        }
                    }
                });
            });

            function checkItemStock(itemId, index) {
                $.ajax({
                    url: "{{ route('stockOut.getItemStock') }}",
                    type: "GET",
                    data: {
                        item_id: itemId
                    },
                    dataType: 'json',
                    success: function(response) {
                        $(`#stock-info-${index}`).text(
                            `Stok: ${response.available}`
                        );
                        // Simpan data stok di elemen untuk validasi nanti
                        $(`#stock-info-${index}`).data('available-stock', response.available);
                        $(`#stock-info-${index}`).data('sellable-stock', response.sellable_stock);
                        $(`#stock-info-${index}`).data('safety-stock', response.safety_stock_quantity);

                        if (response.price_sale) {
                            $(`#price_sale-${index}`).val(response.price_sale);
                            calculateTotalPrice(index);
                        }
                        validateQuantity(index); // Validasi kuantitas setelah update stok
                    },
                    error: function() {
                        $(`#stock-info-${index}`).text('Stok: Error');
                        $(`#stock-info-${index}`).removeData('available-stock');
                        $(`#stock-info-${index}`).removeData('sellable-stock');
                        $(`#stock-info-${index}`).removeData('safety-stock');
                        toastr.error('Gagal mengambil data stok.', 'Kesalahan', {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 2000
                        });
                    }
                });
            }

            function calculateTotalPrice(index) {
                const quantity = parseFloat($(`#quantity-${index}`).val()) || 0;
                const price = parseFloat($(`#price_sale-${index}`).val()) || 0;
                const total = quantity * price;

                $(`#total_price-${index}`).val(total);
            }

            function validateQuantity(index) {
                const quantityInput = $(`#quantity-${index}`);
                const quantity = parseFloat(quantityInput.val());
                const sellableStock = $(`#stock-info-${index}`).data('sellable-stock');
                const totalAvailable = $(`#stock-info-${index}`).data('available-stock');
                const safetyStock = $(`#stock-info-${index}`).data('safety-stock');
                const errorElement = $(`.error-items\\.${index}\\.quantity`);

                errorElement.html(''); // Clear previous error

                if (isNaN(quantity) || quantity <= 0) {
                    errorElement.html('Jumlah barang minimal 1.');
                } else if (sellableStock !== undefined && quantity > sellableStock) {
                    errorElement.html(
                        `Kuantitas melebihi stok yang aman untuk dijual. Stok aman: ${sellableStock} (Total: ${totalAvailable}, Safety Stock: ${safetyStock}).`
                    );
                    toastr.warning(
                        `Kuantitas untuk item #${index + 1} melebihi stok yang aman untuk dijual. Stok aman: ${sellableStock}.`,
                        'Peringatan Stok', {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 5000
                        });
                }
            }

            function addItemRow() {
                const index = itemCount;
                const newRow = `
                <div class="item-row mb-4 border-bottom pb-3">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Item #${index + 1}</h5>
                                <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                                    <i class="ti ti-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="item-${index}" class="form-label">Barang <span class="text-danger">*</span></label>
                            <select class="form-control item-select" data-index="${index}" id="item-${index}" name="items[${index}][id]">
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger error-items.${index}.id"></small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity-${index}" class="form-label">Qty <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="items[${index}][quantity]" id="quantity-${index}" class="form-control item-quantity" data-index="${index}">
                                <span class="input-group-text stock-info" id="stock-info-${index}">Stok: -</span>
                            </div>
                            <small class="text-danger error-items.${index}.quantity"></small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="price_sale-${index}" class="form-label">Harga Jual <span class="text-danger">*</span></label>
                            <input type="number" name="items[${index}][price_sale]" id="price_sale-${index}" class="form-control" data-index="${index}">
                            <small class="text-danger error-items.${index}.price_sale"></small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="total_price-${index}" class="form-label">Total Harga</label>
                            <input type="number" class="form-control total-price" id="total_price-${index}" name="items[${index}][total_price]" readonly>
                        </div>
                    </div>
                </div>
            `;

                $('#item-container').append(newRow);
                itemCount++;

                // Show all remove buttons if we have more than one item
                if (itemCount > 1) {
                    $('.btn-remove-item').removeClass('d-none');
                }
            }

            function updateItemNumbers() {
                $('.item-row').each(function(i) {
                    $(this).find('h5').text(`Item #${i + 1}`);
                    // Update data-index and name attributes for correctness
                    $(this).find('.item-select').attr({
                        'data-index': i,
                        'id': `item-${i}`,
                        'name': `items[${i}][id]`
                    });
                    $(this).find('.item-quantity').attr({
                        'data-index': i,
                        'id': `quantity-${i}`,
                        'name': `items[${i}][quantity]`
                    });
                    $(this).find('[id^=price_sale-]').attr({
                        'data-index': i,
                        'id': `price_sale-${i}`,
                        'name': `items[${i}][price_sale]`
                    });
                    $(this).find('.total-price').attr({
                        'id': `total_price-${i}`,
                        'name': `items[${i}][total_price]`
                    });
                    $(this).find('.stock-info').attr('id', `stock-info-${i}`);
                    $(this).find('[class^=error-item-]').attr('class', `text-danger error-items.${i}.id`);
                    $(this).find('[class^=error-quantity-]').attr('class',
                        `text-danger error-items.${i}.quantity`);
                    $(this).find('[class^=error-price_sale-]').attr('class',
                        `text-danger error-items.${i}.price_sale`);
                });
                itemCount = $('.item-row').length;
            }


            function resetForm() {
                $('#form').trigger("reset");
                // Remove all item rows except the first one
                $('.item-row:not(:first)').remove();

                // Reset the first row
                $('#item-0').val('');
                $('#quantity-0').val('');
                $('#price_sale-0').val('');
                $('#total_price-0').val('');
                $('#stock-info-0').text('Stok: -');
                $('#stock-info-0').removeData('available-stock');
                $('#stock-info-0').removeData('sellable-stock');
                $('#stock-info-0').removeData('safety-stock');

                // Hide the remove button on the first row
                $('.btn-remove-item').addClass('d-none');

                // Reset error messages
                $('.text-danger').html('');

                // Reset counter
                itemCount = 1;
            }

            $(document).on('click', '#btnPrint', function() {
                const id = $(this).data('id');
                const url = `/barang-keluar/print/${id}`;

                window.open(url, '_blank');
            });
        });
    </script>
@endsection
