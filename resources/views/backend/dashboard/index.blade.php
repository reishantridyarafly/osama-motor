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
                                            <th>Item</th>
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
                                                <td>{{ number_format($item['current_stock']) }}</td>
                                                <td>{{ number_format($item['average_daily_demand'], 2) }}</td>
                                                <td>{{ number_format($item['safety_stock']) }}</td>
                                                <td>{{ number_format($item['reorder_point']) }}</td>
                                                <td>
                                                    @if ($item['stock_status'] === 'danger')
                                                        <span class="badge rounded-pill text-bg-danger">Stok Dibawah Safety
                                                            Stock!</span>
                                                    @elseif($item['stock_status'] === 'warning')
                                                        <span class="badge rounded-pill text-bg-warning">Perlu
                                                            Reorder!</span>
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
        </div>
    </div>
@endsection
