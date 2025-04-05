<div class="m-0">
    <h2 class="fs-3 text-dark text-center fw-bold mb-8">Choose Your Category List</h2>
</div>

@foreach ($categories as $a => $category)
    <div class="m-0">
        <div class="d-flex align-items-center collapsible py-3 toggle mb-0" data-bs-toggle="collapse"
            data-bs-target="#category{{ $a }}">
            <div class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-5">
                <i class="ki-duotone ki-minus-square toggle-on text-primary fs-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <i class="ki-duotone ki-plus-square toggle-off fs-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
            </div>
            <h4 class="text-gray-700 fw-bold cursor-pointer mb-0">
                {{ strtoupper($category->category_name) }}
            </h4>
        </div>

        <div id="category{{ $a }}" class="collapse show fs-6 ms-1">
            @foreach ($category->subCategory as $b => $sub_category)
                <div class="mb-4">
                    <div class="d-flex align-items-center ps-10 mb-n1">
                        <span class="bullet me-3"></span>
                        <div class="text-gray-600 fw-semibold fs-6">
                            {{ strtoupper($sub_category->sub_category_name) }}
                            <a href="javascript:void(0)" class="select-sub-category"
                                data-sub-category-id={{ $sub_category->id }}
                                data-category-id={{ $sub_category->category_id }} data-type="select">

                                @if (!in_array($sub_category->id, $addedCategories ?? []))
                                    <span class="badge badge-primary">
                                        Select
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="separator separator-dashed"></div>
    </div>
@endforeach
