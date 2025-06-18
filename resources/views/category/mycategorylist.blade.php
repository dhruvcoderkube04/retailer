@extends('layouts.base')
@section('title')
    My Category List | TrendMart
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container  d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">My
                            Category List</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="index.html" class="text-muted text-hover-primary">Category</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">My Category list</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container ">
                    <div class="card">
                        <div class="card-body p-5">
                            <table class="table align-middle table-row-dashed fs-7" id="kt_datatable_my_categroy_list">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-center min-w-100px">Action</th>
                                        <th class="text-center min-w-100px">Images</th>
                                        <th class="text-center min-w-175px">Category</th>
                                        <th class="text-center min-w-250px">Sub Category</th>
                                        <th class="text-center min-w-150px">Created</th>
                                    </tr>
                                </thead>

                                <tbody class="fw-semibold text-gray-600">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="category-image-update" tabindex="-1" aria-labelledby="category-image-update-label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="category-image-update-label">Update Category Image</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="categoryImageForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-5 text-center">
                            <label class="form-label fw-bold">Current Category Image:</label>
                            <br>
                            <img id="image-preview" src="" alt="Category Image" class="img-fluid rounded"
                                style="max-width: 200px; max-height: 200px; display: none; border: 1px solid #ccc; margin-bottom: 10px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload New Image:</label>
                            <input type="file" class="form-control" name="category_image" id="category_image">
                        </div>
                        <span class="text-danger mt-2 d-none" id="image-error"></span>

                        <input type="hidden" name="retailer_category_id" id="retailer_category_id">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        //<------------- START : server-side transaction datatable ------------->
        dataTable = $('#kt_datatable_my_categroy_list').DataTable({
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
                url: "{{ route('retailer.my-category-list.fetch-record') }}",
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
            columns: [{
                    data: 'action',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'sub_category_image',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'category_name',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'sub_category_name',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'created_at',
                    className: 'text-center',
                    orderable: true,
                }
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
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: mydata,
                });
            };

            $(document).on('click', '#remove-btn', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Are You sure to Delete it !',
                    showCancelButton: true,
                    confirmButtonColor: '#000',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const subCategoryId = $(this).attr('data-sub_category');
                        const categoryId = $(this).attr('data-category_id');
                        const id = $(this).attr('data-id');

                        request_call("{{ url('remove-category') }}", "category_id=" + categoryId +
                            "&sub_category=" + subCategoryId + "&id=" + id);
                        xhr.done(function(mydata) {
                            if (mydata.status) {
                                location.reload();
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: mydata.msg,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                        xhr.fail(function(mydata) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Subcategory Remove Failed!',
                                showCancelButton: true
                            })
                        });
                    }
                });

            });

            $(document).on("click", "#image-upload", function() {
                let categoryId = $(this).data("id");
                let imageUrl = $(this).data("image");
                let fallbackImage = "{{ asset('assets/media/images/no_image.jpg') }}";

                $("#retailer_category_id").val(categoryId);

                $("#image-preview")
                    .off("error") // remove previous error handlers just in case
                    .on("error", function() {
                        $(this).off("error");
                        $(this).attr("src", fallbackImage);
                    })
                    .attr("src", imageUrl || fallbackImage)
                    .show();

                $("#category-image-update").modal("show");
            });

            // Handle form submission with AJAX
            $(document).on("submit", "#categoryImageForm", function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('retailer.category-image.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $("#image-error").addClass("d-none").text(""); // Reset error message
                    },
                    success: function(response) {
                        if (response.status) {
                            location.reload();
                            $("#category-image-update").modal("hide");
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.category_image) {
                                $("#image-error").removeClass("d-none").text(errors
                                    .category_image[0]);
                            }
                        } else {
                            alert("Something went wrong! " + xhr.responseJSON.msg);
                        }
                    },
                });
            });
        });
    </script>
@endsection
