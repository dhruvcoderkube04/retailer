<div class="mb-10 mt-2 text-center">
    <h2 class="fs-3 text-dark fw-bold">Selected Category List</h2>
</div>

@foreach ($retailerCateogries as $category)
    <div class="d-flex justify-content-between align-items-center p-3 bg-white shadow-sm rounded mb-3">
        <div>
            <h5 class="text-dark fw-semibold mb-0">
                {{ $category->subCategory->sub_category_name ?? '' }}
                <span class="text-muted">({{ $category->category->category_name }})</span>
            </h5>
        </div>
        <button class="btn btn-sm btn-danger d-flex align-items-center remove-sub-category"
            data-sub-category-id={{ $category->sub_category_id }} data-category-id={{ $category->category_id }}
            data-type="remove">
            <i class="bi bi-x-circle me-1"></i> Remove
        </button>
    </div>
@endforeach
