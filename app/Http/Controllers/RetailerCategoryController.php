<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategorySuggestion;
use App\Models\RetailerCategory;
use App\Models\RetailerCloneProduct;
use App\Models\RetailerProducts;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RetailerCategoryController extends Controller
{
    // category-list
    public function categoryList()
    {
        $user = Auth::user();
        $categories = Category::with([
            'subCategory' => function ($q) {
                $q->where('status', 1);
            }
        ])
            ->whereHas('subCategory', function ($q) {
                $q->where('status', 1);
            })
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();
        $addedCategories = RetailerCategory::where('retailer_id', $user->id)->pluck('sub_category_id')->toArray();
        $retailerCateogries = RetailerCategory::with([
            'retailer',
            'category',
            'subCategory'
        ])
            ->where('retailer_id', $user->id)
            ->get();

        return view('category.categorylist', compact('categories', 'addedCategories', 'retailerCateogries'));
    }

    // add OR remove category (AJAX)
    public function addRetailerCategory(Request $request)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'category_id' => 'required|exists:categories,id',
            'actionType' => 'required|in:select,remove'
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $retailerCategory = RetailerCategory::where([
                'retailer_id' => $user->id,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id
            ]);

            if ($request->actionType === 'select') {
                if (!$retailerCategory->exists()) {
                    RetailerCategory::create([
                        'retailer_id' => $user->id,
                        'category_id' => $request->category_id,
                        'sub_category_id' => $request->sub_category_id
                    ]);
                }
            } else {
                $retailerCloneProductExist = RetailerCloneProduct::where('sub_category_id', $request->sub_category_id)
                    ->where('retailer_id', $user->id)
                    ->exists();
                if ($retailerCloneProductExist) {
                    return response()->json(['status' => false, 'msg' => "You can't delete this!, Category is already in use"]);
                }

                $categoryData = $retailerCategory->first();
                if ($categoryData && $categoryData->category_image) {
                    deleteImageToSpaces($categoryData->category_image);
                }
                $retailerCategory->delete();

                // delete margin related to this sub_category
                RetailerProducts::where('sub_category_id', $request->sub_category_id)
                    ->where('retailer_id', $user->id)
                    ->delete();
            }

            $categories = Category::with([
                'subCategory' => function ($q) {
                    $q->where('status', 1);
                }
            ])
                ->whereHas('subCategory', function ($q) {
                    $q->where('status', 1);
                })
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();
            $addedCategories = RetailerCategory::where('retailer_id', $user->id)->pluck('sub_category_id')->toArray();
            $retailerCateogries = RetailerCategory::with([
                'retailer',
                'category',
                'subCategory'
            ])
                ->where('retailer_id', $user->id)
                ->get();

            $html_1 = view('category.ajax.reload-categorylist', compact('categories', 'addedCategories'))->render();
            $html_2 = view('category.ajax.reload-selected-categorylist', compact('retailerCateogries'))->render();

            DB::commit();
            return response()->json([
                'status' => true,
                'msg' => $request->actionType == 'select' ? 'Category added successfully' : 'Category removed successfully',
                'html_1' => $html_1,
                'html_2' => $html_2
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error on add / remove retailer category ' . $e->getMessage());
            return response()->json(['status' => false, 'msg' => 'Something went wrong. Please try again later.']);
        }
    }

    // INDEX : my-categories list
    public function myCategoryList()
    {
        $user = Auth::user();
        $retailerCateogries = RetailerCategory::with([
            'retailer',
            'category',
            'subCategory'
        ])
            ->where('retailer_id', $user->id)
            ->get();
        return view('category.mycategorylist', compact('retailerCateogries'));
    }

    // AJAX : server-side data-table for my-categories list
    public function myCategoryListFetchRecord(Request $request)
    {
        $limit = ($request->has('length') ? $request->input('length') : 10);
        $page = ($request->has('start') ? $request->input('start') : 0);
        $search = ($request->has('search') ? $request->input('search')['value'] : '');

        $retailer = Auth::user();

        $query = RetailerCategory::with([
            'category',
            'subCategory'
        ])
            ->where('retailer_id', $retailer->id);

        if (!empty($search)) {
            $search = trim($search);
            $search = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');

            if (isMaliciousSearch($search) || !preg_match('/^[a-zA-Z0-9\s_\-\.@#,:]+$/', $search)) {
                abort(400, 'Invalid search input detected.');
            }
            $query->where(function ($q) use ($search) {
                $q->orWhere('created_at', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('category_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('subCategory', function ($q) use ($search) {
                        $q->where('sub_category_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->has('order') && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];  // get column index
            $columnName = $request->columns[$columnIndex]['data'];  // get column name
            $direction = $request->order[0]['dir'];  // get sort direction (asc or desc)

            $query->orderBy($columnName, $direction);
        } else {
            $query->orderBy('id', 'desc');
        }

        $cntFilter = clone $query;
        $query->offset($page)->limit($limit);
        $myCategories = $query->get();

        $queryTotal = RetailerCategory::with([
            'retailer',
            'category',
            'subCategory'
        ])
            ->where('retailer_id', $retailer->id)
            ->count('id');

        $data = [];
        $i = $page;
        foreach ($myCategories as $key => $item) {
            $i++;

            $sub_category_image = '
                <img src="' . ($item->category_image
                    ? Storage::disk('spaces')->url($item->category_image)
                    : asset('assets/media/images/no_image.jpg')) . '" 
                    onerror="this.onerror=null;this.src=\'' . asset('assets/media/images/no_image.jpg') . '\';"
                    class="w-40px me-3" 
                    alt="sub-category-image" />
            ';

            $action = '
                <button class="btn btn-icon btn-light-danger w-30px h-30px me-3"
                    id="remove-btn"
                    data-category_id="' . $item->category_id . '"
                    data-sub_category="' . $item->sub_category_id . '"
                    data-id="' . $item->id . '"
                    data-bs-toggle="tooltip"
                    aria-label="Delete">
                    <i class="ki-duotone ki-trash fs-3">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        <span class="path4"></span><span class="path5"></span>
                    </i>
                </button>
                <button class="btn btn-icon btn-light-primary w-30px h-30px"
                    id="image-upload"
                    data-id="' . $item->id . '"
                    data-image="' . ($item->category_image
                    ? Storage::disk('spaces')->url($item->category_image)
                    : asset('assets/media/images/no_image.jpg')
                ) . '"
                    data-bs-toggle="tooltip"
                    aria-label="Image Upload">
                    <i class="ki-duotone ki-setting-3 fs-3">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        <span class="path4"></span><span class="path5"></span>
                    </i>
                </button>';

            $data[] = array(
                'sub_category_image' => $sub_category_image,
                'category_name' => strtoupper(optional($item->category)->category_name),
                'sub_category_name' => strtoupper(optional($item->subCategory)->sub_category_name),
                'created_at' => $item->created_at->format('F d, Y, h:i a'),
                'action' => $action,
            );
        }
        return response()->json(array("draw" => $_POST['draw'], "recordsTotal" => $queryTotal, "recordsFiltered" => $cntFilter->count(), 'data' => $data));
    }

    // remove category - from myCategoryList (AJAX)
    public function removeCategory(Request $request)
    {
        $user = Auth::user()->id;
        $category_id = $request->category_id;
        $sub_category = $request->sub_category;

        DB::beginTransaction();
        try {
            $retailerCategory = RetailerCategory::where('retailer_id', $user)->where('category_id', $category_id)->where('sub_category_id', $sub_category)->first();
            if (!$retailerCategory) {
                return response()->json(['status' => false, 'msg' => "Invalid category details or already removed"]);
            }

            $retailerCloneProductExist = RetailerCloneProduct::where('sub_category_id', $sub_category)
                ->where('retailer_id', $user)
                ->exists();
            if ($retailerCloneProductExist) {
                return response()->json(['status' => false, 'msg' => "You can't delete this!, Category is already in use"]);
            }

            if ($retailerCategory->category_image) {
                deleteImageToSpaces($retailerCategory->category_image);
            }
            $retailerCategory->delete();

            // delete margin related to this sub_category
            RetailerProducts::where('sub_category_id', $sub_category)
                ->where('retailer_id', $user)
                ->delete();

            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Removed successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
    }

    // update category image - from myCategoryList (AJAX)
    public function updateCategoryImage(Request $request)
    {
        $request->validate([
            'category_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'retailer_category_id' => 'required|exists:retailer_categories,id'
        ]);

        $id = $request->retailer_category_id;

        DB::beginTransaction();
        try {
            $retailerCategory = RetailerCategory::findOrFail($id);

            // store in digital ocean
            if ($request->hasFile('category_image')) {
                try {
                    $file = $request->file('category_image');
                    $retailerCategory->category_image = uploadOrUpdateImageToSpaces($file, 'retailer_category', $retailerCategory->category_image);
                } catch (\Illuminate\Validation\ValidationException $validationException) {
                    // Handle validation errors
                    Log::error('Category Image Validation Failed: ' . $validationException->getMessage());
                    return back()->withErrors($validationException->errors())->withInput();
                } catch (\Exception $e) {
                    Log::error('Category Image Upload Failed: ' . $e->getMessage());
                    return back()->with('error', 'Category image upload failed.');
                }
            }
            $retailerCategory->save();

            DB::commit();
            return response()->json(['status' => true, 'msg' => 'Image updated successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
    }

    public function categorySuggestion()
    {
        $user = Auth::user();
        $category_suggestion = CategorySuggestion::where('retailer_id', $user->id)->get();
        return view('category.suggestion-box', compact('category_suggestion'));
    }

    public function createCategorySuggestion(Request $request)
    {
        try {
            // Validate Request
            $request->validate([
                'categoryName' => 'required|string|min:3|max:255',
                'subCategoryName' => 'required|string|min:2|max:500',
            ]);

            $user = Auth::user();

            // Begin Database Transaction
            DB::beginTransaction();

            // Insert into CategorySuggestion
            CategorySuggestion::create([
                'category_name' => $request->categoryName,
                'sub_category_name' => $request->subCategoryName,
                'retailer_id' => $user->id,
                'is_approve' => 0
            ]);

            // Commit Transaction
            DB::commit();

            return response()->json([
                'status' => true,
                'success' => 'Suggestion submitted successfully'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'error' => 'Validation Error',
                'messages' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            // Rollback Transaction on Error
            DB::rollBack();

            return response()->json([
                'status' => false,
                'error' => 'Something went wrong!',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteCategorySuggestion(Request $request)
    {
        try {
            $user = Auth::user();
            // Validate the request
            $request->validate([
                'id' => 'required|exists:category_suggestions,id',
            ]);

            DB::beginTransaction();
            // Find and delete the category suggestion
            $deleted = CategorySuggestion::where('id', $request->id)
                ->where('retailer_id', $user->id)
                ->delete();

            // Check if deletion was successful
            if ($deleted) {
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Suggestion deleted successfully'
                ], 200);
            }

            // If record was not found (due to incorrect user ID), return error
            DB::rollBack();
            return response()->json([
                'status' => false,
                'error' => 'Record not found or unauthorized action!'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation Error Handling
            return response()->json([
                'status' => false,
                'error' => 'Validation Error',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // General Error Handling
            DB::rollBack();
            return response()->json([
                'status' => false,
                'error' => 'Something went wrong!',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
