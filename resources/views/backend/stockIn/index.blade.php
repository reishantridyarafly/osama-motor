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
        <div class="modal-dialog modal-lg">
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
                        <div class="mb-3">
                            <label for="item" class="form-label">Barang <span class="text-danger">*</span></label>
                            <select class="form-control" id="item" name="item" disabled>
                                <option value="">-- Pilih Barang --</option>
                            </select>
                            <small class="text-danger errorItem"></small>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <input type="number" name="price" id="price" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stok Tersedia</label>
                            <input type="number" name="stock" id="stock" class="form-control" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="price_sale" class="form-label">Harga Jual <span class="text-danger">*</span></label>
                            <input type="number" name="price_sale" id="price_sale" class="form-control">
                            <small class="text-danger errorPriceSale"></small>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Qty <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" class="form-control">
                            <small class="text-danger errorQuantity"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"></script>

    <script>
        $(document).ready(function() {
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

            $('#btnAdd').click(function() {
                $('#id').val('');
                $('#modalLabel').html("Tambah Barang Masuk");
                $('#modal').modal('show');
                $('#form').trigger("reset");

                $('#supplier').removeClass('is-invalid');
                $('.errorSupplier').html('');

                $('#item').removeClass('is-invalid');
                $('.errorItem').html('');

                $('#price_sale').removeClass('is-invalid');
                $('.errorPriceSale').html('');

                $('#quantity').removeClass('is-invalid');
                $('.errorQuantity').html('');
            });

            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "barang-masuk/" + id + "/edit",
                    dataType: "json",
                    success: function(response) {
                        $('#modalLabel').html("Edit Barang masuk");
                        $('#save').val("edit-barang-masuk");
                        $('#modal').modal('show');

                        $('#supplier').removeClass('is-invalid');
                        $('.errorSupplier').html('');

                        $('#item').removeClass('is-invalid');
                        $('.errorItem').html('');

                        $('#price_sale').removeClass('is-invalid');
                        $('.errorPriceSale').html('');

                        $('#quantity').removeClass('is-invalid');
                        $('.errorQuantity').html('');

                        $('#id').val(response.id);
                        $('#supplier').val(response.supplier_id);
                        $('#price_sale').val(response.price_sale);
                        $('#price').val(response.price);
                        $('#stock').val(response.stock);

                        $('#item').prop('disabled', false);

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
                                $('#item').html(options);
                                $('#item').val(response.item_id);

                                const selectedItem = $('#item option:selected');
                                if (selectedItem.val()) {
                                    const itemPrice = selectedItem.data('price');
                                    const itemStock = selectedItem.data('stock');
                                    $('#price').val(itemPrice);
                                    $('#stock').val(itemStock);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Gagal mengambil data barang',
                                    'Kesalahan', {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 2000
                                    });
                            }
                        });

                        $('#quantity').val(response.quantity);
                    }
                });
            })

            $('#supplier').on('change', function() {
                const supplierId = $(this).val();
                const itemSelect = $('#item');

                if (!supplierId) {
                    itemSelect.html('<option value="">-- Pilih Barang --</option>').prop('disabled', true);
                    $('#price').val('');
                    return;
                }

                itemSelect.prop('disabled', false);

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
                        itemSelect.html(options);
                    },
                    error: function(xhr) {
                        toastr.error('Gagal mengambil data barang', 'Kesalahan', {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 2000
                        });
                        itemSelect.html('<option value="">-- Pilih Barang --</option>');
                    }
                });
            });

            $('#item').on('change', function() {
                const selectedItem = $(this).find('option:selected');
                if (selectedItem.val()) {
                    const itemPrice = selectedItem.data('price');
                    const itemStock = selectedItem.data('stock');

                    $('#price').val(itemPrice);
                    $('#stock').val(itemStock);
                } else {
                    $('#price').val('');
                    $('#stock').val('');
                }
            });

            $('#modal').on('hidden.bs.modal', function() {
                $('#item').html('<option value="">-- Pilih Barang --</option>').prop('disabled', true);
                $('#price').val('');
            });

            $('#form').submit(function(e) {
                e.preventDefault();

                const quantity = parseInt($('#quantity').val());
                const stock = parseInt($('#stock').val());

                if (quantity > stock) {
                    toastr.error('Jumlah yang dimasukkan melebihi stok tersedia!', 'Kesalahan', {
                        closeButton: true,
                        progressBar: true,
                        timeOut: 2000
                    });
                    return;
                }

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('stockIn.store') }}",
                    type: "POST",
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
                        if (response.errors) {
                            if (response.errors.supplier) {
                                $('#supplier').addClass('is-invalid');
                                $('.errorSupplier').html(response.errors.supplier.join(
                                    '<br>'));
                            } else {
                                $('#supplier').removeClass('is-invalid');
                                $('.errorSupplier').html('');
                            }

                            if (response.errors.item) {
                                $('#item').addClass('is-invalid');
                                $('.errorItem').html(response.errors.item.join(
                                    '<br>'));
                            } else {
                                $('#item').removeClass('is-invalid');
                                $('.errorItem').html('');
                            }

                            if (response.errors.price_sale) {
                                $('#price_sale').addClass('is-invalid');
                                $('.errorPriceSale').html(response.errors.price_sale.join(
                                    '<br>'));
                            } else {
                                $('#price_sale').removeClass('is-invalid');
                                $('.errorPriceSale').html('');
                            }

                            if (response.errors.quantity) {
                                $('#quantity').addClass('is-invalid');
                                $('.errorQuantity').html(response.errors.quantity.join(
                                    '<br>'));
                            } else {
                                $('#quantity').removeClass('is-invalid');
                                $('.errorQuantity').html('');
                            }
                        } else {
                            $('#modal').modal('hide');
                            $('#form').trigger("reset");
                            toastr.success(response.message, 'Sukses', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 2000
                            });
                            $('#datatable').DataTable().ajax.reload()
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key,
                                value) {
                                toastr.error(value.join('<br>'),
                                    'Kesalahan Validasi', {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 2000
                                    });
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
                            url: "{{ url('barang-masuk/" + id + "') }}",
                            data: {
                                id: id
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.message) {
                                    toastr.success(response.message, 'Sukses', {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 2000
                                    });
                                    $('#datatable').DataTable().ajax.reload()
                                }
                            },
                            error: function(xhr) {
                                if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    $.each(xhr.responseJSON.errors, function(key,
                                        value) {
                                        toastr.error(value.join('<br>'),
                                            'Kesalahan Validasi', {
                                                closeButton: true,
                                                progressBar: true,
                                                timeOut: 2000
                                            });
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
                        })
                    }
                })
            })
        });
    </script>
@endsection
