@extends('layouts.backend.main')
@section('title', 'Profile')
@section('content')
    <style>
        #profileImage {
            border-radius: 50%;
            object-fit: cover;
            width: 120px;
            height: 120px;
        }
    </style>

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
                                <img src="../assets/images/breadcrumb/ChatBc.png" alt="modernize-img"
                                    class="img-fluid mb-n4" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 d-flex align-items-stretch">
                            <div class="card w-100 border position-relative overflow-hidden">
                                <form id="formChangeImageProfile" enctype="multipart/form-data">
                                    <div class="card-body p-4">
                                        <h4 class="card-title">Ubah Profile</h4>
                                        <p class="card-subtitle mb-4">Ubah gambar profil Anda dari sini</p>
                                        <div class="text-center">
                                            <img id="profileImage"
                                                src="{{ asset('storage/users-avatar/' . auth()->user()->avatar) }}"
                                                alt="modernize-img" class="img-fluid rounded-circle" width="120"
                                                height="120">
                                            <div class="d-flex align-items-center justify-content-center gap-6">
                                                <small class="text-danger errorAvatar mt-2"></small>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center my-4 gap-6">
                                                <button type="button" class="btn btn-primary"
                                                    id="btnChangeImage">Ubah</button>
                                                <button type="button" class="btn bg-danger-subtle text-danger"
                                                    id="btnDeleteImage">Hapus</button>
                                            </div>
                                            <p class="mb-0">Diizinkan JPG, GIF atau PNG. Ukuran maksimal 800 KB
                                            </p>
                                            <input type="file" id="avatar" name="avatar" style="display: none;"
                                                accept="image/*">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-6 d-flex align-items-stretch">
                            <div class="card w-100 border position-relative overflow-hidden">
                                <div class="card-body p-4">
                                    <h4 class="card-title">Ubah Kata Sandi</h4>
                                    <p class="card-subtitle mb-4">Untuk mengubah kata sandi Anda, silakan konfirmasi di sini
                                    </p>
                                    <form id="formPassword">
                                        <div class="mb-3">
                                            <label for="old_password" class="form-label">Kata sandi saat ini</label>
                                            <input type="password" class="form-control" id="old_password"
                                                name="old_password">
                                            <small class="text-danger errorOldPassword mt-2"></small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Kata sandi baru</label>
                                            <input type="password" class="form-control" id="password" name="password">
                                            <small class="text-danger errorPassword mt-2"></small>
                                        </div>
                                        <div>
                                            <label for="password_confirmation" class="form-label">Konfirmasi kata
                                                sandi</label>
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation">
                                            <small class="text-danger errorConfirmPassword mt-2"></small>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center justify-content-end mt-4 gap-6">
                                                <button type="submit" id="btnPassword" class="btn btn-primary">Ubah
                                                    Kata Sandi</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card w-100 border position-relative overflow-hidden mb-0">
                                <div class="card-body p-4">
                                    <h4 class="card-title">Detail Pribadi</h4>
                                    <p class="card-subtitle mb-4">Untuk mengubah detail pribadi Anda, edit dan
                                        simpan dari sini</p>
                                    <form id="formBiodata">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="first_name" class="form-label">Nama Depan <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="first_name"
                                                        name="first_name" value="{{ auth()->user()->first_name }}">
                                                    <small class="text-danger errorFirstName mt-2"></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="last_name" class="form-label">Nama Belakang</label>
                                                    <input type="text" class="form-control" id="last_name"
                                                        name="last_name" value="{{ auth()->user()->last_name }}">
                                                    <small class="text-danger errorLastName mt-2"></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email <span
                                                            class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="email"
                                                        name="email" value="{{ auth()->user()->email }}">
                                                    <small class="text-danger errorEmail mt-2"></small>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="telephone" class="form-label">No Telepon <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="telephone"
                                                        name="telephone" value="{{ auth()->user()->telephone }}">
                                                    <small class="text-danger errorTelephone mt-2"></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center justify-content-end mt-4 gap-6">
                                                <button type="submit" id="btnBiodata"
                                                    class="btn btn-primary">Simpan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#btnChangeImage').on('click', function() {
                $('#avatar').click();
            });

            $('#avatar').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profileImage').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);

                    const formData = new FormData($('#formChangeImageProfile')[0]);
                    $.ajax({
                        url: "{{ route('profile.updateAvatar') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        beforeSend: function() {
                            $('#btnChangeImage').attr('disabled', 'disabled');
                            $('#btnChangeImage').text('Proses...');
                        },
                        complete: function() {
                            $('#btnChangeImage').removeAttr('disabled');
                            $('#btnChangeImage').text('Ubah');
                        },
                        success: function(response) {
                            if (response.errors) {
                                if (response.errors.avatar) {
                                    $('#avatar').addClass('is-invalid');
                                    $('.errorAvatar').html(response.errors.avatar.join(
                                        '<br>'));
                                } else {
                                    $('#avatar').removeClass('is-invalid');
                                    $('.errorAvatar').html('');
                                }
                            } else {
                                toastr.success(response.message, 'Sukses', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 2000,
                                    onHidden: function() {
                                        location.reload();
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    toastr.error(value.join('<br>'),
                                        'Kesalahan Validasi', {
                                            closeButton: true,
                                            progressBar: true,
                                            timeOut: 2000
                                        });
                                });
                            } else {
                                toastr.error('Terjadi kesalahan, silakan coba lagi.',
                                    'Kesalahan', {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 2000
                                    });
                            }
                        }
                    });
                }
            });

            $('body').on('click', '#btnDeleteImage', function() {
                Swal.fire({
                    title: 'Hapus Profile',
                    text: "Apakah kamu yakin?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('profile.deleteAvatar') }}",
                            type: 'DELETE',
                            beforeSend: function() {
                                $('#btnDeleteImage').attr('disabled', 'disabled');
                                $('#btnDeleteImage').text('Proses...');
                            },
                            complete: function() {
                                $('#btnDeleteImage').removeAttr('disabled');
                                $('#btnDeleteImage').text('Hapus');
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    $('#profileImage').attr('src',
                                        "{{ asset('storage/users-avatar/avatar.png') }}"
                                    );
                                    toastr.success(response.message, 'Sukses', {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 2000,
                                        onHidden: function() {
                                            location.reload();
                                        }
                                    });
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
                    }
                });
            })

            $('#formPassword').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('profile.changePassword') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnPassword').attr('disabled', 'disabled');
                        $('#btnPassword').text('Proses...');
                    },
                    complete: function() {
                        $('#btnPassword').removeAttr('disabled');
                        $('#btnPassword').text('Ubah Kata Sandi');
                    },
                    success: function(response) {
                        if (response.errors) {
                            if (response.errors.old_password) {
                                $('#old_password').addClass('is-invalid');
                                $('.errorOldPassword').html(response.errors.old_password.join(
                                    '<br>'));
                            } else {
                                $('#old_password').removeClass('is-invalid');
                                $('.errorOldPassword').html('');
                            }

                            if (response.errors.password) {
                                $('#password').addClass('is-invalid');
                                $('.errorPassword').html(response.errors.password.join('<br>'));
                            } else {
                                $('#password').removeClass('is-invalid');
                                $('.errorPassword').html('');
                            }

                            if (response.errors.password_confirmation) {
                                $('#password_confirmation').addClass('is-invalid');
                                $('.errorConfirmPassword').html(response.errors
                                    .password_confirmation.join('<br>'));
                            } else {
                                $('#password_confirmation').removeClass('is-invalid');
                                $('.errorConfirmPassword').html('');
                            }
                        } else {
                            if (response.error_password) {
                                toastr.error(response.message, 'Validasi gagal', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 2000
                                });
                            } else {
                                toastr.success(response.message, 'Sukses', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 2000,
                                    onHidden: function() {
                                        location.reload();
                                    }
                                });


                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                toastr.error(value.join('<br>'), 'Kesalahan Validasi', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 2000
                                });
                            });
                        } else {
                            toastr.error('Terjadi kesalahan, silakan coba lagi.', 'Kesalahan', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 2000
                            });
                        }
                    }
                });
            });

            $('#formBiodata').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('profile.changeBiodata') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnBiodata').attr('disabled', 'disabled');
                        $('#btnBiodata').text('Proses...');
                    },
                    complete: function() {
                        $('#btnBiodata').removeAttr('disabled');
                        $('#btnBiodata').text('Simpan');
                    },
                    success: function(response) {
                        if (response.errors) {
                            if (response.errors.first_name) {
                                $('#first_name').addClass('is-invalid');
                                $('.errorFirstName').html(response.errors.first_name.join(
                                    '<br>'));
                            } else {
                                $('#first_name').removeClass('is-invalid');
                                $('.errorFirstName').html('');
                            }

                            if (response.errors.email) {
                                $('#email').addClass('is-invalid');
                                $('.errorEmail').html(response.errors.email.join(
                                    '<br>'));
                            } else {
                                $('#email').removeClass('is-invalid');
                                $('.errorEmail').html('');
                            }

                            if (response.errors.telephone) {
                                $('#telephone').addClass('is-invalid');
                                $('.errorTelephone').html(response.errors.telephone.join(
                                    '<br>'));
                            } else {
                                $('#telephone').removeClass('is-invalid');
                                $('.errorTelephone').html('');
                            }
                        } else {
                            toastr.success(response.message, 'Sukses', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 2000,
                                onHidden: function() {
                                    location.reload();
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                toastr.error(value.join('<br>'), 'Kesalahan Validasi', {
                                    closeButton: true,
                                    progressBar: true,
                                    timeOut: 2000
                                });
                            });
                        } else {
                            toastr.error('Terjadi kesalahan, silakan coba lagi.', 'Kesalahan', {
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
