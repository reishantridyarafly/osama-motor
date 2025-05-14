@extends('layouts.backend.main')
@section('title', 'Dashboard')
@section('content')
    <div class="body-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card bg-info-subtle overflow-hidden shadow-none">
                        <div class="card-body py-3">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-sm-6">
                                    <h5 class="fw-semibold mb-9 fs-5">Selamat Datang
                                        <strong>{{ auth()->user()->first_name }}</strong> di Osama Motor!
                                    </h5>
                                    <p class="mb-9">
                                        Kelola inventori dan monitoring stok dengan mudah.
                                        Mulai sekarang untuk pengalaman manajemen bengkel yang lebih optimal!
                                    </p>
                                </div>
                                <div class="col-sm-5 mt-3">
                                    <div class="position-relative mb-n5 text-center">
                                        <img src="{{ asset('assets') }}/images/backgrounds/welcome-bg.svg"
                                            alt="modernize-img" class="img-fluid" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if (auth()->user()->role != 'supplier')
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Safety Stock Monitoring</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                    <table class="table table-bordered">
                                        <thead class="sticky-top bg-white">
                                            <tr>
                                                <th>Barang</th>
                                                <th>Supplier</th>
                                                <th>Stok Saat Ini</th>
                                                <th>Permintaan Harian</th>
                                                <th>Safety Stock</th>
                                                <th>Reorder Point</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $sortedData = collect($safetyStockData)->sortBy(function ($item) {
                                                    // Sort priority: danger (1), warning (2), safe (3)
                                                    if ($item['stock_status'] === 'danger') {
                                                        return 1;
                                                    }
                                                    if ($item['stock_status'] === 'warning') {
                                                        return 2;
                                                    }
                                                    return 3;
                                                });
                                            @endphp
                                            @foreach ($sortedData as $item)
                                                <tr>
                                                    <td>{{ $item['item_name'] }}</td>
                                                    <td>{{ $item['supplier_name'] }}</td>
                                                    <td>
                                                        {{ number_format($item['current_stock']) }}
                                                    </td>
                                                    <td>{{ number_format($item['average_daily_demand'], 2) }}</td>
                                                    <td>{{ number_format($item['safety_stock']) }}</td>
                                                    <td>{{ number_format($item['reorder_point']) }}</td>
                                                    <td>
                                                        @if ($item['stock_status'] === 'danger')
                                                            <a href="{{ route('stockIn.index') }}"
                                                                class="text-decoration-none"
                                                                style="transition: all 0.3s ease;"
                                                                onmouseover="this.style.opacity='0.8'; this.style.transform='scale(1.05)'"
                                                                onmouseout="this.style.opacity='1'; this.style.transform='scale(1)'">
                                                                <span class="badge rounded-pill text-bg-danger"
                                                                    style="display: inline-block;">Stok Dibawah
                                                                    Safety
                                                                    Stock!</span>
                                                            </a>
                                                        @elseif($item['stock_status'] === 'warning')
                                                            <a href="{{ route('stockIn.index') }}"
                                                                class="text-decoration-none"
                                                                style="transition: all 0.3s ease;"
                                                                onmouseover="this.style.opacity='0.8'; this.style.transform='scale(1.05)'"
                                                                onmouseout="this.style.opacity='1'; this.style.transform='scale(1)'">
                                                                <span class="badge rounded-pill text-bg-warning">Perlu
                                                                    Reorder!</span>
                                                            </a>
                                                        @else
                                                            <span class="badge rounded-pill text-bg-success">Aman</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1 text-white">Dibawah Safety Stock</h5>
                                        <h3 class="card-text mb-0 text-white">
                                            {{ collect($safetyStockData)->where('stock_status', 'danger')->count() }} Item
                                        </h3>
                                        <small>Memerlukan tindakan segera!</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-bell fa-3x"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1 text-white">Perlu Reorder</h5>
                                        <h3 class="card-text mb-0 text-white">
                                            {{ collect($safetyStockData)->where('stock_status', 'warning')->count() }} Item
                                        </h3>
                                        <small>Saatnya melakukan pemesanan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-check fa-3x"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1 text-white">Stok Aman</h5>
                                        <h3 class="card-text mb-0 text-white">
                                            {{ collect($safetyStockData)->where('stock_status', 'safe')->count() }} Item
                                        </h3>
                                        <small>Stok dalam kondisi ideal</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Panduan Status Stok</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="alert alert-danger">
                                            <h6 class="alert-heading">Dibawah Safety Stock</h6>
                                            <p class="mb-0">Segera lakukan pemesanan darurat</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-warning">
                                            <h6 class="alert-heading">Perlu Reorder</h6>
                                            <p class="mb-0">Siapkan pesanan baru</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-success">
                                            <h6 class="alert-heading">Stok Aman</h6>
                                            <p class="mb-0">Lakukan monitoring rutin</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (auth()->user()->role == 'supplier')
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Permintaan Barang</h3>
                            </div>
                            <div class="card-body">
                                @php
                                    $itemRequests = \App\Models\StockIn::with('item')
                                        ->where('status', 'request')
                                        ->where('supplier_id', auth()->id())
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                @endphp

                                @if ($itemRequests->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Barang</th>
                                                    <th>Jumlah</th>
                                                    <th>Harga</th>
                                                    <th>Tanggal Permintaan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($itemRequests as $key => $request)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $request->item->name ?? 'N/A' }}</td>
                                                        <td>{{ $request->quantity }}</td>
                                                        <td>Rp {{ number_format($request->item->price, 0, ',', '.') }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($request->date)->translatedFormat('d F Y') }}
                                                        </td>
                                                        <td>
                                                            @if ($request->status == 'request')
                                                                <span class="badge rounded-pill text-bg-warning">Menunggu
                                                                    Persetujuan</span>
                                                            @elseif ($request->status == 'accepted')
                                                                <span
                                                                    class="badge rounded-pill text-bg-success">Disetujui</span>
                                                            @else
                                                                <span
                                                                    class="badge rounded-pill text-bg-danger">Ditolak</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <a href="javascript:void(0)"
                                                                    class="approve-btn text-success me-1" id="btnAccepted"
                                                                    data-id="{{ $request->id }}" title="Setuju">
                                                                    <i class="fas fa-check fa-2x"></i>
                                                                </a>
                                                                <a href="javascript:void(0)"
                                                                    class="reject-btn text-danger" id="btnRejected"
                                                                    data-id="{{ $request->id }}" title="Tidak Setuju">
                                                                    <i class="fas fa-times fa-2x"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> Tidak ada permintaan barang saat ini.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-shopping-cart fa-3x"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1 text-white">Total Permintaan</h5>
                                        <h3 class="card-text mb-0 text-white">
                                            {{ $itemRequests->count() }} Item
                                        </h3>
                                        <small>Menunggu persetujuan Anda</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-money-bill-wave fa-3x"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1 text-white">Total Nilai</h5>
                                        <h3 class="card-text mb-0 text-white">
                                            Rp
                                            {{ number_format(
                                                $itemRequests->sum(function ($stockIn) {
                                                    return $stockIn->quantity * ($stockIn->item->price ?? 0);
                                                }),
                                                0,
                                                ',',
                                                '.',
                                            ) }}
                                        </h3>
                                        <small>Nilai permintaan barang</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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

            $('body').on('click', '#btnAccepted', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Terima',
                    text: "Apakah anda yakin?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, terima!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('item_request.updateStatus') }}",
                            data: {
                                id: id,
                                status: 'accepted'
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.message) {
                                    toastr.success(response.message, 'Sukses', {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 2000
                                    });
                                    location.reload();
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

            $('body').on('click', '#btnRejected', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Tolak',
                    text: "Apakah anda yakin?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, tolak!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('item_request.updateStatus') }}",
                            data: {
                                id: id,
                                status: 'rejected'
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.message) {
                                    toastr.success(response.message, 'Sukses', {
                                        closeButton: true,
                                        progressBar: true,
                                        timeOut: 2000
                                    });
                                    location.reload();
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
