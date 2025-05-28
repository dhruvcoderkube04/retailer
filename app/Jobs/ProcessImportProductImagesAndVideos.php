<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\RetailerCloneProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessImportProductImagesAndVideos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;          // Retry 3 times on failure
    public $timeout = 120;      // Max execution time in seconds

    protected $productId;

    /**
     * Create a new job instance.
     */
    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $product = RetailerCloneProduct::find($this->productId);

        if (!$product) {
            Log::warning("Product not found for ID: {$this->productId}");
            return;
        }

        if ($product->images) {
            $imageUrls = explode(',', $product->images);
            $uploadedPaths = [];

            foreach ($imageUrls as $url) {
                try {
                    if (!filter_var($url, FILTER_VALIDATE_URL)) {
                        Log::warning("Invalid image URL: $url for product ID: {$this->productId}");
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
                        Log::error("Image download failed: $url for product ID: {$this->productId}");
                    }
                } catch (\Exception $e) {
                    Log::error("Image error for product ID {$this->productId}, URL: $url - " . $e->getMessage());
                }
            }

            if (!empty($uploadedPaths)) {
                $product->update(['images' => implode(',', $uploadedPaths)]);
            }
        }

        if ($product->videos) {
            try {
                $videoUrl = $product->videos;

                if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
                    Log::warning("Invalid video URL: $videoUrl for product ID: {$this->productId}");
                } else {
                    $response = Http::withOptions([
                        'timeout' => 180,
                        'connect_timeout' => 30,
                        'verify' => false,
                    ])->get($videoUrl);
                    if ($response->successful()) {
                        $extension = pathinfo(parse_url($videoUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'mp4';
                        $filename = now()->timestamp . '_' . Str::random(6) . '.' . $extension;
                        $path = 'products/videos/' . $filename;

                        Storage::disk('spaces')->put($path, $response->body(), 'public');
                        $product->update(['videos' => $path]);
                    } else {
                        Log::error("Video download failed: $videoUrl for product ID: {$this->productId}");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Video error for product ID {$this->productId}, URL: $videoUrl - " . $e->getMessage());
            }
        }
    }
}
