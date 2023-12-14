<?php
/**
 * AlphaPo Class
 * author Artem
 */
namespace App\Libraries;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AlphaPo {
    private $setting;

    public function __construct() {
        if (config('app.env') === 'production') {
            $this->setting = config('alphapo.prod');
        } else {
            $this->setting = config('alphapo.sandbox');
        }
	} 

    public function getCurrencies() {
        $endpoint = $this->setting['url'] . '/currencies/list';
        $params = ["visible" => true];
        $requestBody = json_encode($params);
        $signature = hash_hmac('sha512', $requestBody, $this->setting['secret']);

        $response = Http::withHeaders([
            'Content-type' => 'application/json',
            'X-Processing-Key' => $this->setting['key'],
            'X-Processing-Signature' => $signature,
        ])->post($endpoint, $params)->json();

        return $response;
    }

    public function createDepositAddress($params) {
        $endpoint = $this->setting['url'] . '/addresses/take';

        $validator = Validator::make($params, [
            'currency' => ['required', 'string', 'min:3', 'max:5'],
            'foreign_id' => ['required', 'exists:App\Models\User,id'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()
            ];
        }

        $inputData = $validator->validated();
        $inputData['foreign_id'] = strval($inputData['foreign_id']);

        $requestBody = json_encode($inputData);
        $signature = hash_hmac('sha512', $requestBody, $this->setting['secret']);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Processing-Key' => $this->setting['key'],
            'X-Processing-Signature' => $signature,
        ])->post($endpoint, $inputData)->json();
        
        return $response;
    }

    public function createWithdrawRequest($params) {
        $endpoint = $this->setting['url'] . '/withdrawal/crypto';

        $validator = Validator::make($params, [
            'currency' => ['required', 'string', 'min:3', 'max:5'],
            'foreign_id' => ['required', 'exists:App\Models\User,id'],
            'address' => ['required', 'string'],
            'amount' => ['required'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()
            ];
        }

        $inputData = $validator->validated();
        $inputData['foreign_id'] = strval($inputData['foreign_id']) . '-' . Str::random(36);;

        $requestBody = json_encode($inputData);
        $signature = hash_hmac('sha512', $requestBody, $this->setting['secret']);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Processing-Key' => $this->setting['key'],
            'X-Processing-Signature' => $signature,
        ])->post($endpoint, $inputData)->json();
        
        return $response;
    }

    public function getCryptoPrice($code) {
        $url = 'https://bitpay.com/api/rates';
        $json = json_decode(file_get_contents($url));

        $usd = 1;
        $btc = 0;
        $price = 0;

        foreach( $json as $obj ){
            if( $obj->code == 'USD' ) $btc = $obj->rate;
            if( $obj->code == $code ) $price = $obj->rate;
        }

        if ($code == 'BTC') {
            return $btc;
        } else {
            return ($usd / $price ) * $btc;
        }
    }

    public function getBalance($params) {
        $endpoint = $this->setting['url'] . '/accounts/list';

        $validator = Validator::make($params, [
            'currency' => ['required', 'string', 'min:3', 'max:5'],
        ]);

        if ($validator->fails()) {
            return null;
        }

        $inputData = $validator->validated();
        $currency = $inputData['currency'] == 'USDT' ? 'USDTE' : $inputData['currency']; 

        $inputData = [];
        $requestBody = json_encode($inputData);
        $signature = hash_hmac('sha512', $requestBody, $this->setting['secret']);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Processing-Key' => $this->setting['key'],
            'X-Processing-Signature' => $signature,
        ])->post($endpoint, $inputData)->json();

        if (!isset($response['data'])) {
            return null;
        }

        foreach($response['data'] as $balance) {
            if ($balance['currency'] == $currency) {
                return $balance['balance'];
            }
        }

        return null;
    }
}