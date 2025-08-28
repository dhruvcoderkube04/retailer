<div class="m-0">
    <h2 class="fs-3 text-dark text-center fw-bold mb-8">Choose Your Category List</h2>
</div>

{{-- Global Select All --}}
<div class="form-check mb-3">
    <input type="checkbox" class="form-check-input" id="select_all_categories" style="width: 17px; height: 17px;" />
    <label class="form-check-label fw-bold text-gray-800" for="select_all_categories"><h4>Select All Categories</h4></label>
</div><br>

@foreach ($categories as $a => $category)
    <div class="m-0">
         {{-- Per Category Select All --}}
        <div class="form-check ps-10 mb-2">
            @php
                $allSubChecked = $category->subCategory->pluck('id')->every(fn($id) => in_array($id, $addedCategories ?? []));
            @endphp
            <input type="checkbox" class="form-check-input select-category"
                   id="select_category_{{ $category->id }}"
                   data-category-id="{{ $category->id }}"
                   style="width: 17px; height: 17px;"  {{ $allSubChecked ? 'checked' : '' }}/>
            <label class="form-check-label fw-bold text-gray-700" for="select_category_{{ $category->id }}">
                <h4>{{ strtoupper($category->category_name) }}</h4>
            </label>
        </div>

        {{-- <div id="category{{ $a }}" class="collapse show fs-6 ms-1"> --}}
        <div id="category{{ $category->id }}" class="collapse show fs-6 ms-1">
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
