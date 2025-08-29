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

                    <!-- Contact US SECTION -->
                    <div class="card mb-10">
                        <div class="card-header">
                            <h3 class="card-title">Contact Us Section</h3>
                        </div>
                        <div class="card-body">
                            <form id="contactUsForm"
                                action="{{ isset($contactSection) ? route('retailer.website-content.contactus.update', $contactSection->id) : route('retailer.website-content.contactus.store') }}"
                                method="POST">
                                @csrf
                                @if (isset($contactSection))
                                    @method('PUT')
                                @endif
                                <input type="hidden" name="section" value="contact-us">
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                                <div class="mb-5">
                                    <label class="form-label required">Title</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ old('title', $contactSection->title ?? '') }}" placeholder="Enter title">
                                </div>

                                <div class="mb-5">
                                    <label class="form-label required">Content</label>
                                    <textarea name="content" id="contactContentEditor" class="form-control" rows="5" placeholder="Enter content">{{ old('content', $contactSection->content ?? '') }}</textarea>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label required">Phone</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', $contactSection->phone ?? '') }}"
                                        placeholder="Enter phone number">
                                </div>

                                <div class="mb-5">
                                    <label class="form-label required">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $contactSection->email ?? '') }}" placeholder="Enter email">
                                </div>

                                <div class="mb-5">
                                    <label class="form-label required">Address</label>
                                    <textarea name="address" class="form-control" rows="3" placeholder="Enter address">{{ old('address', $contactSection->address ?? '') }}</textarea>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label required">Facebook Link</label>
                                    <input type="url" name="facebook_link" class="form-control"
                                        value="{{ old('facebook_link', $contactSection->facebook_link ?? '') }}"
                                        placeholder="Enter Facebook link">
                                </div>

                                <div class="mb-5">
                                    <label class="form-label required">Twitter Link</label>
                                    <input type="url" name="twitter_link" class="form-control"
                                        value="{{ old('twitter_link', $contactSection->twitter_link ?? '') }}"
                                        placeholder="Enter Twitter link">
                                </div>

                                <div class="mb-5">
                                    <label class="form-label required">LinkedIn Link</label>
                                    <input type="url" name="linkedin_link" class="form-control"
                                        value="{{ old('linkedin_link', $contactSection->linkedin_link ?? '') }}"
                                        placeholder="Enter LinkedIn link">
                                </div>

                                <div class="mb-5">
                                    <label class="form-label required">Instagram Link</label>
                                    <input type="url" name="instagram_link" class="form-control"
                                        value="{{ old('instagram_link', $contactSection->instagram_link ?? '') }}"
                                        placeholder="Enter Instagram link">
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($contactSection) ? 'Update Contact Us' : 'Save Contact Us' }}
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
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script>
        let contactEditor;
        ClassicEditor
            .create(document.querySelector('#contactContentEditor'))
            .then(editor => {
                contactEditor = editor;
            })
            .catch(error => {
                console.error("CKEditor error:", error);
            });

        // jQuery Validation
        $(document).ready(function() {
            $("#contactUsForm").validate({
                ignore: [], // important for CKEditor
                rules: {
                    title: {
                        required: true,
                        minlength: 3
                    },
                    content: {
                        required: function() {
                            // get CKEditor content
                            return contactEditor.getData().trim().length === 0;
                        }
                    },
                    address: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 15
                    },
                    facebook_link: {
                        required: true,
                        url: true
                    },
                    twitter_link: {
                        required: true,
                        url: true
                    },
                    linkedin_link: {
                        required: true,
                        url: true
                    },
                    instagram_link: {
                        required: true,
                        url: true
                    }
                },
                messages: {
                    title: {
                        required: "Please enter a title",
                        minlength: "Title must be at least 3 characters"
                    },
                    content: {
                        required: "Please enter some content"
                    },
                    email: {
                        required: "Please enter email",
                        email: "Please enter a valid email"
                    },
                    phone: {
                        digits: "Phone must contain only numbers",
                        minlength: "Phone must be at least 10 digits",
                        maxlength: "Phone must not exceed 15 digits"
                    }
                },
                errorClass: "text-danger",
                errorElement: "div",
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                }
            });
        });
    </script>
@endsection
