@extends('layouts.backend.main')
@section('title', 'Dashboard')
@section('content')
    <div class="body-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-lg-4 d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-body">
                            <h4 class="card-title fw-semibold">Weekly Stats</h4>
                            <p class="card-subtitle mb-0">Average sales</p>
                            <div id="weekly-stats" class="mb-4 mt-7"></div>
                            <div class="position-relative">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex">
                                        <div
                                            class="p-6 bg-primary-subtle text-primary rounded-2 me-6 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-grid-dots fs-6"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fs-4 fw-semibold">Top Sales</h6>
                                            <p class="fs-3 mb-0">Johnathan Doe</p>
                                        </div>
                                    </div>
                                    <div class="bg-primary-subtle text-primary badge">
                                        <p class="fs-3 fw-semibold mb-0">+68</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex">
                                        <div
                                            class="p-6 bg-success-subtle text-success rounded-2 me-6 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-grid-dots fs-6"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fs-4 fw-semibold">Best Seller</h6>
                                            <p class="fs-3 mb-0">Footware</p>
                                        </div>
                                    </div>
                                    <div class="bg-success-subtle text-success badge">
                                        <p class="fs-3 fw-semibold mb-0">+68</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex">
                                        <div
                                            class="p-6 bg-danger-subtle text-danger rounded-2 me-6 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-grid-dots fs-6"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fs-4 fw-semibold">Most Commented</h6>
                                            <p class="fs-3 mb-0">Fashionware</p>
                                        </div>
                                    </div>
                                    <div class="bg-danger-subtle text-danger badge">
                                        <p class="fs-3 fw-semibold mb-0">+68</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-body">
                            <div>
                                <h4 class="card-title fw-semibold">Yearly Sales</h4>
                                <p class="card-subtitle">Every month</p>
                                <div id="salary" class="mb-7 pb-8 mx-n4"></div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="bg-primary-subtle text-primary rounded-2 me-8 p-8 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-grid-dots fs-6"></i>
                                        </div>
                                        <div>
                                            <p class="fs-3 mb-0 fw-normal">Total Sales</p>
                                            <h6 class="fw-semibold text-dark fs-4 mb-0">$36,358</h6>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="bg-light-subtle text-muted rounded-2 me-8 p-8 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-grid-dots fs-6"></i>
                                        </div>
                                        <div>
                                            <p class="fs-3 mb-0 fw-normal">Expenses</p>
                                            <h6 class="fw-semibold text-dark fs-4 mb-0">$5,296</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-body">
                            <h4 class="card-title fw-semibold">Payment Gateways</h4>
                            <p class="card-subtitle mb-7">Platform for Income</p>
                            <div class="position-relative">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex">
                                        <div
                                            class="p-8 bg-primary-subtle rounded-2 d-flex align-items-center justify-content-center me-6">
                                            <img src="{{ asset('assets') }}/images/svgs/icon-paypal2.svg"
                                                alt="modernize-img" class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fs-4 fw-semibold">PayPal</h6>
                                            <p class="fs-3 mb-0">Big Brands</p>
                                        </div>
                                    </div>
                                    <h6 class="mb-0 fw-semibold">+$6,235</h6>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex">
                                        <div
                                            class="p-8 bg-success-subtle rounded-2 d-flex align-items-center justify-content-center me-6">
                                            <img src="{{ asset('assets') }}/images/svgs/icon-wallet.svg"
                                                alt="modernize-img" class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fs-4 fw-semibold">Wallet</h6>
                                            <p class="fs-3 mb-0">Bill payment</p>
                                        </div>
                                    </div>
                                    <h6 class="mb-0 fw-semibold text-muted">+$345</h6>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="d-flex">
                                        <div
                                            class="p-8 bg-warning-subtle rounded-2 d-flex align-items-center justify-content-center me-6">
                                            <img src="{{ asset('assets') }}/images/svgs/icon-credit-card.svg"
                                                alt="modernize-img" class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fs-4 fw-semibold">Credit card</h6>
                                            <p class="fs-3 mb-0">Money reversed</p>
                                        </div>
                                    </div>
                                    <h6 class="mb-0 fw-semibold">+$2,235</h6>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-7 pb-1">
                                    <div class="d-flex">
                                        <div
                                            class="p-8 bg-danger-subtle rounded-2 d-flex align-items-center justify-content-center me-6">
                                            <img src="{{ asset('assets') }}/images/svgs/icon-pie2.svg" alt="modernize-img"
                                                class="img-fluid" width="24" height="24">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fs-4 fw-semibold">Refund</h6>
                                            <p class="fs-3 mb-0">Bill payment</p>
                                        </div>
                                    </div>
                                    <h6 class="mb-0 fw-semibold text-muted">-$32</h6>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary w-100">View all transactions</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
