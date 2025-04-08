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
                    <div class="form-check form-check-custom form-check-solid d-flex align-items-center ps-10">
                        <input class="form-check-input me-3 sub-category-checkbox" type="checkbox"
                            id="sub_category_{{ $sub_category->id }}" name="sub_categories[]"
                            value="{{ $sub_category->id }}"
                            {{ in_array($sub_category->id, $addedCategories ?? []) ? 'checked' : '' }}
                            data-sub-category-id="{{ $sub_category->id }}"
                            data-category-id="{{ $sub_category->category_id }}"
                            data-type="{{ in_array($sub_category->id, $addedCategories ?? []) ? 'remove' : 'select' }}"
                            style="width: 17px; height: 17px;" />

                        <label class="form-check-label text-gray-700 fw-semibold fs-6 mb-0"
                            for="sub_category_{{ $sub_category->id }}">
                            {{ strtoupper($sub_category->sub_category_name) }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="separator separator-dashed"></div>
    </div>
@endforeach
