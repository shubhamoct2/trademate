<?php

namespace App\Http\Controllers\Trading;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BitgetController extends Controller
{
    private $apiKey = "bg_41961aa4767ae23b3a7938bd881c216f";
    private $secretKey = "6006d6ef4cf0f960e7244c5c9f5290f1df6b47688fbe5be47248b5e6d656e314";
    private $passPhrase  = "BitgetAntonDidenko0503";
    private $apiUrl  = "https://api.bitget.com";

    public function __construct() {
        
	} 

    public function sendTestRequest(Request $request) {
        // Log::info("Bitget API call:");

        $timestamp = intval(microtime(true) * 1000);
        $method = 'GET';
        $requestPath = '/api/v2/mix/order/orders-history';
        $content = $timestamp . $method . $requestPath;
        
        $payload = hash_hmac('sha512', $content, $this->secretKey);
        $signature = base64_encode($payload);

        $endpoint = $this->apiUrl . $requestPath;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'ACCESS-KEY' => $this->apiKey,
            'ACCESS-SIGN' => $signature,
            'ACCESS-TIMESTAMP' => $timestamp,
            'ACCESS-PASSPHRASE' => $this->passPhrase,
            'locale' => 'en-US',
        ])->get($endpoint)->json();

        dd ($timestamp, $response);
    }
}
