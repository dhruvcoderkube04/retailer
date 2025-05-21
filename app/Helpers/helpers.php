<?php 

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



?>