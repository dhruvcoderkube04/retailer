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

?>