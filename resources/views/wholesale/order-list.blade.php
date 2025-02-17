@extends('wholesale.layouts.base')
@section('title')
    TrendMart| Order List
@endsection
@section('content')
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Orders Listing</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="index.html" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">eCommerce</li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Sales</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <!--begin::Filter menu-->
                        <div class="m-0">
                            <!--begin::Menu toggle-->
                            <a href="#" class="btn btn-sm btn-flex btn-secondary fw-bold" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            <i class="ki-duotone ki-filter fs-6 text-muted me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>Filter</a>
                            <!--end::Menu toggle-->
                            <!--begin::Menu 1-->
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_673c0cbe39ba6">
                                <!--begin::Header-->
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-gray-900 fw-bold">Filter Options</div>
                                </div>
                                <!--end::Header-->
                                <!--begin::Menu separator-->
                                <div class="separator border-gray-200"></div>
                                <!--end::Menu separator-->
                                <!--begin::Form-->
                                <div class="px-7 py-5">
                                    <!--begin::Input group-->
                                    <div class="mb-10">
                                        <!--begin::Label-->
                                        <label class="form-label fw-semibold">Status:</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <div>
                                            <select class="form-select form-select-solid" multiple="multiple" data-kt-select2="true" data-close-on-select="false" data-placeholder="Select option" data-dropdown-parent="#kt_menu_673c0cbe39ba6" data-allow-clear="true">
                                                <option></option>
                                                <option value="1">Approved</option>
                                                <option value="2">Pending</option>
                                                <option value="2">In Process</option>
                                                <option value="2">Rejected</option>
                                            </select>
                                        </div>
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="mb-10">
                                        <!--begin::Label-->
                                        <label class="form-label fw-semibold">Member Type:</label>
                                        <!--end::Label-->
                                        <!--begin::Options-->
                                        <div class="d-flex">
                                            <!--begin::Options-->
                                            <label class="form-check form-check-sm form-check-custom form-check-solid me-5">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                                <span class="form-check-label">Author</span>
                                            </label>
                                            <!--end::Options-->
                                            <!--begin::Options-->
                                            <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="2" checked="checked" />
                                                <span class="form-check-label">Customer</span>
                                            </label>
                                            <!--end::Options-->
                                        </div>
                                        <!--end::Options-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="mb-10">
                                        <!--begin::Label-->
                                        <label class="form-label fw-semibold">Notifications:</label>
                                        <!--end::Label-->
                                        <!--begin::Switch-->
                                        <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="" name="notifications" checked="checked" />
                                            <label class="form-check-label">Enabled</label>
                                        </div>
                                        <!--end::Switch-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Actions-->
                                    <div class="d-flex justify-content-end">
                                        <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Reset</button>
                                        <button type="submit" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true">Apply</button>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::Form-->
                            </div>
                            <!--end::Menu 1-->
                        </div>
                        <!--end::Filter menu-->
                        <!--begin::Secondary button-->
                        <!--end::Secondary button-->
                        <!--begin::Primary button-->
                        <a href="#" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_app">Create</a>
                        <!--end::Primary button-->
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Products-->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" data-kt-ecommerce-order-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Search Order" />
                                </div>
                                <!--end::Search-->
                            </div>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                <!--begin::Flatpickr-->
                                <div class="input-group w-250px">
                                    <input class="form-control form-control-solid rounded rounded-end-0" placeholder="Pick date range" id="kt_ecommerce_sales_flatpickr" />
                                    <button class="btn btn-icon btn-light" id="kt_ecommerce_sales_flatpickr_clear">
                                        <i class="ki-duotone ki-cross fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </button>
                                </div>
                                <!--end::Flatpickr-->
                                <div class="w-100 mw-150px">
                                    <!--begin::Select2-->
                                    <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-order-filter="status">
                                        <option></option>
                                        <option value="all">All</option>
                                        <option value="Cancelled">Cancelled</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Denied">Denied</option>
                                        <option value="Expired">Expired</option>
                                        <option value="Failed">Failed</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Processing">Processing</option>
                                        <option value="Refunded">Refunded</option>
                                        <option value="Delivered">Delivered</option>
                                        <option value="Delivering">Delivering</option>
                                    </select>
                                    <!--end::Select2-->
                                </div>
                                <!--begin::Add product-->
                                <a href="apps/ecommerce/catalog/add-product.html" class="btn btn-primary">Add Order</a>
                                <!--end::Add product-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_sales_table">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-start w-10px pe-2">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_ecommerce_sales_table .form-check-input" value="1" />
                                            </div>
                                        </th>
                                        <th class="min-w-100px">Order ID</th>
                                        <th class="min-w-175px">Customer</th>
                                        <th class="text-end min-w-70px">Status</th>
                                        <th class="text-end min-w-100px">Total</th>
                                        <th class="text-end min-w-100px">Date Added</th>
                                        <th class="text-end min-w-100px">Date Modified</th>
                                        <th class="text-end min-w-100px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12685</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-6.jpg" alt="Emma Smith" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Emma Smith</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$229.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-15">
                                            <span class="fw-bold">15/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-18">
                                            <span class="fw-bold">18/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12686</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-13.jpg" alt="John Miller" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">John Miller</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$297.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-15">
                                            <span class="fw-bold">15/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-17">
                                            <span class="fw-bold">17/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12687</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-13.jpg" alt="John Miller" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">John Miller</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$261.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-13">
                                            <span class="fw-bold">13/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-16">
                                            <span class="fw-bold">16/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12688</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-12.jpg" alt="Ana Crown" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Ana Crown</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Failed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Failed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$269.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-08">
                                            <span class="fw-bold">08/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-15">
                                            <span class="fw-bold">15/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12689</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-info text-info">A</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Robert Doe</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Refunded">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-info">Refunded</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$156.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-07">
                                            <span class="fw-bold">07/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-14">
                                            <span class="fw-bold">14/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12690</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-primary text-primary">N</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Neil Owen</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$194.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-11">
                                            <span class="fw-bold">11/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-13">
                                            <span class="fw-bold">13/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12691</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-6.jpg" alt="Emma Smith" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Emma Smith</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Failed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Failed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$96.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-10">
                                            <span class="fw-bold">10/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-12">
                                            <span class="fw-bold">12/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12692</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-21.jpg" alt="Ethan Wilder" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Ethan Wilder</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Denied">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Denied</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$314.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-09">
                                            <span class="fw-bold">09/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-11">
                                            <span class="fw-bold">11/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12693</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-6.jpg" alt="Emma Smith" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Emma Smith</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Expired">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Expired</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$286.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-07">
                                            <span class="fw-bold">07/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-10">
                                            <span class="fw-bold">10/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12694</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-12.jpg" alt="Ana Crown" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Ana Crown</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$70.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-04">
                                            <span class="fw-bold">04/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-09">
                                            <span class="fw-bold">09/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12695</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-success text-success">L</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Lucy Kunic</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$237.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-02">
                                            <span class="fw-bold">02/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-08">
                                            <span class="fw-bold">08/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12696</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">E</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Emma Bold</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$33.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-01">
                                            <span class="fw-bold">01/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-07">
                                            <span class="fw-bold">07/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12697</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-12.jpg" alt="Ana Crown" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Ana Crown</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$269.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-31">
                                            <span class="fw-bold">31/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-06">
                                            <span class="fw-bold">06/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12698</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-primary text-primary">N</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Neil Owen</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$101.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-02">
                                            <span class="fw-bold">02/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-05">
                                            <span class="fw-bold">05/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12699</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">M</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Melody Macy</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$167.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-29">
                                            <span class="fw-bold">29/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-04">
                                            <span class="fw-bold">04/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12700</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-21.jpg" alt="Ethan Wilder" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Ethan Wilder</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Processing">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-primary">Processing</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$271.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-02">
                                            <span class="fw-bold">02/11/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-03">
                                            <span class="fw-bold">03/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12701</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-23.jpg" alt="Dan Wilson" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Dan Wilson</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$377.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-27">
                                            <span class="fw-bold">27/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-02">
                                            <span class="fw-bold">02/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12702</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">M</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Melody Macy</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Pending">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-warning">Pending</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$477.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-27">
                                            <span class="fw-bold">27/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-11-01">
                                            <span class="fw-bold">01/11/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12703</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-9.jpg" alt="Francis Mitcham" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Francis Mitcham</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$123.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-29">
                                            <span class="fw-bold">29/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-31">
                                            <span class="fw-bold">31/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12704</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-1.jpg" alt="Max Smith" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Max Smith</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$298.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-26">
                                            <span class="fw-bold">26/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-30">
                                            <span class="fw-bold">30/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12705</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-9.jpg" alt="Francis Mitcham" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Francis Mitcham</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$414.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-27">
                                            <span class="fw-bold">27/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-29">
                                            <span class="fw-bold">29/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12706</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-5.jpg" alt="Sean Bean" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Sean Bean</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Cancelled">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Cancelled</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$138.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-22">
                                            <span class="fw-bold">22/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-28">
                                            <span class="fw-bold">28/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12707</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-12.jpg" alt="Ana Crown" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Ana Crown</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Cancelled">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Cancelled</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$143.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-22">
                                            <span class="fw-bold">22/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-27">
                                            <span class="fw-bold">27/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12708</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-21.jpg" alt="Ethan Wilder" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Ethan Wilder</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$436.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-22">
                                            <span class="fw-bold">22/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-26">
                                            <span class="fw-bold">26/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12709</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-success text-success">L</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Lucy Kunic</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Pending">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-warning">Pending</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$354.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-18">
                                            <span class="fw-bold">18/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-25">
                                            <span class="fw-bold">25/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12710</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-primary text-primary">N</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Neil Owen</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$334.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-20">
                                            <span class="fw-bold">20/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-24">
                                            <span class="fw-bold">24/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12711</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-9.jpg" alt="Francis Mitcham" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Francis Mitcham</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Refunded">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-info">Refunded</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$459.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-21">
                                            <span class="fw-bold">21/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-23">
                                            <span class="fw-bold">23/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12712</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-success text-success">L</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Lucy Kunic</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$300.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-15">
                                            <span class="fw-bold">15/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-22">
                                            <span class="fw-bold">22/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12713</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-warning text-warning">C</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Mikaela Collins</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$118.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-14">
                                            <span class="fw-bold">14/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-21">
                                            <span class="fw-bold">21/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12714</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-6.jpg" alt="Emma Smith" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Emma Smith</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$238.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-14">
                                            <span class="fw-bold">14/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-20">
                                            <span class="fw-bold">20/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12715</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-info text-info">A</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Robert Doe</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$461.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-18">
                                            <span class="fw-bold">18/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-19">
                                            <span class="fw-bold">19/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12716</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">M</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Melody Macy</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$453.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-13">
                                            <span class="fw-bold">13/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-18">
                                            <span class="fw-bold">18/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12717</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">E</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Emma Bold</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$39.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-11">
                                            <span class="fw-bold">11/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-17">
                                            <span class="fw-bold">17/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12718</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-5.jpg" alt="Sean Bean" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Sean Bean</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Expired">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Expired</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$485.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-15">
                                            <span class="fw-bold">15/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-16">
                                            <span class="fw-bold">16/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12719</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-6.jpg" alt="Emma Smith" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Emma Smith</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Expired">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Expired</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$262.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-14">
                                            <span class="fw-bold">14/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-15">
                                            <span class="fw-bold">15/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12720</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-warning text-warning">C</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Mikaela Collins</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Refunded">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-info">Refunded</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$277.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-10">
                                            <span class="fw-bold">10/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-14">
                                            <span class="fw-bold">14/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12721</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-23.jpg" alt="Dan Wilson" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Dan Wilson</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$119.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-10">
                                            <span class="fw-bold">10/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-13">
                                            <span class="fw-bold">13/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12722</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-25.jpg" alt="Brian Cox" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Brian Cox</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Pending">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-warning">Pending</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$267.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-07">
                                            <span class="fw-bold">07/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-12">
                                            <span class="fw-bold">12/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12723</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-5.jpg" alt="Sean Bean" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Sean Bean</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Delivered">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Delivered</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$384.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-10">
                                            <span class="fw-bold">10/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-11">
                                            <span class="fw-bold">11/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12724</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-primary text-primary">N</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Neil Owen</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Failed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Failed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$229.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-05">
                                            <span class="fw-bold">05/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-10">
                                            <span class="fw-bold">10/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12725</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-5.jpg" alt="Sean Bean" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Sean Bean</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$92.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-02">
                                            <span class="fw-bold">02/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-09">
                                            <span class="fw-bold">09/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12726</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-9.jpg" alt="Francis Mitcham" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Francis Mitcham</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Cancelled">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Cancelled</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$158.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-01">
                                            <span class="fw-bold">01/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-08">
                                            <span class="fw-bold">08/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12727</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-21.jpg" alt="Ethan Wilder" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Ethan Wilder</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Failed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Failed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$173.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-02">
                                            <span class="fw-bold">02/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-07">
                                            <span class="fw-bold">07/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12728</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-1.jpg" alt="Max Smith" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Max Smith</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$223.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-03">
                                            <span class="fw-bold">03/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-06">
                                            <span class="fw-bold">06/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12729</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-5.jpg" alt="Sean Bean" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Sean Bean</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$132.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-03">
                                            <span class="fw-bold">03/10/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-05">
                                            <span class="fw-bold">05/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12730</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-primary text-primary">N</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Neil Owen</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Completed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-success">Completed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$279.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-09-29">
                                            <span class="fw-bold">29/09/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-04">
                                            <span class="fw-bold">04/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12731</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-21.jpg" alt="Ethan Wilder" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Ethan Wilder</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Cancelled">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Cancelled</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$354.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-09-28">
                                            <span class="fw-bold">28/09/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-03">
                                            <span class="fw-bold">03/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12732</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label fs-3 bg-light-danger text-danger">M</div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Melody Macy</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Failed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Failed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$39.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-09-26">
                                            <span class="fw-bold">26/09/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-02">
                                            <span class="fw-bold">02/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12733</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-6.jpg" alt="Emma Smith" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Emma Smith</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Failed">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-danger">Failed</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$371.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-09-24">
                                            <span class="fw-bold">24/09/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-10-01">
                                            <span class="fw-bold">01/10/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                            <a href="apps/ecommerce/sales/details.html" class="text-gray-800 text-hover-primary fw-bold">12734</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!--begin:: Avatar -->
                                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                    <a href="apps/user-management/users/view.html">
                                                        <div class="symbol-label">
                                                            <img src="assets/media/avatars/300-21.jpg" alt="Ethan Wilder" class="w-100" />
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--end::Avatar-->
                                                <div class="ms-5">
                                                    <!--begin::Title-->
                                                    <a href="apps/user-management/users/view.html" class="text-gray-800 text-hover-primary fs-5 fw-bold">Ethan Wilder</a>
                                                    <!--end::Title-->
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-0" data-order="Delivering">
                                            <!--begin::Badges-->
                                            <div class="badge badge-light-primary">Delivering</div>
                                            <!--end::Badges-->
                                        </td>
                                        <td class="text-end pe-0">
                                            <span class="fw-bold">$100.00</span>
                                        </td>
                                        <td class="text-end" data-order="2024-09-26">
                                            <span class="fw-bold">26/09/2024</span>
                                        </td>
                                        <td class="text-end" data-order="2024-09-30">
                                            <span class="fw-bold">30/09/2024</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/details.html" class="menu-link px-3">View</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="apps/ecommerce/sales/edit-order.html" class="menu-link px-3">Edit</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu-->
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Products-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
        <!--begin::Footer-->
        @include('wholesale.layouts.footer')
        <!--end::Footer-->
    </div>
    <!--end:::Main-->
@endsection
@section('script')
    <script src="{{asset('assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
    <script src="{{asset('assets/js/custom/apps/ecommerce/sales/listing.js')}}"></script>
    <script src="{{asset('assets/js/widgets.bundle.js')}}"></script>
    <script src="{{asset('assets/js/custom/widgets.js')}}"></script>
    <script src="{{asset('assets/js/custom/apps/chat/chat.js')}}"></script>
    <script src="{{asset('assets/js/custom/utilities/modals/upgrade-plan.js')}}"></script>
    <script src="{{asset('assets/js/custom/utilities/modals/create-app.js')}}"></script>
    <script src="{{asset('assets/js/custom/utilities/modals/users-search.js')}}"></script>
@endsection
