<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategorySuggestion;
use App\Models\RetailerCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RetailerCategoryController extends Controller
{
    // category-list
    public function categoryList()
    {
        $user = Auth::user();
        $categories = Category::with('subCategory')->orderBy('id', 'desc')->get();
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
                $retailerCategory->delete();
            }

            $categories = Category::with('subCategory')->orderBy('id', 'desc')->get();
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
            return response()->json(['status' => false, 'msg' => $e->getMessage()]);
        }
    }

    // my-categories list
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

    // remove category - from myCategoryList (AJAX)
    public function removeCategory(Request $request)
    {
        $user = Auth::user()->id;
        $category_id = $request->category_id;
        $sub_category = $request->sub_category;

        DB::beginTransaction();
        try {
            RetailerCategory::where('retailer_id', $user)->where('category_id', $category_id)->where('sub_category_id', $sub_category)->delete();
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

            if ($request->hasFile('category_image')) {
                $file = $request->file('category_image');
                $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $uploadPath = public_path('uploads/retailer_category/');

                // Ensure directory exists
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0777, true, true);
                }

                // Delete old image if it exists
                if (!empty($retailerCategory->category_image)) {
                    $oldImagePath = public_path('uploads/' . $retailerCategory->category_image);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }

                // Move new image to directory
                $file->move($uploadPath, $fileName);
                $retailerCategory->category_image = "retailer_category/" . $fileName;
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
