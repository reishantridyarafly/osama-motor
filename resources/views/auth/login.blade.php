@extends('layouts.auth.main')
@section('title', 'Masuk')
@section('content')
    <div class="col-md-8 col-lg-6 col-xxl-3 auth-card">
        <div class="card mb-0">
            <div class="card-body">
                <a href="../main/index.html" class="text-nowrap logo-img text-center d-block mb-5 w-100">
                    <img src="{{ asset('assets') }}/images/logos/dark-logo.svg" class="dark-logo" alt="Logo-Dark" />
                    <img src="{{ asset('assets') }}/images/logos/light-logo.svg" class="light-logo" alt="Logo-light" />
                </a>
                <form id="form">
                    <div class="mb-3">
                        <label for="username" class="form-label">Email / No Telepon <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username">
                        <small class="text-danger errorUsername mt-2"></small>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Kata Sandi <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="text-danger errorPassword mt-2"></small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="form-check">
                            <input class="form-check-input primary" type="checkbox" id="remember_me" name="remember_me">
                            <label class="form-check-label text-dark" for="remember_me">
                                Ingat Saya
                            </label>
                        </div>
                    </div>
                    <button type="submit" id="login" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Masuk</button>
                </form>
            </div>
        </div>
    </div>



@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#username').on('input', function() {
                $(this).removeClass('is-invalid');
                $('.errorUsername').html('');
            });

            $('#password').on('input', function() {
                $(this).removeClass('is-invalid');
                $('.errorPassword').html('');
            });

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('login') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#login').attr('disabled', 'disabled');
                        $('#login').text('Proses...');
                    },
                    complete: function() {
                        $('#login').removeAttr('disabled');
                        $('#login').text('Login');
                    },
                    success: function(response) {
                        if (response.errors) {
                            if (response.errors.username) {
                                $('#username').addClass('is-invalid');
                                $('.errorUsername').html(response.errors.username.join(
                                    '<br>'));
                            } else {
                                $('#username').removeClass('is-invalid');
                                $('.errorUsername').html('');
                            }

                            if (response.errors.password) {
                                $('#password').addClass('is-invalid');
                                $('.errorPassword').html(response.errors.password.join(
                                    '<br>'));
                            } else {
                                $('#password').removeClass('is-invalid');
                                $('.errorPassword').html('');
                            }
                        } else if (response.NoUsername || response.NonActiveUsername || response
                            .WrongPassword) {
                            let errorMessage = '';
                            if (response.NoUsername) {
                                errorMessage = response.NoUsername.message;
                            } else if (response.NonActiveUsername) {
                                errorMessage = response.NonActiveUsername.message;
                            } else if (response.WrongPassword) {
                                errorMessage = response.WrongPassword.message;
                            }

                            toastr.error(errorMessage, "Validasi gagal", {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 2000
                            });

                            if (response.NoUsername || response.NonActiveUsername) {
                                $('#username').val('');
                            }
                            if (response.WrongPassword || response.NoUsername || response
                                .NonActiveUsername) {
                                $('#password').val('');
                            }
                        } else {
                            window.location.href = response.redirect;
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
        })
    </script>
@endsection
