<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="{{ csrf_token() }}" name="csrf-token">

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets') }}/images/logos/logo.png" />

    <!-- Core Css -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/styles.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/libs/sweetalert2/dist/sweetalert2.min.css">

    <title>@yield('title') || {{ config('app.name') }}</title>

    <script src="{{ asset('assets') }}/libs/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <img src="{{ asset('assets') }}/images/logos/logo.png" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <div id="main-wrapper">
        <!-- Sidebar Start -->
        @include('layouts.backend.sidebar')
        <!--  Sidebar End -->

        <div class="page-wrapper">
            <!--  Header Start -->
            @include('layouts.backend.topbar')
            <!--  Header End -->

            @yield('content')
        </div>
    </div>
    <div class="dark-transparent sidebartoggler"></div>
    <script src="{{ asset('assets') }}/js/vendor.min.js"></script>
    <!-- Import Js Files -->
    <script src="{{ asset('assets') }}/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets') }}/libs/simplebar/dist/simplebar.min.js"></script>
    <script src="{{ asset('assets') }}/js/theme/app.init.js"></script>
    <script src="{{ asset('assets') }}/js/theme/theme.js"></script>
    <script src="{{ asset('assets') }}/js/theme/app.min.js"></script>
    <script src="{{ asset('assets') }}/js/theme/sidebarmenu.js"></script>
    <script src="{{ asset('assets') }}/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/datatable/datatable-basic.init.js"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <script src="{{ asset('assets') }}/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="{{ asset('assets') }}/js/dashboards/dashboard2.js"></script>
    <script src="{{ asset('assets') }}/js/plugins/toastr-init.js"></script>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script>
        $(document).ready(function() {
            $('body').on('click', '.logout-link', function() {
                Swal.fire({
                    title: 'Keluar',
                    text: "Apakah kamu yakin?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, keluar!',
                    cancelButtonText: 'Batal',
                }).then((willLogout) => {
                    if (willLogout.value) {
                        logoutUser();
                    }
                });
            })

            function logoutUser() {
                $.ajax({
                    url: "{{ route('logout') }}",
                    type: 'POST',
                    data: $('#logout-form').serialize(),
                    success: function(response) {
                        window.location.href = "{{ route('login') }}";
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" +
                            thrownError);
                    }
                });
            }
        })
    </script>

    @yield('script')
</body>

</html>
