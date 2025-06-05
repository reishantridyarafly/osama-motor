<header class="topbar">
    <div class="with-vertical">
        <!-- Start Vertical Layout Header -->
        <nav class="navbar navbar-expand-lg p-0">
            <ul class="navbar-nav">
                <li class="nav-item nav-icon-hover-bg rounded-circle ms-n2">
                    <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="javascript:void(0)"
                        class="nav-link nav-icon-hover-bg rounded-circle mx-0 ms-n1 d-flex d-lg-none align-items-center justify-content-center"
                        type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar"
                        aria-controls="offcanvasWithBothOptions">
                        <i class="ti ti-align-justified fs-7"></i>
                    </a>
                    <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                        <!-- start notification Dropdown -->
                        @if (auth()->user()->role == 'warehouse')
                            <li class="nav-item nav-icon-hover-bg rounded-circle dropdown">
                                <a class="nav-link position-relative" href="javascript:void(0)"
                                    id="notificationDropdown" aria-expanded="false" data-bs-toggle="dropdown">
                                    <i class="ti ti-bell-ringing"></i>
                                    <div class="notification bg-primary rounded-circle" id="notificationCountBadge"
                                        style="display: none;"></div>
                                </a>
                                <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                    <div class="d-flex align-items-center justify-content-between py-3 px-7">
                                        <h5 class="mb-0 fs-5 fw-semibold">Notifikasi</h5>
                                        <span class="badge text-bg-primary rounded-4 px-3 py-1 lh-sm"
                                            id="totalNotificationCount">0 Baru</span>
                                    </div>
                                    <div class="message-body" data-simplebar id="notificationList">
                                        <a href="javascript:void(0)"
                                            class="py-6 px-7 d-flex align-items-center dropdown-item">
                                            <div class="w-100 text-center text-muted" id="noNotificationsMessage">Tidak
                                                ada notifikasi stok saat ini.</div>
                                        </a>
                                    </div>
                                    <div class="py-6 px-7 mb-1">
                                        <a href="{{ route('dashboard.index') }}"
                                            class="btn btn-outline-primary w-100">Lihat Semua Notifikasi Stok</a>
                                    </div>
                                </div>
                            </li>
                        @endif
                        <!-- end notification Dropdown -->

                        <!-- start profile Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <div class="user-profile-img">
                                        <img src="{{ asset('storage/users-avatar/' . auth()->user()->avatar) }}"
                                            class="rounded-circle" width="35" height="35" alt="modernize-img" />
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                aria-labelledby="drop1">
                                <div class="profile-dropdown position-relative" data-simplebar>
                                    <div class="py-3 px-7 pb-0">
                                        <h5 class="mb-0 fs-5 fw-semibold">Profile Pengguna</h5>
                                    </div>
                                    <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                                        <img src="{{ asset('storage/users-avatar/' . auth()->user()->avatar) }}"
                                            class="rounded-circle" width="80" height="80" alt="modernize-img" />
                                        <div class="ms-3">
                                            <h5 class="mb-1 fs-3">
                                                {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}</h5>
                                            <span class="mb-1 d-block text-truncate">
                                                @if (auth()->user()->role == 'owner')
                                                    Pemilik
                                                @elseif (auth()->user()->role == 'warehouse')
                                                    Gudang
                                                @elseif (auth()->user()->role == 'supplier')
                                                    Supplier
                                                @endif
                                            </span>
                                            <p class="mb-0 d-flex align-items-center gap-2 text-truncate">
                                                <i class="ti ti-mail fs-4"></i>{{ auth()->user()->email }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="message-body">
                                        <a href="{{ route('profile.index') }}"
                                            class="py-8 px-7 mt-8 d-flex align-items-center">
                                            <span
                                                class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                                                <img src="{{ asset('assets') }}/images/svgs/icon-account.svg"
                                                    alt="modernize-img" width="24" height="24" />
                                            </span>
                                            <div class="w-100 ps-3">
                                                <h6 class="mb-1 fs-3 fw-semibold lh-base">Profile Saya</h6>
                                                <span class="fs-2 d-block text-body-secondary">Pengaturan Akun</span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="d-grid py-4 px-7 pt-8">
                                        <a href="javascript:void(0);"
                                            class="btn btn-outline-primary logout-link">Keluar</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!-- end profile Dropdown -->
                    </ul>
                </div>
            </div>
        </nav>
        <!-- End Vertical Layout Header -->
    </div>
</header>

<script>
    $(document).ready(function() {
        function fetchNotifications() {
            if ("{{ auth()->user()->role }}" !== "warehouse") {
                return;
            }

            $.ajax({
                url: "{{ route('safetyStock.notifications') }}",
                method: 'GET',
                success: function(response) {
                    const notificationList = $('#notificationList');
                    const notificationCountBadge = $('#notificationCountBadge');
                    const totalNotificationCount = $('#totalNotificationCount');

                    notificationList.empty();
                    const notificationCount = response.length;

                    if (notificationCount > 0) {
                        notificationCountBadge.text('').show();
                        totalNotificationCount.text(`${notificationCount} baru`);
                        $('#noNotificationsMessage').remove();

                        response.forEach(function(notif) {
                            let badgeColor = 'bg-primary';
                            if (notif.stock_status === 'danger') {
                                badgeColor = 'bg-danger';
                            } else if (notif.stock_status === 'warning') {
                                badgeColor = 'bg-warning';
                            }

                            const notificationItem = `
                                <a href="{{ route('dashboard.index') }}#item-${notif.item_id}" class="py-6 px-7 d-flex align-items-center dropdown-item">
                                    <span class="me-3">
                                        <span class="badge ${badgeColor} rounded-circle p-2">
                                            <i class="ti ti-box"></i>
                                        </span>
                                    </span>
                                    <div class="w-100" style="max-width: 200px;">
                                        <h6 class="mb-1 fw-semibold lh-base text-truncate">${notif.item_name}</h6>
                                        <span class="fs-2 d-block text-body-secondary text-truncate">${notif.message}</span>
                                    </div>
                                </a>
                            `;
                            notificationList.append(notificationItem);
                        });
                    } else {
                        notificationCountBadge.hide();
                        totalNotificationCount.text('0 baru');
                        notificationList.append(`
                            <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                                <div class="w-100 text-center text-muted" id="noNotificationsMessage">Tidak ada notifikasi stok saat ini.</div>
                            </a>
                        `);
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching notifications:', xhr);
                }
            });
        }

        fetchNotifications();
        setInterval(fetchNotifications, 60000);
    });
</script>
