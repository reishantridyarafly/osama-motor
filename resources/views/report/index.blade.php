@extends('layouts.backend.main')
@section('title', 'Laporan')
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
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('report.print') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-md-12">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Tanggal Awal</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" required
                                        max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" required
                                        max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    {{-- <button class="btn btn-success me-2" name="action" value="excel" type="submit">
                                        <i class="ti ti-file-spreadsheet"></i> Cetak Excel
                                    </button> --}}
                                    <button class="btn btn-danger" name="action" value="pdf" type="submit">
                                        <i class="ti ti-file-text"></i> Cetak PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
