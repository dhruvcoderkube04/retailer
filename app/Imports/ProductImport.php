<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;

class ProductImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $requiredColumns = ['product_name', 'product_description', 'product_tags','quantity','new_price','sku','image_1','image_2','image_3','video']; // Define your required columns

    public function collection(Collection $rows)
    {
        $validRows = [];
        $invalidRows = [];

        foreach ($rows as $row) {
            $validator = Validator::make($row->toArray(), [
                'product_name' => 'required|string',
                'new_price' => 'required|numeric',
                'quantity' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $invalidRows[] = [
                    'row' => $row->toArray(),
                    'errors' => $validator->errors()->toArray(),
                ];
            } else {
                $validRows[] = $row->toArray();
                // Perform further processing of valid rows if needed
            }
        }

        return ['valid' => $validRows, 'invalid' => $invalidRows];
    }

    public function headingRow(): int
    {
        return 1; // Start reading data from the first row (headers)
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

    public function checkColumns(Collection $headings)
    {
        foreach ($this->requiredColumns as $column) {
            if (!$headings->contains($column)) {
                return false; // Column is missing
            }
        }
        return true; // All required columns are present
    }
}
