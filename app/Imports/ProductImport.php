<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\RetailerCloneProduct;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str;

class ProductImport implements ToCollection, WithHeadingRow, WithValidation
{

    // private $requiredColumns = ['product_name', 'product_description', 'product_tags','quantity','new_price','sku','image_1','image_2','image_3','video','slug']; // Define your required columns

    protected $categoryId;

    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }


    // public function collection(Collection $rows)
    // {
    //     $validRows = [];

    //     $retailerId = Auth::id();

    //     foreach ($rows as $row) {
    //         // Your existing logic for unique slug and sku
    //         $slug = isset($row['product_name']) ? Str::slug($row['product_name']) : 'default-slug'; 
    //         $originalSlug = $slug;
    //         $counter = 1;
        
    //         // Ensure unique slug
    //         while (RetailerCloneProduct::where('slug', $slug)->exists()) {
    //             $slug = $originalSlug . '-' . $counter;
    //             $counter++;
    //         }
        
    //         $sku = $row['sku'] ?? null;
    //         $originalSku = $sku;
    //         $skuCounter = 1;
        
    //         // Ensure unique SKU
    //         while (RetailerCloneProduct::where('sku', $sku)->exists()) {
    //             $sku = $originalSku . '-' . $skuCounter;
    //             $skuCounter++;
    //         }
        
    //         RetailerCloneProduct::create([
    //             'retailer_id' => $retailerId,
    //             'name' => $row['product_name'],
    //             'description' => $row['product_description'] ?? null,
    //             'category_id' => $this->categoryId,
    //             'tags' => $row['product_tags'] ?? null,
    //             'slug' => $slug,
    //             'quantity' => $row['quantity'],
    //             'new_price' => $row['new_price'],
    //             'old_price' => 0,
    //             'sku' =>  $sku,
    //             'image_1' => $row['image_1'] ?? null,
    //             'image_2' => $row['image_2'] ?? null,
    //             'image_3' => $row['image_3'] ?? null,
    //             'video' => $row['video'] ?? null,
    //         ]);
        
    //         $validRows[] = $row; // ✅ Fixed: No need for ->toArray()
    //     }
        

    //     return ['valid' => $validRows];
    // }


    public function collection(Collection $rows)
    {
        $validRows = [];
        $invalidRows = [];
    
        $retailerId = Auth::id();
    
        foreach ($rows as $row) {
            // Unique slug generate કરો
            $slug = isset($row['product_name']) ? Str::slug($row['product_name']) : 'default-slug';
            $originalSlug = $slug;
            $counter = 1;
        
            while (RetailerCloneProduct::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        
            // Unique SKU Generate કરો
            $sku = $row['sku'] ?? null;
            $originalSku = $sku;
            $skuCounter = 1;
        
            while (RetailerCloneProduct::where('sku', $sku)->exists()) {
                $sku = $originalSku . '-' . $skuCounter;
                $skuCounter++;
            }
        
            // Now Insert Data
            RetailerCloneProduct::create([
                'retailer_id' => $retailerId,
                'name' => $row['product_name'],
                'description' => $row['product_description'] ?? null,
                'category_id' => $this->categoryId,
                'tags' => $row['product_tags'] ?? null,
                'slug' => $slug, // Unique Slug
                'quantity' => $row['quantity'],
                'new_price' => $row['new_price'],
                'old_price' => 0,
                'sku' => $sku, // Unique SKU
                'image_1' => $row['image_1'] ?? null,
                'image_2' => $row['image_2'] ?? null,
                'image_3' => $row['image_3'] ?? null,
                'video' => $row['video'] ?? null,
            ]);
        
            $validRows[] = $row;
        }
        
    
        return [
            'valid' => $validRows,
            'invalid' => $invalidRows, // ✅ Now returns invalid rows properly
        ];
    }
    


    public function headingRow(): int
    {
        return 1; // Start reading from the first row
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string',
            'new_price' => 'required|numeric',
            'quantity' => 'required|integer',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'product_name.required' => 'Product Name is required.',
            'new_price.required' => 'Price is required.',
            'new_price.numeric' => 'Price must be a number.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
        ];
    }

    // public function checkColumns(Collection $headings)
    // {
    //     // Extract only the column names (keys)
    //     $headingsArray = array_keys($headings->toArray());
    
    //     // Normalize column names: lowercase, trim, remove hidden characters
    //     $headingsArray = array_map(function ($heading) {
    //         return strtolower(trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $heading))); // Remove hidden characters
    //     }, $headingsArray);
    
    //     $missingColumns = [];
    
    //     foreach ($this->requiredColumns as $column) {
    //         if (!in_array(strtolower($column), $headingsArray, true)) {
    //             $missingColumns[] = $column; // Store missing column name
    //         }
    //     }
    
    //     return empty($missingColumns) ? true : $missingColumns; // Return missing columns if any
    // }
    
    
    
    
}
