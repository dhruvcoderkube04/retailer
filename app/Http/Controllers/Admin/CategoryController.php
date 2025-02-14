<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Display the category management page
    public function categoryPage(){
        return view('admin.category.add-category');
    }

    // Store a new category
    public function postCategory(Request $request){

        $request->merge([
            'category_name' => strtolower($request->input('category_name')),
            'sub_category' => array_map('trim', explode(',', strtolower($request->input('sub_category')))),
            'status' => $request->boolean('status')
        ]);

        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'sub_category' => 'required|array',
            'sub_category.*' => 'required|string|max:255',
            'status' => 'required|boolean'
        ]);

        // Save to the database
        Category::create([
            'category_name' => $request->category_name,
            'sub_category_name' => json_encode($request->sub_category), // Store as JSON
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Category added successfully!');
    }

    // Retrieve category details
    public function categoryDetail($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.category-detail',['category' => $category ]);
    }

    // Update an existing category
    public function categoryUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'status' => 'nullable|boolean'
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'status' => $request->status ?? 1
        ]);

        return redirect()->route('admin.category.detail', $category->id)->with('success', 'Category updated successfully!');

        // return redirect()->back()->with('success', 'Category updated successfully!');
    }

    // Get list of all categories
    public function categoryList()
    {
        $categories = Category::orderBy('id', 'desc')->get();
        return view('admin.category.category-list', compact('categories'));
    }

   // Delete a category
   public function deleteCategory($id)
   {
       $category = Category::findOrFail($id);
       $category->delete();

       return redirect()->back()->with('success', 'Category deleted successfully!');
   }

}
