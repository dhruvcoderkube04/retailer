<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\RetailerCloneProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessRetailerPendingImagesLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-retailer-pending-images-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store images URLs (http) found in RetailerCloneProduct into bucket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingRetailerProducts = RetailerCloneProduct::whereRaw("JSON_UNQUOTE(images) LIKE 'http%'")
            ->limit(100)
            ->get();

        $this->info('Retailer Products image links founds : ' . $pendingRetailerProducts->count());

        foreach ($pendingRetailerProducts as $product) {
            if ($product->images && Str::startsWith($product->images, 'http')) {
                $uploadedPaths = $this->imagesProcess($product->images, $product->id);
                if (!empty($uploadedPaths)) {
                    $product->update(['images' => implode(',', $uploadedPaths)]);
                }
            }
        }

        $this->info('Pending RetailerCloneProduct image links processed successfully.');
    }


    private function imagesProcess($images, $product_id)
    {
        $imageUrls = explode(',', $images);
        $uploadedPaths = [];

        foreach ($imageUrls as $url) {
            try {
                $url = trim(str_replace(['\\"', '"'], '', $url)); // Clean up possible escaped JSON strings

                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    Log::warning("Invalid RetailerCloneProduct image URL: $url for product ID: {$product_id}");
                    continue;
                }

                $response = Http::timeout(10)->get($url);
                if ($response->successful()) {
                    $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                    $filename = now()->timestamp . '_' . Str::random(6) . '.' . $extension;
                    $path = 'products/images/' . $filename;

                    Storage::disk('spaces')->put($path, $response->body(), 'public');
                    $uploadedPaths[] = $path;
                } else {
                    Log::error("RetailerCloneProduct Image download failed: $url for product ID: {$product_id}");
                }
            } catch (\Exception $e) {
                Log::error("RetailerCloneProduct Image error for product ID {$product_id}, URL: $url - " . $e->getMessage());
            }
        }

        return $uploadedPaths;
    }
}
