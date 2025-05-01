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
        'old_price',
        // 'sku',
        'images',
        'images1',
        'videos',
        // 'slug'
    ];

    protected $categoryId;

    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function collection(Collection $rows)
    {
        $validRows = [];
        $invalidRows = [];
        $retailerId = Auth::id();

        foreach ($rows as $row) {
            $row = $this->map($row);

            $allImages = [];
            // Add the primary image if available
            if (!empty($row['images'])) {
                $allImages[] = $row['images'];
            }

            // Add additional images from 'images1' if available
            if (!empty($row['images1'])) {
                $extraImages = explode('|', $row['images1']);
                $allImages = array_merge($allImages, $extraImages);
            }

            // Prepare video if available
            $videoUrl = !empty($row['videos']) ? $row['videos'] : null;


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
                'old_price' => $row['old_price'],
                'status' => 'active',
                'sku' => !empty($row['sku']) ? $row['sku'] : (string) Str::uuid(),
                // 'sku' => $row['sku'] ,
                'images' => implode(',', $allImages),
                'videos' => $videoUrl,
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
            'old_price' => $row['old_price'] ?? null,
            'sku' => $row['sku'] ?? null,
            'images' => $row['images'] ?? null,
            'images1' => $row['images1'] ?? null,
            'videos' => $row['videos'] ?? null,
            'slug' => @$row['slug'] ?? null,
        ];
    }


}
