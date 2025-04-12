@extends('layouts.backend.main')
@section('title', 'Barang')
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
                                        <th>Nama</th>
                                        <th>Stok</th>
                                        <th>Kategori</th>
                                        <th>Supplier</th>
                                        <th width="10%">Aksi</th>
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
    <div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
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
                            <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" autofocus>
                            <small class="text-danger errorName"></small>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-control">
                                <option value="">-- Pilih kategori --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger errorCategory"></small>
                        </div>
                        <div class="mb-3">
                            <label for="supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier" id="supplier" class="form-control">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->first_name }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger errorSupplier"></small>
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
                ajax: "{{ route('item.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'total_stock',
                        name: 'total_stock'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
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
                $('#form')[0].reset();
                $('#id').val('');
                $('#modalLabel').html("Tambah Barang");
                $('#modal').modal('show');

                $('#name').removeClass('is-invalid');
                $('.errorName').html('');

                $('#category').removeClass('is-invalid');
                $('.errorCategory').html('');

                $('#supplier').removeClass('is-invalid');
                $('.errorSupplier').html('');
            });

            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "barang/" + id + "/edit",
                    dataType: "json",
                    success: function(response) {
                        $('#modalLabel').html("Edit Barang");
                        $('#save').val("edit-barang");
                        $('#modal').modal('show');

                        $('#name').removeClass('is-invalid');
                        $('.errorName').html('');

                        $('#category').removeClass('is-invalid');
                        $('.errorCategory').html('');

                        $('#supplier').removeClass('is-invalid');
                        $('.errorSupplier').html('');

                        $('#id').val(response.id);
                        $('#name').val(response.name);
                        $('#category').val(response.category_id);
                        $('#supplier').val(response.supplier_id);
                    }
                });
            })

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('item.store') }}",
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
                            if (response.errors.name) {
                                $('#name').addClass('is-invalid');
                                $('.errorName').html(response.errors.name.join(
                                    '<br>'));
                            } else {
                                $('#name').removeClass('is-invalid');
                                $('.errorName').html('');
                            }

                            if (response.errors.category) {
                                $('#category').addClass('is-invalid');
                                $('.errorCategory').html(response.errors.category.join(
                                    '<br>'));
                            } else {
                                $('#category').removeClass('is-invalid');
                                $('.errorCategory').html('');
                            }

                            if (response.errors.supplier) {
                                $('#supplier').addClass('is-invalid');
                                $('.errorSupplier').html(response.errors.supplier.join(
                                    '<br>'));
                            } else {
                                $('#supplier').removeClass('is-invalid');
                                $('.errorSupplier').html('');
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
                            url: "{{ url('barang/" + id + "') }}",
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
