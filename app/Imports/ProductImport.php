<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\RetailerCloneProduct;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImport implements ToCollection, WithValidation, WithHeadingRow
{

    private $requiredColumns = [
        'product_name',
        'quantity',
        'new_price',
        'old_price',
        'status',
        'images',
    ];

    protected $subcategoryId;
    protected $images_and_video_update;

    public function __construct($subcategoryId, $images_and_video_update)
    {
        $this->subcategoryId = $subcategoryId;
        $this->images_and_video_update = $images_and_video_update;
    }

    public function collection(Collection $rows)
    {
        $validRows = [];
        $invalidRows = [];
        $retailer = Auth::user();

        $sub_category = SubCategory::where('id', $this->subcategoryId)->first();

        foreach ($rows as $index => $row) {
            DB::beginTransaction();
            $row = $this->map($row);

            //<-------- START : Missing data validation ---------->
            $missing = [];
            foreach ($this->requiredColumns as $field) {
                $value = $row[$field] ?? null;
                if ($field === 'quantity') {
                    if (!array_key_exists($field, $row) || $value === null || (string)$value === '') {
                        $missing[] = $field;
                    }
                } else {
                    if (empty(trim((string) $value))) {
                        $missing[] = $field;
                    }
                }
            }
            if (!empty($missing)) {
                DB::rollBack();
                $invalidRows[] = "Row " . ($index + 2) . ": Missing - " . implode(', ', $missing);
                continue;
            }
            //<-------- END : Missing data validation ---------->

            // images
            $allImages = array_filter(array_merge(
                [$row['images']],
                isset($row['images1']) ? explode('|', $row['images1']) : []
            ));

            // sku
            $sku = $this->generateUniqueSku();

            // slug
            $slug = Str::slug($row['product_name']) . '-' . now()->timestamp . '-' . uniqid();

            // tags
            $tagsString = $row['product_tags'] ?? null;
            $tags = collect(explode(',', $tagsString))
                ->map(function ($tag) {
                    return ['value' => trim($tag)];
                })
                ->values()
                ->toJson();

            $product = RetailerCloneProduct::firstOrNew([
                'retailer_id' => $retailer->id,
                'name' => $row['product_name'],
            ]);

            $product->sku = $sku;
            $product->slug = $slug;
            $product->description = $row['product_description'] ?? null;
            $product->tags = $tags;
            $product->quantity = $row['quantity'];
            $product->new_price = $row['new_price'];
            $product->old_price = $row['old_price'];
            $product->status = $row['status'];
            $product->category_id = $sub_category->category_id;
            $product->sub_category_id = $sub_category->id;
            $product->meta_title = $row['meta_title'] ?? null;
            $product->meta_description = $row['meta_description'] ?? null;
            $product->meta_keywords = $row['meta_keywords'] ?? null;
            if ($this->images_and_video_update || !$product->exists) {
                $product->images = implode(',', $allImages);
                $product->videos = $row['videos'] ?? null;
            }
            $product->save();

            $validRows[] = $row;
            DB::commit();
        }

        return [
            'valid' => $validRows,
            'invalid' => $invalidRows,
        ];
    }

    private function generateUniqueSku()
    {
        do {
            $sku = str_pad(mt_rand(111, 99999999999999), 14, '0', STR_PAD_LEFT);
        } while (RetailerCloneProduct::where('sku', $sku)->exists());

        return $sku;
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'new_price' => 'required|numeric',
            'old_price' => 'required|numeric',
            'status' => 'required|string',
            'images' => 'required|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'product_name.required' => 'Product Name is required.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
            'new_price.required' => 'New Price is required.',
            'new_price.numeric' => 'New Price must be a number.',
            'old_price.required' => 'Old Price is required.',
            'old_price.numeric' => 'Old Price must be a number.',
            'status.required' => 'Status is required.',
            'images.required' => 'Images are required.',
        ];
    }

    public function checkColumns(array $headings)
    {
        $headings = array_map(fn($h) => strtolower(trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $h))), $headings);

        $missing = [];
        foreach ($this->requiredColumns as $column) {
            if (!in_array($column, $headings, true)) {
                $missing[] = $column;
            }
        }

        return empty($missing) ? true : $missing;
    }

    public function map($row): array
    {
        return [
            'product_name' => $row['product_name'] ?? null,
            'product_tags' => $row['product_tags'] ?? null,
            'quantity' => $row['quantity'] ?? null,
            'new_price' => $row['new_price'] ?? null,
            'old_price' => $row['old_price'] ?? null,
            'status' => $row['status'] ?? null,
            'product_description' => $row['product_description'] ?? null,
            'images' => $row['images'] ?? null,
            'images1' => $row['images1'] ?? null,
            'videos' => $row['videos'] ?? null,
            'meta_title' => $row['meta_title'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'meta_keywords' => $row['meta_keywords'] ?? null,
        ];
    }
}
