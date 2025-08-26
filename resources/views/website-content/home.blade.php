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
                        <h1 class="page-heading text-gray-900 fw-bold fs-2 my-0">Manage Website Content</h1>
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

                    <!-- HERO SECTION -->
                    <div class="card mb-10">
                        <div class="card-header">
                            <h3 class="card-title">Hero Section</h3>
                        </div>
                        <div class="card-body">
                            <form id="heroForm"
                                action="{{ $heroSection ? route('retailer.website-content.update', $heroSection->id) : route('retailer.website-content.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ($heroSection)
                                    @method('POST')
                                @endif
                                <input type="hidden" name="section" value="hero">

                                <div class="mb-5">
                                    <label class="form-label required">Title</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ old('title', $heroSection->title ?? '') }}"
                                        placeholder="Enter hero title">
                                </div>

                                <div class="mb-5">
                                    <label class="form-label required">Content</label>
                                    <textarea name="content" id="heroContent" class="form-control" rows="4" placeholder="Enter hero content">{{ old('content', $heroSection->content ?? '') }}</textarea>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label {{ $heroSection ? '' : 'required' }}">Hero Image</label>
                                    <input class="form-control" type="file" name="content_image"
                                        accept="image/jpeg,image/png,image/jpg">
                                    @if ($heroSection && $heroSection->content_image)
                                        <div class="mt-2">
                                            <img src="{{ asset('uploads/' . $heroSection->content_image) }}" width="150">
                                        </div>
                                    @endif
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        {{ $heroSection ? 'Update Hero Section' : 'Save Hero Section' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- SECONDARY SECTION -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Secondary Section</h3>
                        </div>
                        <div class="card-body">
                            <form id="secondaryForm"
                                action="{{ $secondarySection ? route('retailer.website-content.update', $secondarySection->id) : route('retailer.website-content.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ($secondarySection)
                                    @method('POST')
                                @endif
                                <input type="hidden" name="section" value="secondary">

                                <div class="mb-5">
                                    <label class="form-label required">Title</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ old('title', $secondarySection->title ?? '') }}"
                                        placeholder="Enter secondary title">
                                </div>

                                <div class="mb-5">
                                    <label class="form-label required">Content</label>
                                    <textarea name="content" id="secondaryContent" class="form-control" rows="4"
                                        placeholder="Enter secondary content">{{ old('content', $secondarySection->content ?? '') }}</textarea>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label {{ $secondarySection ? '' : 'required' }}">Secondary
                                        Image</label>
                                    <input class="form-control" type="file" name="content_image"
                                        accept="image/jpeg,image/png,image/jpg">
                                    @if ($secondarySection && $secondarySection->content_image)
                                        <div class="mt-2">
                                            <img src="{{ asset('uploads/' . $secondarySection->content_image) }}"
                                                width="150">
                                        </div>
                                    @endif
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-success">
                                        {{ $secondarySection ? 'Update Secondary Section' : 'Save Secondary Section' }}
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
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        $(function() {
            function setupValidation(formId, isRequiredImage) {
                $("#" + formId).validate({
                    rules: {
                        title: {
                            required: true,
                            minlength: 3
                        },
                        content: {
                            required: true,
                            minlength: 10
                        },
                        content_image: {
                            required: isRequiredImage,
                            extension: "jpg|jpeg|png",
                            filesize: 2097152
                        }
                    },
                    messages: {
                        title: {
                            required: "Title is required",
                            minlength: "At least 3 characters"
                        },
                        content: {
                            required: "Content is required",
                            minlength: "At least 10 characters"
                        },
                        content_image: {
                            required: "Image is required",
                            extension: "Only jpg, jpeg, png allowed",
                            filesize: "Max size 2MB"
                        }
                    },
                    errorElement: "div",
                    errorPlacement: function(error, element) {
                        error.addClass("text-danger mt-2");
                        error.insertAfter(element);
                    },
                    highlight: function(element) {
                        $(element).addClass("is-invalid");
                    },
                    unhighlight: function(element) {
                        $(element).removeClass("is-invalid");
                    }
                });
            }

            $.validator.addMethod("filesize", function(value, element, param) {
                if (element.files.length === 0) return true;
                return element.files[0].size <= param;
            });

            setupValidation("heroForm", {{ $heroSection ? 'false' : 'true' }});
            setupValidation("secondaryForm", {{ $secondarySection ? 'false' : 'true' }});

            // ✅ Initialize CKEditor 5
            ClassicEditor
                .create(document.querySelector('#heroContent'))
                .catch(error => console.error(error));

            ClassicEditor
                .create(document.querySelector('#secondaryContent'))
                .catch(error => console.error(error));
        });
    </script>
@endsection
