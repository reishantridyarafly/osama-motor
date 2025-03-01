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
                                        <th>Harga Jual</th>
                                        <th>Kategori</th>
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
                            <label for="price" class="form-label">Harga Jual<span class="text-danger">*</span></label>
                            <input type="text" id="price" name="price" class="form-control">
                            <small class="text-danger errorPrice"></small>
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
        new AutoNumeric('#price', {
            currencySymbol: 'Rp ',
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            decimalPlaces: 0,
        });

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
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'category',
                        name: 'category'
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

                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
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

                        $('#price').removeClass('is-invalid');
                        $('.errorPrice').html('');

                        $('#category').removeClass('is-invalid');
                        $('.errorCategory').html('');

                        $('#id').val(response.id);
                        $('#name').val(response.name);

                        if (AutoNumeric.getAutoNumericElement('#price')) {
                            AutoNumeric.getAutoNumericElement('#price').remove();
                        }

                        new AutoNumeric('#price', response.price, {
                            currencySymbol: 'Rp ',
                            decimalCharacter: ',',
                            digitGroupSeparator: '.',
                            decimalPlaces: 0
                        });

                        $('#category').val(response.category_id);
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

                            if (response.errors.price) {
                                $('#price').addClass('is-invalid');
                                $('.errorPrice').html(response.errors.price.join(
                                    '<br>'));
                            } else {
                                $('#price').removeClass('is-invalid');
                                $('.errorPrice').html('');
                            }

                            if (response.errors.category) {
                                $('#category').addClass('is-invalid');
                                $('.errorCategory').html(response.errors.category.join(
                                    '<br>'));
                            } else {
                                $('#category').removeClass('is-invalid');
                                $('.errorCategory').html('');
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
