<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('encryptId')) {
   function encryptId($id)
   {
      $key = 'ZLCvrkbzipyCbam8Up+xWSo2Ky7GtBFZkEQWcIYYGM0=';
      return base64_encode(openssl_encrypt($id, "AES-256-CBC", $key, 0, substr(hash('sha256', $key), 0, 16)));
   }
}

if (!function_exists('decryptId')) {
   function decryptId($encryptedId)
   {
      $key = 'ZLCvrkbzipyCbam8Up+xWSo2Ky7GtBFZkEQWcIYYGM0=';
      return openssl_decrypt(base64_decode($encryptedId), "AES-256-CBC", $key, 0, substr(hash('sha256', $key), 0, 16));
   }
}

//<-------------------- START : For Order List ------------------->
if (!function_exists('order_status')) {
   function order_status($value)
   {
      $statuses = [
         'pending' => 'Pending',
         'approved_by_retailer' => 'Approved By Retailer',
         'transfered_retailer_to_wholesaler' => 'Transferred To Wholesaler',
         'approved_by_wholesaler' => 'Confirmed By Wholesaler',
         'pickup' => 'Pickup',
         'in_transit' => 'In Transit',
         'ofd' => 'OFD',
         'ndr' => 'NDR',
         'delivered' => 'Delivered',
         'rto' => 'RTO',
         'rtn_to_seller' => 'RTN To Seller',
         'close' => 'Close',
         'cancel' => 'Cancelled',
         'lost' => 'Lost',
         'received' => 'Received',
      ];

      return $statuses[$value] ?? 'Unknown Status';
   }
}
//<-------------------- END : For Order List ------------------->

//<---------------------- START : For Image -------------------------->
if (!function_exists('uploadOrUpdateImageToSpaces')) {
   function uploadOrUpdateImageToSpaces($file, $directory = 'uploads', $oldImagePath = null)
   {
      if (!$file || !$file->isValid()) {
         return $oldImagePath;
      }

      // Delete old image if exists
      if ($oldImagePath && Storage::disk('spaces')->exists($oldImagePath)) {
         Storage::disk('spaces')->delete($oldImagePath);
      }

      // Generate new filename and path
      $extension = $file->getClientOriginalExtension();
      $filename = now()->timestamp . '_' . Str::random(6) . '.' . $extension;
      $path = $directory . '/' . $filename;

      // Upload new image
      Storage::disk('spaces')->putFileAs($directory, $file, $filename, 'public');

      return $path;
   }
}
if (!function_exists('deleteImageToSpaces')) {
   function deleteImageToSpaces($oldImagePath)
   {
      Log::info('Attempting to delete image from Spaces:', ['path' => $oldImagePath]);

      if ($oldImagePath && Storage::disk('spaces')->exists($oldImagePath)) {
         $deleted = Storage::disk('spaces')->delete($oldImagePath);
         Log::info('Image deleted status: ' . ($deleted ? 'success' : 'failed'));
      } else {
         Log::warning('File does not exist in Spaces:', ['path' => $oldImagePath]);
      }
   }
}
//<---------------------- END : For Image -------------------------->

//<---------------------- START : For Video -------------------------->
if (!function_exists('uploadOrUpdateVideoToSpaces')) {
   function uploadOrUpdateVideoToSpaces($file, $directory = 'uploads/videos', $oldVideoPath = null)
   {
      if (!$file || !$file->isValid()) {
         return $oldVideoPath;
      }

      // Delete old video if it exists
      if ($oldVideoPath && Storage::disk('spaces')->exists($oldVideoPath)) {
         Storage::disk('spaces')->delete($oldVideoPath);
      }

      // Generate new filename and path
      $extension = $file->getClientOriginalExtension();
      $filename = now()->timestamp . '_' . Str::random(6) . '.' . $extension;
      $path = $directory . '/' . $filename;

      // Upload new video with correct content type
      $mimeType = $file->getMimeType(); // Example: video/mp4, video/avi
      $content = file_get_contents($file);

      Storage::disk('spaces')->put($path, $content, [
         'visibility' => 'public',
         'ContentType' => $mimeType,
      ]);

      return $path;
   }
}
//<---------------------- END : For Video -------------------------->


//<---------------------- START : For  Block known SQL keywords or suspicious patterns -------------------------->


if (!function_exists('isMaliciousSearch')) {
   function isMaliciousSearch($input)
   {
       $input = strtolower(trim($input));

       // Allow common special characters like @ . - _
       // Still block SQLi or XSS attempts
       $blacklistPatterns = [
           '/\bselect\b/',
           '/\binsert\b/',
           '/\bupdate\b/',
           '/\bdelete\b/',
           '/\bdrop\b/',
           '/\btruncate\b/',
           '/\bexec\b/',
           '/\bunion\b/',
           '/\b(or|and)\b\s+\d+=\d+/', // typical SQL injection pattern like 'OR 1=1'
           '/--/',               // SQL comment
           '/;/',                // command chaining
           '/#(?![a-zA-Z0-9])/', // hash not part of normal word
           '/\/\*/',             // start comment
           '/\*\//',             // end comment
           '/<script\b[^>]*>(.*?)<\/script>/is', // XSS
       ];

       foreach ($blacklistPatterns as $pattern) {
           if (preg_match($pattern, $input)) {
               return true;
           }
       }

       return false;
   }
}


//<---------------------- END : For  Block known SQL keywords or suspicious patterns -------------------------->



   if (!function_exists('sanitize_input')) {
      function sanitize_input($input)
      {
         // Remove <script> tags
         $input = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $input);

         // Remove on-event handlers (e.g., onclick="...", onerror=...)
         $input = preg_replace('/on\w+="[^"]*"/i', '', $input);
         $input = preg_replace("/on\w+='[^']*'/i", '', $input);

         $input = preg_replace('/<\?(php)?(.*?)\?>/is', '', $input);

         // Remove all HTML tags (for plain text only — optional)
         $input = strip_tags($input);

         return trim($input);
      }
   }

   if (!function_exists('cleanInput')) {
      function cleanInput($input)
      {
         // 1. Remove script tags
         $input = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $input);

         // 2. Remove all event handlers like onclick, onerror, onload
         $input = preg_replace('/on\w+="[^"]*"/i', '', $input); // double quotes
         $input = preg_replace("/on\w+='[^']*'/i", '', $input); // single quotes

         // 3. Remove javascript:, data: and vbscript: protocols
         $input = preg_replace('/(javascript:|data:|vbscript:)/i', '', $input);

         // 4. Remove iframe, embed, object, and other dangerous tags
         $input = preg_replace('#<(iframe|embed|object|applet|meta|link|style|base)[^>]*>.*?</\1>#is', '', $input);
         $input = preg_replace('#<(iframe|embed|object|applet|meta|link|style|base)[^>]*>#is', '', $input);

         // 5. Remove remaining HTML tags (optional — keep if you want plain text)
         $input = strip_tags($input);

         // 6. Trim spaces
         return trim($input);
      }
   }

