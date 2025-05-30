@extends('layouts.base')
@section('title')
    Subscribed Categories | TrendMart
@endsection
@section('content')
    @if ($is_all_wholesaler_visible)
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                            <h1
                                class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                                Subscribed Categories
                            </h1>
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                <li class="breadcrumb-item text-muted">
                                    <a href="{{ route('retailer.dashboard') }}"
                                        class="text-muted text-hover-primary">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                </li>
                                <li class="breadcrumb-item text-muted">Subscribed Categories</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div id="kt_app_content_container" class="app-container container-xxl">
                        @if (session('success'))
                            <div class="alert alert-success text-green-600 p-2">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger text-green-600 p-2">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="card">
                            <div class="card-body pt-4">
                                <table class="table align-middle table-row-dashed fs-7"
                                    id="kt_datatable_wholesaler_list">
                                    <thead>
                                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="text-center align-middle min-w-50px">Action</th>
                                            <th class="text-center align-middle min-w-50px">Media</th>
                                            <th class="text-center align-middle min-w-100px">Category</th>
                                            <th class="text-center align-middle min-w-100px">Wholesaler</th>
                                            <th class="text-center align-middle min-w-200px">Payment Method</th>
                                            <th class="text-center min-w-70px">Margin
                                                <br> <span class="text-capitalize fs-9">(In Rs.)</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @else
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div id="kt_app_content_container" class="app-container container-xxl">
                        <div class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-5 mb-10">
                            <i class="ki-duotone ki-message-text-2 fs-2hx text-primary me-4 mt-2 mb-5 mb-sm-0">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-column ps-3 m-1 pe-sm-10">
                                <h4 class="fw-semibold">No Access</h4>
                                <p class="mb-2">Unfortunately, you do not have the required access to use this facility.
                                </p>
                                <p>If you believe you should have access, please contact your administrator for further
                                    assistance.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="modal fade" id="kt_modal_edit_margin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Update Margin</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <form id="marginupdateform" class="form" method="POST" action="{{ route('retailer.update-category-margin') }}">
                        @csrf
                        <input type="hidden" id="edit_wholesaler_id" name="wholesaler_id">
                        <input type="hidden" id="edit_margin_id" name="margin_id">

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="edit_category_id" name="category_id" data-control="select2">
                                <option value="">Select Category</option>
                                {{-- @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach --}}
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Margin</label>
                            <input type="number" min="1" class="form-control" id="edit_margin_value" name="margin">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input mt-1" id="edit_payment_cod" name="payment_method[]" value="COD">
                                <label class="form-check-label mt-1" for="edit_payment_cod">COD</label>
                            </div>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input mt-1" id="edit_payment_prepaid" name="payment_method[]" value="Prepaid">
                                <label class="form-check-label mt-1" for="edit_payment_prepaid">Prepaid</label>
                            </div>
                            <div class="form-check mt-1">
                                <input type="checkbox" class="form-check-input mt-1" id="edit_payment_semi" name="payment_method[]" value="Semi">
                                <label class="form-check-label mt-1" for="edit_payment_semi">Semi</label>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Margin</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        //<------------- START : server-side transaction datatable ------------->
        dataTable = $('#kt_datatable_wholesaler_list').DataTable({
            dom: "<'row mb-2'" +
                "<'col-4 col-sm-6 col-md-3 d-flex align-items-center justify-content-start dt-toolbar datatable-length-section'l>" +
                "<'col-8 col-sm-6 col-md-9 d-flex align-items-center justify-content-end dt-toolbar datatable-search-section'f>" +
                ">" +
                "<'table-responsive'tr>" +
                "<'row'" +
                "<'col-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start mt-6'i>" +
                "<'col-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                ">",
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('retailer.subscribed-category.fetch-record') }}",
                type: "POST",
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.order = d.order; // Add order data
                    d.columns = d.columns; // Add columns data
                },
                dataSrc: function(json) {
                    return json.data;
                }
            },
            order: [],
            columns: [
                {
                    data: 'action',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'category_image',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'category_name',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'wholesaler_name',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'payment_method',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'margin',
                    className: 'text-center',
                    orderable: false,
                },
            ],
            initComplete: function() {
                let searchBox = $('.datatable-search-section input');
                let searchLabel = $('.datatable-search-section label');
                let lengthSelect = $('.datatable-length-section select');

                searchBox.wrap(
                    '<div class="d-flex align-items-center position-relative my-1 w-100"></div>'
                );
                searchBox.before(
                    '<i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>'
                ); // add icon
                searchBox.addClass('form-control form-control-solid w-100 ps-12 bg-secondary').attr(
                    'placeholder', 'Search'); // style the search input
                searchBox.css({
                    'padding': '13px 15px 12px 15px',
                    'font-size': '14px',
                });

                searchLabel.css({
                    'display': 'none',
                });

                lengthSelect.addClass('form-control form-control-solid w-100 bg-secondary');
                lengthSelect.css({
                    'padding': '13px 27px 12px 14px',
                    'font-size': '14px',
                })
            }
        });
        //<------------- END : server-side transaction datatable ------------->

        // DELETE margin with Swal confirmation
        $(document).on('click', '.delete-margin-btn', function () {
            const url = $(this).data('url');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function (response) {
                            Swal.fire('Deleted!', 'Margin has been removed.', 'success').then(() => {
                                location.reload(); // Reload page or use table.row(...).remove().draw() if dynamic
                            });
                        },
                        error: function () {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.edit-margin-btn', function () {
            const wholesalerId = $(this).data('wholesaler-id');
            const marginId = $(this).data('margin-id');

            $.ajax({
                url: "{{ route('retailer.edit-category-margin') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    wholesaler_id: wholesalerId,
                    margin_id: marginId
                },
                success: function (response) {

                    if (response.success) {

                        const data = response.data;

                        const categories = response.categories;

                        let $select = $('#edit_category_id');

                        $select.empty().append('<option value="">Select Category</option>');

                        categories.forEach(category => {

                            let selected = category.id === data.category_id ? 'selected' : '';

                            $select.append(`<option value="${category.id}" ${selected}>${category.category_name}</option>`);

                        });

                        $('#edit_margin_value').val(data.margin);

                        $('#edit_margin_id').val(data.id);

                        $('#edit_wholesaler_id').val(data.wholesaler_id);

                        let paymentMethods = data.payment_method ?? [];

                        ['COD', 'Prepaid', 'Semi'].forEach(method => {

                            $('#edit_payment_' + method.toLowerCase()).prop('checked', paymentMethods.includes(method));

                        });

                        $('#kt_modal_edit_margin').modal('show');

                    }

                }

            });
        });

        $('#marginupdateform').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: "{{ route('retailer.update-category-margin') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: "Success!",
                            text: "Margin Update successfully!",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            // form reset
                            document.getElementById('marginupdateform').reset();
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "Something went wrong!",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = "";
                    $.each(errors, function(key, value) {
                        errorMsg += value[0] + "\n";
                    });

                    Swal.fire({
                        title: "Validation Error",
                        text: errorMsg,
                        icon: "warning",
                        confirmButtonText: "OK"
                    });
                }
            });
        });
    </script>
@endsection
