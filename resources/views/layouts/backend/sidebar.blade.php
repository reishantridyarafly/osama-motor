<aside class="left-sidebar with-vertical">
    <div><!-- ---------------------------------- -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ---------------------------------- -->
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="../main/index.html" class="text-nowrap logo-img">
                <img src="{{ asset('assets') }}/images/logos/dark-logo.svg" class="dark-logo" alt="Logo-Dark" />
                <img src="{{ asset('assets') }}/images/logos/light-logo.svg" class="light-logo" alt="Logo-light" />
            </a>
            <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
                <i class="ti ti-x"></i>
            </a>
        </div>

        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
            <ul id="sidebarnav">
                <!-- ---------------------------------- -->
                <!-- Home -->
                <!-- ---------------------------------- -->
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Navigasi</span>
                </li>
                <!-- ---------------------------------- -->
                <!-- Dashboard -->
                <!-- ---------------------------------- -->
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs(['dashboard.*']) ? 'active' : '' }}"
                        href="{{ route('dashboard.index') }}">
                        <span>
                            <i class="ti ti-dashboard"></i>
                        </span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs(['category.*']) ? 'active' : '' }}"
                        href="{{ route('category.index') }}">
                        <span>
                            <i class="ti ti-category"></i>
                        </span>
                        <span class="hide-menu">Kategori</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs(['item.*']) ? 'active' : '' }}"
                        href="{{ route('item.index') }}">
                        <span>
                            <i class="ti ti-box"></i>
                        </span>
                        <span class="hide-menu">Barang</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs(['supplier.*']) ? 'active' : '' }}"
                        href="{{ route('supplier.index') }}">
                        <span>
                            <i class="ti ti-building-store"></i>
                        </span>
                        <span class="hide-menu">Supplier</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs(['stockIn.*']) ? 'active' : '' }}"
                        href="{{ route('stockIn.index') }}">
                        <span>
                            <i class="ti ti-truck-delivery"></i>
                        </span>
                        <span class="hide-menu">Barang Masuk</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs(['stockOut.*']) ? 'active' : '' }}"
                        href="{{ route('stockOut.index') }}">
                        <span>
                            <i class="ti ti-truck-return"></i>
                        </span>
                        <span class="hide-menu">Barang Keluar</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs(['report.*']) ? 'active' : '' }}"
                        href="{{ route('report.index') }}">
                        <span>
                            <i class="ti ti-clipboard-text"></i>
                        </span>
                        <span class="hide-menu">Laporan</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs(['profile.*']) ? 'active' : '' }}"
                        href="{{ route('profile.index') }}">
                        <span>
                            <i class="ti ti-user"></i>
                        </span>
                        <span class="hide-menu">Profile</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="fixed-profile p-3 mx-4 mb-2 bg-secondary-subtle rounded mt-3">
            <div class="hstack gap-3">
                <div class="john-img">
                    <img src="{{ asset('storage/users-avatar/' . auth()->user()->avatar) }}" class="rounded-circle"
                        width="40" height="40" alt="modernize-img" />
                </div>
                <div class="john-title">
                    <h6 class="mb-0 fs-4 fw-semibold">{{ auth()->user()->first_name }}</h6>
                    <span class="fs-2">
                        @if (auth()->user()->role == 'owner')
                            Pemilik
                        @elseif (auth()->user()->role == 'warehouse')
                            Gudang
                        @elseif (auth()->user()->role == 'supplier')
                            Supplier
                        @endif
                    </span>
                </div>
                <button class="border-0 bg-transparent text-primary ms-auto logout-link" type="button">
                    <i class="ti ti-power fs-6"></i>
                </button>
            </div>
        </div>

        <!-- ---------------------------------- -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ---------------------------------- -->
    </div>
</aside>
