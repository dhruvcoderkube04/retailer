<!DOCTYPE html>

<html lang="en">
<!--begin::Head-->

<head>
    <title>@yield('title')</title>
    <meta charset="utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="#" />
    <meta property="og:url" content="#" />
    <meta property="og:site_name" content="#" />
    <link rel="canonical" href="#" />
    {{-- <link rel="shortcut icon" href="#" /> --}}
    <link rel="icon" href="{{ asset('assets/media/favicon/android-icon-36x36.png') }}" type="image/png" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/custom/vis-timeline/vis-timeline.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @yield('styles')
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
    data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default" data-kt-app-sidebar-minimize="on">
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <!--end::Theme mode setup on page load-->
    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!--begin::Header-->
            @include('layouts.header')
            <!--end::Header-->
            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <!--begin::Sidebar-->
                @include('layouts.sidebar')
                <!--end::Sidebar-->
                <!--begin::Main-->
                @yield('content')
                <!--end:::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->

    <!--begin::Scrolltop-->
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <i class="ki-duotone ki-arrow-up">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    </div>
    <!--end::Scrolltop-->

    <!--begin::Javascript-->
    <script>
        var hostUrl = "assets/";
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <!--end::Global Javascript Bundle-->
    <!--end::Javascript-->

    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    {{-- <script>
        $(document).ready(function() {
            $('#kt_menu_item_wow').on('click', function () {

                const container = $('#notification-list');
                container.html('<div class="text-muted text-center py-10">Loading...</div>');

                $.ajax({
                    url: "{{ route('notifications.get') }}",
                    method: 'GET',
                    success: function (res) {
                        console.log(res);
                        container.html('');
                        const count = res.notifications.length;

                        $('#notification-count').text(count);
                        if (count === 0) {
                            $('#notification-count').hide();
                            container.html('<div class="text-muted text-center py-10">No notifications found</div>');
                            return;
                        } else {
                            $('#notification-count').show();
                        }

                        res.notifications.forEach(item => {
                            container.append(`
                                <div class="d-flex flex-stack py-4 border-bottom">
                                    <div class="d-flex align-items-center me-2">
                                        <span class="w-70px badge badge-light-success me-4">200 OK</span>
                                        <a href="#" class="text-gray-800 text-hover-primary fw-semibold">${item.message}</a>
                                    </div>
                                    <span class="badge badge-light fs-8">${new Date(item.created_at).toLocaleString()}</span>
                                </div>
                            `);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                        container.html('<div class="text-danger text-center py-10">Failed to load notifications</div>');
                    }
                });
            });
        });
    </script> --}}

    <script>
        $(document).ready(function() {
            // 1) On page-load: fetch unread count and update badge
            $.ajax({
                url: "{{ route('notifications.count') }}",    // your /notifications-count route
                method: 'GET',
                success: function(res) {
                    const c = res.notifications_count;        // note the JSON key
                    if (c > 0) {
                        $('#notification-count')
                        .text(c)
                        .show();
                    } else {
                        $('#notification-count').hide();
                    }
                },
                error: function(xhr, status, err) {
                    console.error("Count fetch error:", err);
                }
            });

            // 2) On bell-icon click: load latest 5, mark read in controller, then update list & badge
            $('#kt_menu_item_wow').on('click', function () {
                const container = $('#notification-list');
                container.html('<div class="text-muted text-center py-10">Loading...</div>');

                $.ajax({
                    url: "{{ route('notifications.get') }}",
                    method: 'GET',
                    success: function (res) {
                        container.empty();
                        const list = res.notifications;
                        $('#notification-count').text(list.length);
                        if (list.length === 0) {
                            $('#notification-count').hide();
                            container.html('<div class="text-muted text-center py-10">No notifications found</div>');
                            return;
                        }
                        $('#notification-count').show();

                        list.forEach(item => {
                            container.append(`
                                <div class="d-flex flex-stack py-4 border-bottom">
                                    <div class="d-flex align-items-center me-2">
                                        <a href="#" class="text-gray-800 text-hover-primary fw-semibold">
                                        ${item.message} <span class="badge badge-primary">${item.order_id}<span>
                                        </a>
                                    </div>
                                    <span class="badge badge-light fs-8">${item.time_ago}</span>
                                </div>
                            `);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                        $('#notification-list').html('<div class="text-danger text-center py-10">Failed to load notifications</div>');
                    }
                });
            });
        });
    </script>

    @yield('script')
</body>
<!--end::Body-->

</html>
