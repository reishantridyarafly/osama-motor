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
                        <div class="ms-auto">
                            <div class="d-flex justify-content-end mb-3">
                                <button class="btn btn-primary" id="btnAdd">
                                    <i class="ti ti-plus me-1"></i>
                                    <span>Tambah @yield('title')</span>
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="datatable" class="table text-nowrap align-middle">
                                <thead>
                                    <tr>
                                        <th width="3%">#</th>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Tanggal</th>
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
                            <label for="item" class="form-label">Barang <span class="text-danger">*</span></label>
                            <select class="form-control" id="item" name="item">
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger errorItem"></small>
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
                        data: 'unit_price',
                        name: 'unit_price'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                ]
            });

            $('#btnAdd').click(function() {
                $('#id').val('');
                $('#modalLabel').html("Tambah Barang Masuk");
                $('#modal').modal('show');
                $('#form').trigger("reset");

                $('#item').removeClass('is-invalid');
                $('.errorItem').html('');

                $('#quantity').removeClass('is-invalid');
                $('.errorQuantity').html('');
            });


            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('stockOut.store') }}",
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
                            if (response.errors.item) {
                                $('#item').addClass('is-invalid');
                                $('.errorItem').html(response.errors.item.join(
                                    '<br>'));
                            } else {
                                $('#item').removeClass('is-invalid');
                                $('.errorItem').html('');
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
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            toastr.error(xhr.responseJSON.error, 'Kesalahan Stok', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 2000
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
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
        });
    </script>
@endsection
