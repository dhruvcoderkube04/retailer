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

class ProductImport implements ToCollection, WithValidation, WithHeadingRow
{


    private $requiredColumns = [
        'product_name',
        'product_description',
        'product_tags',
        'quantity',
        'new_price',
        'sku',
        'image_1',
        'video',
        // 'slug'
    ];

    protected $categoryId;

    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }




    // public function collection(Collection $rows)
    // {
    //     $validRows = [];
    //     $invalidRows = [];
    //     $retailerId = Auth::id();

    //     foreach ($rows as $row) {
    //         $row = $this->map($row); // Map headings correctly

    //         // Skip rows where required data is missing
    //         if (empty($row['product_name']) || empty($row['new_price']) || empty($row['quantity'])) {
    //             $invalidRows[] = $row;
    //             continue;
    //         }

    //         RetailerCloneProduct::updateOrCreate(
    //             [
    //                 'retailer_id' => $retailerId,
    //                 'name' => $row['product_name'], // Match by product_name and retailer_id
    //             ],
    //             [
    //                 'description' => $row['product_description'] ?? null,
    //                 'category_id' => $this->categoryId,
    //                 'tags' => $row['product_tags'] ?? null,
    //                 'slug' => !empty($row['slug'])
    //                     ? Str::slug($row['slug'])
    //                     : Str::slug($row['product_name'] . '-' . uniqid()),  // ✅ Ensure unique slug
    //                 'quantity' => $row['quantity'],
    //                 'new_price' => $row['new_price'],
    //                 'old_price' => 0,
    //                 'status' => 'active',
    //                 'sku' => $row['sku'],
    //                 'image_1' => $row['image_1'] ?? null,
    //                 'video' => $row['video'] ?? null,
    //             ]
    //         );



    //         $validRows[] = $row;
    //     }

    //     return [
    //         'valid' => $validRows,
    //         'invalid' => $invalidRows,
    //     ];
    // }

    // public function collection(Collection $rows)
    // {
    //     $validRows = [];
    //     $invalidRows = [];
    //     $retailerId = Auth::id();

    //     foreach ($rows as $row) {
    //         $row = $this->map($row); // Map headings correctly

    //         if (empty($row['product_name']) || empty($row['new_price']) || empty($row['quantity'])) {
    //             $invalidRows[] = $row;
    //             continue;
    //         }

    //         $originalName = $row['product_name'];
    //         $name = $originalName;
    //         $counter = 1;

    //         // Check for existing product names and modify if needed
    //         while (
    //             RetailerCloneProduct::where('retailer_id', $retailerId)
    //                 ->where('name', $name)
    //                 ->exists()
    //         ) {
    //             $name = $originalName . '-' . $counter;
    //             $counter++;
    //         }

    //         // Update or create with the final unique name
    //         RetailerCloneProduct::updateOrCreate(
    //             [
    //                 'retailer_id' => $retailerId,
    //                 'name' => $name, // Unique name
    //             ],
    //             [
    //                 'description' => $row['product_description'] ?? null,
    //                 'category_id' => $this->categoryId,
    //                 'tags' => $row['product_tags'] ?? null,
    //                 'slug' => !empty($row['slug'])
    //                     ? Str::slug($row['slug'])
    //                     : Str::slug($name . '-' . uniqid()), // Unique slug
    //                 'quantity' => $row['quantity'],
    //                 'new_price' => $row['new_price'],
    //                 'old_price' => 0,
    //                 'status' => 'active',
    //                 'sku' => $row['sku'],
    //                 'image_1' => $row['image_1'] ?? null,
    //                 'video' => $row['video'] ?? null,
    //             ]
    //         );

    //         $row['product_name'] = $name; // Save the final name used
    //         $validRows[] = $row;
    //     }

    //     return [
    //         'valid' => $validRows,
    //         'invalid' => $invalidRows,
    //     ];
    // }


    public function collection(Collection $rows)
    {
        $validRows = [];
        $invalidRows = [];
        $retailerId = Auth::id();
    
        foreach ($rows as $row) {
            $row = $this->map($row);
    
            if (empty($row['product_name']) || empty($row['new_price']) || empty($row['quantity'])) {
                $invalidRows[] = $row;
                continue;
            }
    
            $originalName = $row['product_name'];
    
            // Fetch all matching product names for this retailer
            $existingNames = RetailerCloneProduct::where('retailer_id', $retailerId)
                ->where('name', 'like', $originalName . '%')
                ->pluck('name')
                ->toArray();
    
            // Extract suffix numbers and find the next available one
            $maxSuffix = 0;
            foreach ($existingNames as $existingName) {
                if ($existingName == $originalName) {
                    $maxSuffix = max($maxSuffix, 1); // if exact match, start from 1
                } elseif (preg_match('/^' . preg_quote($originalName, '/') . '-(\d+)$/', $existingName, $matches)) {
                    $maxSuffix = max($maxSuffix, (int)$matches[1] + 1);
                }
            }
    
            // Create unique name
            $finalName = $maxSuffix > 0 ? $originalName . '-' . $maxSuffix : $originalName;
            // dd($finalName);
    
            // Create product
            RetailerCloneProduct::create([
                'retailer_id' => $retailerId,
                'name' => $finalName,
                'description' => $row['product_description'] ?? null,
                'category_id' => $this->categoryId,
                'tags' => $row['product_tags'] ?? null,
                'slug' => !empty($row['slug'])
                    ? Str::slug($row['slug'])
                    : Str::slug($finalName . '-' . uniqid()),
                'quantity' => $row['quantity'],
                'new_price' => $row['new_price'],
                'old_price' => 0,
                'status' => 'active',
                'sku' => $row['sku'],
                'image_1' => $row['image_1'] ?? null,
                'video' => $row['video'] ?? null,
            ]);
    
            $row['product_name'] = $finalName;
            $validRows[] = $row;
        }
    
        return [
            'valid' => $validRows,
            'invalid' => $invalidRows,
        ];
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

    public function checkColumns(array $headings)
    {

        // dd($headings);
        // Convert to lowercase, trim, and clean the headings
        $headingsArray = array_map(function ($heading) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $heading))); // Remove hidden characters
        }, $headings);

        // Check for missing columns
        $missingColumns = [];

        foreach ($this->requiredColumns as $column) {
            if (!in_array(strtolower($column), $headingsArray, true)) {
                $missingColumns[] = $column;
            }
        }

        return empty($missingColumns) ? true : $missingColumns;
    }




    public function map($row): array
    {

        // dd($row);
        return [
            'product_name' => $row['product_name'] ?? null,
            'product_description' => $row['product_description'] ?? null,
            'product_tags' => $row['product_tags'] ?? null,
            'quantity' => $row['quantity'] ?? null,
            'new_price' => $row['new_price'] ?? null,
            'sku' => $row['sku'] ?? null,
            'image_1' => $row['image_1'] ?? null,
            'video' => $row['video'] ?? null,
            'slug' => @$row['slug'] ?? null,
        ];
    }


}
