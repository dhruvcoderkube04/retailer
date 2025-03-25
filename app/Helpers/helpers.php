<?php 

// if (!function_exists('order_status')) {
//     function order_status($value) {
//         $statuses = [
//             'pending' => 'Pending',
//             'transfered_retailer_to_wholesaler' => 'Transferred To Wholesaler',
//             'confirmed_by_retailer' => 'Confirmed By Retailer',
//             'confirmed_by_wholesaler' => 'Confirmed By Wholesaler',
//             'shipped_by_retailer' => 'Shipped By Retailer',
//             'shipped_by_wholesaler' => 'Shipped By Wholesaler',
//             'delivered_by_retailer' => 'Delivered By Retailer',
//             'delivered_by_wholesaler' => 'Delivered By Wholesaler',
//             'cancelled_by_customer' => 'Cancelled By Customer',
//             'cancelled_by_retailer' => 'Cancelled By Retailer',
//             'cancelled_by_wholesaler' => 'Cancelled By Wholesaler',
//             'received' => 'Received'
//         ];

//         return $statuses[$value] ?? 'Unknown Status';
//     }
// }

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

?>