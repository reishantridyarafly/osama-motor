@extends('layouts.backend.main')
@section('title', 'Barang Masuk')
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
                        @if (auth()->user()->role == 'warehouse')
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
                                        <th>Harga Beli</th>
                                        <th>Harga Jual</th>
                                        <th>Supplier</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        @if (auth()->user()->role == 'warehouse')
                                            <th width="10%">Aksi</th>
                                        @endif
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

    <!-- modal -->
    <div id="modal" class="modal fade" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="form">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalLabel"></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="id" id="id">
                            <label for="supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-control" id="supplier" name="supplier">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->first_name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger errorSupplier"></small>
                        </div>

                        <!-- Items Container -->
                        <div id="itemsContainer">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Daftar Barang</h6>
                                <button type="button" class="btn btn-sm btn-success" id="addItemRow">
                                    <i class="ti ti-plus"></i> Tambah Barang
                                </button>
                            </div>

                            <div class="item-row" data-index="0">
                                <div class="card border mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">Barang <span class="text-danger">*</span></label>
                                                <select class="form-control item-select" name="items[0][item_id]"
                                                    data-index="0" disabled>
                                                    <option value="">-- Pilih Barang --</option>
                                                </select>
                                                <small class="text-danger errorItem"></small>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Harga Beli</label>
                                                <input type="number" name="items[0][price_buy]"
                                                    class="form-control price-input" readonly>
                                            </div>
                                            <input type="hidden" name="items[0][stock]" class="stock-input">
                                            <div class="col-md-3">
                                                <label class="form-label">Harga Jual <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="items[0][price_sale]"
                                                    class="form-control price-sale-input">
                                                <small class="text-danger errorPriceSale"></small>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Qty <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" name="items[0][quantity]"
                                                        class="form-control quantity-input">
                                                    <button type="button" class="btn btn-outline-danger remove-item"
                                                        style="display: none;">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                                <small class="text-danger errorQuantity"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal (Single Item) -->
    <div id="editModal" class="modal fade" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editForm">
                    <div class="modal-header">
                        <h4 class="modal-title" id="editModalLabel">Edit Barang Masuk</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="id" id="editId">
                            <label for="editSupplier" class="form-label">Supplier <span
                                    class="text-danger">*</span></label>
                            <select class="form-control" id="editSupplier" name="supplier">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->first_name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger errorEditSupplier"></small>
                        </div>
                        <div class="mb-3">
                            <label for="editItem" class="form-label">Barang <span class="text-danger">*</span></label>
                            <select class="form-control" id="editItem" name="item" disabled>
                                <option value="">-- Pilih Barang --</option>
                            </select>
                            <small class="text-danger errorEditItem"></small>
                        </div>
                        <div class="mb-3">
                            <label for="editPriceSale" class="form-label">Harga Jual <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="price_sale" id="editPriceSale" class="form-control">
                            <small class="text-danger errorEditPriceSale"></small>
                        </div>
                        <div class="mb-3">
                            <label for="editQuantity" class="form-label">Qty <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="editQuantity" class="form-control">
                            <small class="text-danger errorEditQuantity"></small>
                            <input type="hidden" id="editStock">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="editSave">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"></script>

    <script>
        $(document).ready(function() {
            let itemIndex = 0;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#datatable').DataTable({
                processing: true,
                serverside: true,
                ajax: "{{ route('stockIn.index') }}",
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
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'price_sale',
                        name: 'price_sale'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    @if (auth()->user()->role == 'warehouse')
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    @endif
                ]
            });

            // Add new item button
            $('#btnAdd').click(function() {
                $('#id').val('');
                $('#modalLabel').html("Tambah Barang Masuk");
                $('#modal').modal('show');
                $('#form').trigger("reset");
                resetItemRows();
                clearErrors();
            });

            // Add item row
            $('#addItemRow').click(function() {
                itemIndex++;
                addNewItemRow();
                updateRemoveButtons();
            });

            // Remove item row
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.item-row').remove();
                updateRemoveButtons();
            });

            // Supplier change handler
            $('#supplier').on('change', function() {
                const supplierId = $(this).val();

                if (!supplierId) {
                    $('.item-select').html('<option value="">-- Pilih Barang --</option>').prop('disabled',
                        true);
                    $('.price-input').val('');
                    $('.stock-input').val('');
                    return;
                }

                $('.item-select').prop('disabled', false);

                $.ajax({
                    url: "{{ route('items.by.supplier') }}",
                    type: 'GET',
                    data: {
                        supplier_id: supplierId
                    },
                    success: function(response) {
                        let options = '<option value="">-- Pilih Barang --</option>';
                        response.forEach(function(item) {
                            options +=
                                `<option value="${item.id}" data-price="${item.price}" data-stock="${item.stock}">${item.name}</option>`;
                        });
                        $('.item-select').html(options);
                    },
                    error: function(xhr) {
                        toastr.error('Gagal mengambil data barang', 'Kesalahan');
                        $('.item-select').html('<option value="">-- Pilih Barang --</option>');
                    }
                });
            });

            // Item change handler
            $(document).on('change', '.item-select', function() {
                const selectedItem = $(this).find('option:selected');
                const itemRow = $(this).closest('.item-row');

                if (selectedItem.val()) {
                    const itemPrice = selectedItem.data('price');
                    const itemStock = selectedItem.data('stock');

                    itemRow.find('.price-input').val(itemPrice);
                    itemRow.find('.stock-input').val(itemStock);
                } else {
                    itemRow.find('.price-input').val('');
                    itemRow.find('.stock-input').val('');
                }
            });

            // Form submit for multiple items
            $('#form').submit(function(e) {
                e.preventDefault();
                clearErrors();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('stockIn.store') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save').attr('disabled', 'disabled').text('Proses...');
                    },
                    complete: function() {
                        $('#save').removeAttr('disabled').text('Simpan');
                    },
                    success: function(response) {
                        if (response.errors) {
                            handleValidationErrors(response.errors);
                        } else {
                            $('#modal').modal('hide');
                            $('#form').trigger("reset");
                            toastr.success(response.message, 'Sukses');
                            $('#datatable').DataTable().ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr);
                    }
                });
            });

            // Edit button handler
            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "barang-masuk/" + id + "/edit",
                    dataType: "json",
                    success: function(response) {
                        $('#editModal').modal('show');
                        clearEditErrors();

                        $('#editId').val(response.id);
                        $('#editSupplier').val(response.supplier_id);
                        $('#editPriceSale').val(response.price_sale);
                        $('#editStock').val(response.stock);
                        $('#editQuantity').val(response.quantity);

                        $('#editItem').prop('disabled', false);

                        // Load items for edit
                        $.ajax({
                            url: "{{ route('items.by.supplier') }}",
                            type: 'GET',
                            data: {
                                supplier_id: response.supplier_id
                            },
                            success: function(items) {
                                let options =
                                    '<option value="">-- Pilih Barang --</option>';
                                items.forEach(function(item) {
                                    options +=
                                        `<option value="${item.id}" data-price="${item.price}" data-stock="${item.stock}" ${item.id == response.item_id ? 'selected' : ''}>${item.name}</option>`;
                                });
                                $('#editItem').html(options);
                            }
                        });
                    }
                });
            });

            // Edit form submit
            $('#editForm').submit(function(e) {
                e.preventDefault();
                clearEditErrors();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('stockIn.store') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#editSave').attr('disabled', 'disabled').text('Proses...');
                    },
                    complete: function() {
                        $('#editSave').removeAttr('disabled').text('Simpan');
                    },
                    success: function(response) {
                        if (response.errors) {
                            handleEditValidationErrors(response.errors);
                        } else {
                            $('#editModal').modal('hide');
                            toastr.success(response.message, 'Sukses');
                            $('#datatable').DataTable().ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr);
                    }
                });
            });

            // Delete button handler
            $('body').on('click', '#btnDelete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus',
                    text: "Apakah anda yakin?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ url('barang-masuk') }}/" + id,
                            data: {
                                id: id
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.message) {
                                    toastr.success(response.message, 'Sukses');
                                    $('#datatable').DataTable().ajax.reload();
                                }
                            },
                            error: function(xhr) {
                                handleAjaxError(xhr);
                            }
                        });
                    }
                });
            });

            // Helper functions
            function addNewItemRow() {
                const newRow = `
                    <div class="item-row" data-index="${itemIndex}">
                        <div class="card border mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Barang <span class="text-danger">*</span></label>
                                        <select class="form-control item-select" name="items[${itemIndex}][item_id]" data-index="${itemIndex}" disabled>
                                            <option value="">-- Pilih Barang --</option>
                                        </select>
                                        <small class="text-danger errorItem"></small>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Harga Beli</label>
                                        <input type="number" name="items[${itemIndex}][price_buy]" class="form-control price-input" readonly>
                                    </div>
                                    <input type="hidden" name="items[${itemIndex}][stock]" class="stock-input">
                                    <div class="col-md-3">
                                        <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                                        <input type="number" name="items[${itemIndex}][price_sale]" class="form-control price-sale-input">
                                        <small class="text-danger errorPriceSale"></small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Qty <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input">
                                            <button type="button" class="btn btn-outline-danger remove-item">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                        <small class="text-danger errorQuantity"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#itemsContainer').append(newRow);

                const supplierId = $('#supplier').val();
                if (supplierId) {
                    loadItemsForNewRow(supplierId, itemIndex);
                }
            }

            function loadItemsForNewRow(supplierId, index) {
                $.ajax({
                    url: "{{ route('items.by.supplier') }}",
                    type: 'GET',
                    data: {
                        supplier_id: supplierId
                    },
                    success: function(response) {
                        let options = '<option value="">-- Pilih Barang --</option>';
                        response.forEach(function(item) {
                            options +=
                                `<option value="${item.id}" data-price="${item.price}" data-stock="${item.stock}">${item.name}</option>`;
                        });
                        $(`.item-select[data-index="${index}"]`).html(options).prop('disabled', false);
                    }
                });
            }

            function resetItemRows() {
                itemIndex = 0;
                $('#itemsContainer .item-row').not(':first').remove();
                $('#itemsContainer .item-row:first').attr('data-index', '0');
                $('#itemsContainer .item-row:first .item-select').attr('name', 'items[0][item_id]').attr(
                    'data-index', '0');
                $('#itemsContainer .item-row:first .price-input').attr('name', 'items[0][price_buy]');
                $('#itemsContainer .item-row:first .stock-input').attr('name', 'items[0][stock]');
                $('#itemsContainer .item-row:first .price-sale-input').attr('name', 'items[0][price_sale]');
                $('#itemsContainer .item-row:first .quantity-input').attr('name', 'items[0][quantity]');
                updateRemoveButtons();
            }

            function updateRemoveButtons() {
                const itemRows = $('.item-row');
                if (itemRows.length > 1) {
                    $('.remove-item').show();
                } else {
                    $('.remove-item').hide();
                }
            }

            function clearErrors() {
                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
            }

            function clearEditErrors() {
                $('#editForm .form-control').removeClass('is-invalid');
                $('#editForm .text-danger').html('');
            }

            function handleValidationErrors(errors) {
                clearErrors();

                if (errors.supplier) {
                    $('#supplier').addClass('is-invalid');
                    $('.errorSupplier').html(errors.supplier.join('<br>'));
                }

                if (errors.items && Array.isArray(errors.items)) {
                    $.each(errors.items, function(index, itemErrors) {
                        const itemRow = $(`.item-row[data-index="${index}"]`);

                        if (itemErrors.item_id) {
                            itemRow.find('.item-select').addClass('is-invalid');
                            itemRow.find('.errorItem').html(itemErrors.item_id.join('<br>'));
                        }
                        if (itemErrors.price_sale) {
                            itemRow.find('.price-sale-input').addClass('is-invalid');
                            itemRow.find('.errorPriceSale').html(itemErrors.price_sale.join('<br>'));
                        }
                        if (itemErrors.quantity) {
                            itemRow.find('.quantity-input').addClass('is-invalid');
                            itemRow.find('.errorQuantity').html(itemErrors.quantity.join('<br>'));
                        }
                    });
                }

                $.each(errors, function(key, messages) {
                    if (key.startsWith('items.') && key.includes(
                            '.')) {
                        const parts = key.split('.');
                        if (parts.length >= 3) {
                            const index = parts[1];
                            const field = parts[2];
                            const itemRow = $(`.item-row[data-index="${index}"]`);

                            if (itemRow.length > 0) {
                                switch (field) {
                                    case 'item_id':
                                        itemRow.find('.item-select').addClass('is-invalid');
                                        itemRow.find('.errorItem').html(messages.join('<br>'));
                                        break;
                                    case 'quantity':
                                        itemRow.find('.quantity-input').addClass('is-invalid');
                                        itemRow.find('.errorQuantity').html(messages.join('<br>'));
                                        break;
                                    case 'price_sale':
                                        itemRow.find('.price-sale-input').addClass('is-invalid');
                                        itemRow.find('.errorPriceSale').html(messages.join('<br>'));
                                        break;
                                }
                            }
                        } else if (key ===
                            'items') {}
                    }
                });
            }

            function handleEditValidationErrors(errors) {
                clearEditErrors();

                if (errors.supplier) {
                    $('#editSupplier').addClass('is-invalid');
                    $('.errorEditSupplier').html(errors.supplier.join('<br>'));
                }
                if (errors.item) {
                    $('#editItem').addClass('is-invalid');
                    $('.errorEditItem').html(errors.item.join('<br>'));
                }
                if (errors.price_sale) {
                    $('#editPriceSale').addClass('is-invalid');
                    $('.errorEditPriceSale').html(errors.price_sale.join('<br>'));
                }
                if (errors.quantity) {
                    $('#editQuantity').addClass('is-invalid');
                    $('.errorEditQuantity').html(errors.quantity.join('<br>'));
                }
            }

            function handleAjaxError(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        toastr.error(value.join('<br>'), 'Kesalahan Validasi');
                    });
                } else {
                    toastr.error('Terjadi kesalahan, silakan coba lagi.', 'Kesalahan');
                }
            }

            $('#modal').on('hidden.bs.modal', function() {
                resetItemRows();
                $('#supplier').val('');
                $('.item-select').html('<option value="">-- Pilih Barang --</option>').prop('disabled',
                    true);
                $('.price-input').val('');
                $('.stock-input').val('');
                clearErrors();
            });

            $('#editModal').on('hidden.bs.modal', function() {
                $('#editItem').html('<option value="">-- Pilih Barang --</option>').prop('disabled', true);
                $('#editStock').val('');
                clearEditErrors();
            });
        });
    </script>
@endsection
