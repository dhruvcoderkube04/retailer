<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\RetailerCloneProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessRetailerPendingVideosLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-retailer-pending-videos-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store video URLs (http) found in RetailerCloneProduct into bucket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingRetailerProducts = RetailerCloneProduct::whereRaw("JSON_UNQUOTE(videos) LIKE 'http%'")
            ->limit(10)
            ->get();

        $this->info('Retailer Products video links founds : ' . $pendingRetailerProducts->count());

        foreach ($pendingRetailerProducts as $product) {
            if ($product->videos && Str::startsWith($product->videos, 'http')) {
                $videoUrl = $this->videosProcess($product->videos, $product->id);
                if (!empty($videoUrl)) {
                    $product->update(['videos' => $videoUrl]);
                }
            }
        }

        $this->info('Pending RetailerCloneProduct videos links processed successfully.');
    }

    private function videosProcess($videos, $product_id)
    {
        $videos = trim(str_replace(['\\"', '"'], '', $videos)); // Clean up if JSON-escaped
        $path = null;

        try {
            if (!filter_var($videos, FILTER_VALIDATE_URL)) {
                Log::warning("Invalid RetailerCloneProduct video URL: $videos for product ID: {$product_id}");
            } else {
                $response = Http::withOptions([
                    'timeout' => 300,
                    'connect_timeout' => 120,
                    'verify' => false,
                ])->get($videos);

                if ($response->successful()) {
                    $extension = pathinfo(parse_url($videos, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'mp4';
                    $filename = now()->timestamp . '_' . Str::random(6) . '.' . $extension;
                    $path = 'products/videos/' . $filename;

                    Storage::disk('spaces')->put($path, $response->body(), 'public');
                } else {
                    Log::error("RetailerCloneProduct Video download failed: $videos for product ID: {$product_id}");
                }
            }
        } catch (\Exception $e) {
            Log::error("RetailerCloneProduct Video error for product ID {$product_id}, URL: $videos - " . $e->getMessage());
        }

        return $path;
    }
}
