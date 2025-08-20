@extends('layouts.base')
@section('title')
    My Suggestion Category List | TechtrendMart
@endsection

@section('content')
    	<!--begin::Main-->
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <!--begin::Content wrapper-->
            <div class="d-flex flex-column flex-column-fluid">
                <!--begin::Toolbar-->
                <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                    <!--begin::Toolbar container-->
                    <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                        <!--begin::Page title-->
                        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                            <!--begin::Title-->
                            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Suggestion Category List</h1>
                            <!--end::Title-->
                            <!--begin::Breadcrumb-->
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                <!--begin::Item-->
                                <li class="breadcrumb-item text-muted">
                                    <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                                </li>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                </li>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <li class="breadcrumb-item text-muted">Suggestion Category</li>
                                <!--end::Item-->
                            </ul>
                            <!--end::Breadcrumb-->
                        </div>
                        <!--end::Page title-->
                    </div>
                    <!--end::Toolbar container-->
                </div>
                <!--end::Toolbar-->
                <!--begin::Content-->
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <!--begin::Content container-->
                    <div id="kt_app_content_container" class="app-container ">
                        <!--begin::API keys-->
                        <div class="card">
                            <!--begin::Header-->
                            <div class="card-header">
                                <!--begin::Title-->
                                <div class="card-title">
                                    <h3>My Category</h3>
                                </div>
                                <div class="card-toolbar">
                                    <!--begin::Filter-->
                                    <button type="button" class="btn btn-sm btn-flex btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_category">
                                        <i class="ki-duotone ki-plus-square fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        Add New Category request
                                    </button>
                                    <!--end::Filter-->
                                </div>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <!--begin::Table wrapper-->
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table align-middle table-row-bordered table-row-solid fs-7" id="kt_categroy_table">
                                        <!--begin::Thead-->
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div>
                                                <button type="button" id="delete-selected" class="btn btn-danger btn-sm me-2" disabled>
                                                    <i class="fas fa-trash"></i>Delete Selected
                                                </button>
                                            </div>
                                        </div>

                                        <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                                            <tr>
                                                <th class="text-center">
                                                    <input type="checkbox" id="select-all"> Select All
                                                </th>
                                                <th class="min-w-175px ps-9">Category</th>
                                                <th class="min-w-250px px-0">Sub Category</th>
                                                <th class="min-w-250px px-0">Status</th>
                                                <th class="min-w-100px">Created</th>
                                                <th class="min-w-250px px-10">Action</th>
                                            </tr>
                                        </thead>
                                        <!--end::Thead-->
                                        <!--begin::Tbody-->
                                        <tbody class="fs-6 fw-semibold text-gray-600">
                                            @if (count($category_suggestion) > 0)
                                                @foreach ($category_suggestion as $category)
                                                    <tr class="data-load" data-id="{{$category->id}}">
                                                         <td class="text-center">
                                                            <input type="checkbox" class="row-checkbox" value="{{$category->id}}">
                                                        </td>
                                                        <td  class="ps-9">{{ strtoupper($category->category_name)}}</td>
                                                        <td data-bs-target="license" class="ps-0">{{ strtoupper($category->sub_category_name)}}</td>
                                                        <td>{{ (!empty($category->is_approve) && $category->is_approve == 1) ? "Accepted":"Not Accepted" }}</td>
                                                        <td>{{$category->created_at}}</td>
                                                        <td class="ps-9">
                                                            <button class="btn btn-icon btn-light-danger w-30px h-30px me-3" id="remove-btn" data-id="{{$category->id}}"   data-bs-toggle="tooltip"  aria-label="Delete">
                                                                <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                                                <span class="path4"></span><span class="path5"></span></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">No data found.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <!--end::Tbody-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Table wrapper-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::API keys-->
                    </div>
                    <!--end::Content container-->
                </div>
                <!--end::Content-->
            </div>
        </div>
        <!--end:::Main-->

        <div class="modal fade" data-bs-backdrop="static" id="kt_modal_add_category" tabindex="-1" style="display: none;" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content">
                    <!--begin::Modal header-->
                    <div class="modal-header">
                        <!--begin::Modal title-->
                        <h2 class="fw-bold">Add New Category and Sub Category Suggestion Request</h2>
                        <!--end::Modal title-->

                        <!--begin::Close-->
                        <button type="button" id="kt_modal_add_payment_close" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </button>
                        <!--end::Close-->

                    </div>
                    <!--end::Modal header-->

                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                        <!--begin::Form-->
                        <form id="kt_modal_add_categry_form" class="form fv-plugins-bootstrap5 fv-plugins-framework">
                            <!--begin::Input group-->
                            <div class="fv-row mb-7 fv-plugins-icon-container">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold form-label mb-2">
                                    <span class="required">Category Name</span>

                                    <span class="ms-2" data-bs-toggle="tooltip" aria-label="The invoice number must be unique." data-bs-original-title="The invoice number must be unique." data-kt-initialized="1">
                                        {{-- <i class="ki-duotone ki-information fs-7"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>                            </span> --}}
                                </label>
                                <!--end::Label-->

                                <!--begin::Input-->
                                <input type="text" class="form-control form-control-solid" name="category" id="category">
                                <!--end::Input-->
                            <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div></div>
                            <!--end::Input group-->


                            <!--begin::Input group-->
                            <div class="fv-row mb-7 fv-plugins-icon-container">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-semibold form-label mb-2">Sub Category</label>
                                <!--end::Label-->

                                <!--begin::Input-->
                                <input type="text" class="form-control form-control-solid" name="sub_category" id="sub_category">
                                <!--end::Input-->
                            <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div></div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="text-center">
                                <button type="reset" id="kt_modal_add_category_cancel" class="btn btn-light me-3">
                                    Discard
                                </button>

                                <button type="submit" id="kt_modal_add_category_submit" class="btn btn-primary">
                                    <span class="indicator-label">
                                        Submit
                                    </span>
                                    <span class="indicator-progress">
                                        Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var xhr;
            function request_call(url, mydata) {
                if (xhr && xhr.readyState != 4) {
                    xhr.abort();
                }

                xhr = $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: mydata,
                });
            };

            // Handle form submission for adding category & subcategory
            // $("#kt_modal_add_categry_form").submit(function(e) {
            //     e.preventDefault(); // Prevent default form submission

            //     let categoryName = $("input[name='category']").val();
            //     let subCategoryName = $("input[name='sub_category']").val();

            //     request_call("{{ url('category-suggestion-create')}}", "categoryName=" + categoryName  + "&subCategoryName=" + subCategoryName);
            //     xhr.done(function(mydata) {
            //         Swal.fire({
            //             title: 'Category and Sub Category Suggestion Added Successfully!',
            //             icon: 'success',
            //         });

            //         $("#kt_categroy_table").load(location.href + " #kt_categroy_table");
            //         // Reset form

            //         $("input[name='category']").val('');
            //         $("input[name='sub_category']").val('');
            //         // display modal none
            //         $("#kt_modal_add_category").modal('hide');
            //     });
            //     xhr.fail(function(mydata) {
            //         Swal.fire({
            //             icon:'error',
            //             title: 'Category Add Failed!',
            //             showCancelButton: true
            //         })
            //     });

            // });

            $("#kt_modal_add_categry_form").submit(function(e) {
                e.preventDefault();

                let categoryName = $("#category").val();
                let subCategoryName = $("#sub_category").val();

                request_call("{{ url('category-suggestion-create')}}", {
                    categoryName: categoryName,
                    subCategoryName: subCategoryName
                });

                xhr.done(function(mydata) {
                    // ✅ Clear old error messages
                    $(".invalid-feedback").text('');
                    $(".form-control").removeClass("is-invalid");

                    // Success message (optional inline)
                    $("#kt_categroy_table").load(location.href + " #kt_categroy_table");

                    // Reset form
                    $("#category").val('');
                    $("#sub_category").val('');

                    $("#kt_modal_add_category").modal('hide');
                });

                xhr.fail(function(xhr) {
                    if (xhr.status === 422) {
                        // Validation error
                        let errors = xhr.responseJSON.messages;

                        // Clear old errors
                        $(".invalid-feedback").text('');
                        $(".form-control").removeClass("is-invalid");

                        // Show new errors
                        if (errors.categoryName) {
                            $("#category").addClass("is-invalid");
                            $("#category").siblings(".invalid-feedback").text(errors.categoryName[0]);
                        }
                        if (errors.subCategoryName) {
                            $("#sub_category").addClass("is-invalid");
                            $("#sub_category").siblings(".invalid-feedback").text(errors.subCategoryName[0]);
                        }
                    }
                });
            });

             $('#kt_modal_add_category').on('hidden.bs.modal shown.bs.modal', function () {
                $("#kt_modal_add_categry_form")[0].reset();
                $(".invalid-feedback").text('');
                $(".form-control").removeClass("is-invalid");
            });


            $(document).on('click', '#remove-btn', function() {
                Swal.fire({
                    icon:'warning',
					title: 'Are you sure, you want to remove it?',
                    showCancelButton: true,
                    confirmButtonColor: '#000',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const id = $(this).attr('data-id');

                        request_call("{{ url('category-suggestion-delete')}}", "id=" + id);
                        xhr.done(function(mydata) {

                            Swal.fire({
                                icon:'success',
                                title: 'Category Removed Successuflly!',
                                showCancelButton: true
                            })

                            $("#kt_categroy_table").load(location.href + " #kt_categroy_table");
                        });
                        xhr.fail(function(mydata) {
                            Swal.fire({
                                icon:'error',
                                title: 'Category Remove Failed!',
                                showCancelButton: true
                            })
                        });
                    }
                });

            });
        });

        $(document).ready(function () {
            $('#kt_categroy_table').DataTable({
                pageLength: 20,
                lengthMenu: [10, 20, 50, 100],
                order: [[4, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 0 }
                ]
            });
        });

        // <-------------START multipel select----->
            $(document).ready(function () {
                // Handle select all checkbox
                $("#select-all").on("change", function () {
                    $(".row-checkbox").prop("checked", $(this).prop("checked"));
                    toggleDeleteButton();
                });

                // Handle single row checkbox
                $(document).on("change", ".row-checkbox", function () {
                    if ($(".row-checkbox:checked").length === $(".row-checkbox").length) {
                        $("#select-all").prop("checked", true);
                    } else {
                        $("#select-all").prop("checked", false);
                    }
                    toggleDeleteButton();
                });

                // Enable/disable Delete Selected button
                function toggleDeleteButton() {
                    if ($(".row-checkbox:checked").length > 0) {
                        $("#delete-selected").prop("disabled", false);
                    } else {
                        $("#delete-selected").prop("disabled", true);
                    }
                }

                // Handle bulk delete
                $("#delete-selected").on("click", function () {
                    let ids = $(".row-checkbox:checked").map(function () {
                        return $(this).val();
                    }).get();

                    if (ids.length === 0) return;

                    Swal.fire({
                        icon: 'warning',
                        title: 'Are you sure?',
                        text: 'You are about to delete selected categories.',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete them!',
                        confirmButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('category-suggestion-bulk-delete') }}",
                                type: "POST",
                                data: {
                                    ids: ids,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function (response) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    $("#kt_categroy_table").load(location.href + " #kt_categroy_table");
                                },
                                error: function () {
                                    Swal.fire('Error!', 'Failed to delete records.', 'error');
                                }
                            });
                        }
                    });
                });
            });


    </script>
@endsection
