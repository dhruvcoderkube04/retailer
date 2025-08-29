@extends('layouts.base')

@section('title')
    Website Content | TechtrendMart
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">

            <!-- Toolbar -->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 my-0">Manage About us Content</h1>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container">

                    {{-- Flash messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- About US SECTION -->
                    <div class="card mb-10">
                        <div class="card-header">
                            <h3 class="card-title">About Us Section</h3>
                        </div>
                        <div class="card-body">
                            <form id="aboutUsForm"
                                action="{{ isset($aboutSection) ? route('retailer.website-content.aboutus.update', $aboutSection->id) : route('retailer.website-content.aboutus.store') }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="section" value="about-us">

                                <div class="mb-5">
                                    <label class="form-label required">Content</label>
                                    <textarea name="content" id="contentEditor" class="form-control" rows="6" placeholder="Enter about us content">{{ old('content', $aboutSection->content ?? '') }}</textarea>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($aboutSection) ? 'Update About Us' : 'Save About Us' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- jQuery Validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <!-- CKEditor Classic CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

    <script>
        let contentEditor;

        $(document).ready(function() {
            // Initialize CKEditor after DOM is ready
            ClassicEditor
                .create(document.querySelector('#contentEditor'))
                .then(editor => {
                    contentEditor = editor;
                })
                .catch(error => {
                    console.error("CKEditor init error:", error);
                });

            // jQuery Validation
            $("#aboutUsForm").validate({
                ignore: [], // important for CKEditor
                rules: {
                    content: {
                        required: function() {
                            return contentEditor.getData().trim() === '';
                        }
                    }
                },
                messages: {
                    content: {
                        required: "Please enter About Us content."
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "content") {
                        error.insertAfter("#contentEditor");
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    // Sync CKEditor data to textarea before submit
                    $('#contentEditor').val(contentEditor.getData());
                    form.submit();
                }
            });
        });
    </script>
@endsection
