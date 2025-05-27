@extends('layouts.base')

@section('title', 'Ticket Details | TrendMart')

@section('content')
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Ticket Details
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('retailer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">Ticket Details</li>
                    </ul>
                </div>
            </div>
        </div>

        @php
            $status = strtolower($ticket->status);
            $badgeClass = match($status) {
                'open' => 'badge-danger',
                'in progress' => 'badge-info',
                'closed' => 'badge-secondary',
                'resolved' => 'badge-success',
                default => 'badge-light',
            };
        @endphp

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card mb-5">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <h3 class="mb-3">{{ $ticket->subject }}</h3>
                        <p><strong>Status:</strong> <span class="badge {{ $badgeClass }}">{{ ucfirst($ticket->status) }}</span></p>
                        <p>{{ $ticket->description }}</p>

                        <div class="d-flex flex-wrap gap-3 mt-4">
                            @php
                                $images = explode(',', $ticket->ref_image);
                            @endphp
                            @foreach($images as $image)
                                @if($image)
                                    <img src="https://techsell.blr1.cdn.digitaloceanspaces.com/tickets/{{ $image }}"
                                         alt="Ticket Image"
                                         class="ticket-image rounded border"
                                         style="width: 150px; height: auto; cursor: pointer;">
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">Update Ticket</h5>
                        <form id="reopenForm" data-action="{{ route('retailer.ticket.status.update', encryptId($ticket->ticket_id)) }}">
                            @csrf
                            <input type="hidden" name="status" value="Open">
                            <div class="mb-4">
                                <label for="description" class="form-label">Ticket Description</label>
                                <textarea name="description" disabled id="description" rows="4" class="form-control" required>{{ $ticket->description }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" {{ strtolower($ticket->status) !== 'closed' ? 'disabled' : '' }}>
                                Reopen
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Image popup modal --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img src="" id="modalImage" class="img-fluid" alt="Full Image">
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
    // Image modal
    document.querySelectorAll('.ticket-image').forEach(img => {
        img.addEventListener('click', () => {
            document.getElementById('modalImage').src = img.src;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        });
    });

    // AJAX Reopen Ticket Form
    document.getElementById('reopenForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const action = form.getAttribute('data-action');
        const formData = new FormData(form);

        fetch(action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message || 'Ticket reopened successfully.',
                }).then(() => window.location.reload());
            } else {
                Swal.fire('Error', data.message || 'Something went wrong.', 'error');
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error', 'Unexpected error occurred.', 'error');
        });
    });
</script>
@endsection
