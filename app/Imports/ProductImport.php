<?php

namespace App\Imports;

use App\Mail\FailedProductImportDetailsMail;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\RetailerCloneProduct;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

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

    protected $sub_category;
    protected $subcategoryId;
    protected $images_and_video_update;

    public function __construct($subcategoryId, $images_and_video_update, $sub_category)
    {
        $this->subcategoryId = $subcategoryId;
        $this->images_and_video_update = $images_and_video_update;
        $this->sub_category = $sub_category;
    }

    public function collection(Collection $rows)
    {
        $validRows = [];
        $invalidRows = [];
        $currentDateTime = Carbon::now();
        $importStartDateTime = $currentDateTime->format('F d, Y h:i A');
        $sub_category = $this->sub_category; // sub-category
        $retailer = Auth::user();

        if ($sub_category->sub_category_variation) {
            $this->requiredColumns = array_diff($this->requiredColumns, ['quantity', 'new_price', 'old_price']);
            $this->requiredColumns[] = 'variations';
        }

        foreach ($rows as $index => $row) {
            DB::beginTransaction();
            $row = $this->map($row);
            $rowErrors = [];

            //<-------- START : validation ---------->
            $missing = [];
            foreach ($this->requiredColumns as $field) {
                $value = $row[$field] ?? null;

                if ($field === 'quantity') {
                    if (!array_key_exists($field, $row) || $value === null || (string)$value === '') {
                        $missing[] = $field;
                    }
                } elseif ($sub_category->sub_category_variation && $field === 'variations') {
                    if (empty(trim((string) $value))) {
                        $missing[] = $field;
                    } else {
                        $variationsRaw = str_replace(['“', '”', '‘', '’'], ['"', '"', "'", "'"], $value);
                        $variationData = json_decode($variationsRaw, true);

                        if (json_last_error() !== JSON_ERROR_NONE || !is_array($variationData)) {
                            $rowErrors[] = "Invalid data entered in variations field.";
                        } else {
                            $requiredKeys = ['size', 'old_price', 'new_price', 'quantity'];
                            $allowedSizes = explode(',', $sub_category->sub_category_variation);

                            foreach ($variationData as $vIndex => $variation) {
                                foreach ($requiredKeys as $reqKey) {
                                    if (!isset($variation[$reqKey]) || trim((string)$variation[$reqKey]) === '') {
                                        $rowErrors[] = "Variation index " . ($vIndex + 1) . " missing '$reqKey'.";
                                    }
                                }

                                if (!in_array($variation['size'], $allowedSizes)) {
                                    $rowErrors[] = "Variation index " . ($vIndex + 1) . " has invalid size '{$variation['size']}'. Allowed sizes: " . implode(', ', $allowedSizes);
                                }
                            }
                        }
                    }
                } else {
                    if (empty(trim((string) $value))) {
                        $missing[] = $field;
                    }
                }
            }

            if (!empty($missing)) {
                $rowErrors[] = "Missing fields - " . implode(', ', $missing);
            }

            // Duplicate product check
            $productExist = RetailerCloneProduct::where('name', $row['product_name'])
                ->where('retailer_id', $retailer->id)
                ->exists();
            if ($productExist) {
                $rowErrors[] = "Already exists - {$row['product_name']} is already exist.";
            }

            // If any error found, rollback and collect all together
            if (!empty($rowErrors)) {
                DB::rollBack();
                $invalidRows[] = "Row " . ($index + 2) . ": " . implode(' | ', array_unique($rowErrors));
                continue;
            }
            //<-------- END : validation ---------->

            try {
                // images
                $allImages = array_filter(array_merge(
                    [$row['images']],
                    isset($row['images1']) ? explode('|', $row['images1']) : []
                ));

                // sku
                do {
                    $sku = str_pad(mt_rand(111, 99999999999999), 14, '0', STR_PAD_LEFT);
                } while (
                    Product::where('sku', $sku)->exists() ||
                    RetailerCloneProduct::where('sku', $sku)->exists()
                );

                // slug
                $slug = Str::slug($row['product_name']) . '-' . now()->timestamp . '-' . uniqid();

                // tags
                $tags = collect(explode(',', $row['product_tags'] ?? ''))
                    ->map(fn($tag) => ['value' => trim($tag)])
                    ->values()
                    ->toJson();

                $product = new RetailerCloneProduct();
                $product->retailer_id = $retailer->id;
                $product->name = $row['product_name'];
                $product->sku = $sku;
                $product->slug = $slug;
                $product->description = $row['product_description'] ?? null;
                $product->tags = $tags;
                $product->quantity = $sub_category->sub_category_variation ? 0 : $row['quantity'];
                $product->new_price = $sub_category->sub_category_variation ? null : $row['new_price'];
                $product->old_price = $sub_category->sub_category_variation ? null : $row['old_price'];
                $product->status = $row['status'];
                $product->category_id = $sub_category->category_id;
                $product->sub_category_id = $sub_category->id;
                $product->meta_title = $row['meta_title'] ?? null;
                $product->meta_description = $row['meta_description'] ?? null;
                $product->meta_keywords = $row['meta_keywords'] ?? null;
                $product->images = implode(',', $allImages);
                $product->videos = $row['videos'] ?? null;
                // if ($this->images_and_video_update || !$product->exists) {
                //     $product->images = implode(',', $allImages);
                //     $product->videos = $row['videos'] ?? null;
                // }
                $product->save();

                // Store variations
                $variationsRaw = str_replace(['“', '”', '‘', '’'], ['"', '"', "'", "'"], $value);
                $variationData = json_decode($variationsRaw, true);
                if (!empty($variationData)) {
                    foreach ($variationData as $index => $variation_detail) {
                        ProductVariation::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'product_variation' => $variation_detail['size'],
                            ],
                            [
                                'old_price' => $variation_detail['old_price'],
                                'price' => $variation_detail['new_price'],
                                'stock' => $variation_detail['quantity'] ?? 0,
                            ]
                        );
                    }
                }

                DB::commit();
                $validRows[] = $row;
            } catch (\Exception $e) {
                DB::rollBack();
                $invalidRows[] = "Row " . ($index + 2) . ": Failed to import due to internal error.";
            }
        }

        if (count($invalidRows)) {
            Mail::to($retailer->email)->send(new FailedProductImportDetailsMail($invalidRows, $validRows, $importStartDateTime));
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

        if ($this->sub_category->sub_category_variation) {
            $this->requiredColumns = array_diff($this->requiredColumns, ['quantity', 'new_price', 'old_price']);
            $this->requiredColumns[] = 'variations';
        }

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
            'variations' => $row['variations'] ?? null,
            'meta_title' => $row['meta_title'] ?? null,
            'meta_description' => $row['meta_description'] ?? null,
            'meta_keywords' => $row['meta_keywords'] ?? null,
        ];
    }
}
