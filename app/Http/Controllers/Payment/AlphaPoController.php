<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Txn;
use Validator;
use App\Traits\NotifyTrait;
use App\Libraries\AlphaPo;
use Illuminate\Support\Facades\Log;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class AlphaPoController extends Controller
{
    use NotifyTrait;

    public function alphapoCallback(Request $request) {
        Log::info("Callback detected: " . json_encode($request->all()));

        if (isset($request['status']) && $request['status'] === 'confirmed') {
            if ($request['type'] == 'withdrawal') { // withdrawal
                $index = $request['id'];

                $currency = $request['currency_sent']['currency'];
                $amount = $request['currency_sent']['amount'];
                $txID = $request['transactions'][0]['txid'];
                $address = $request['crypto_address']['address'];

                $transaction = Transaction::where('address', $index)->first();

                if ($transaction && $transaction->status !== TxnStatus::Success) {
                    $alphaPo = new AlphaPo;
                    $price = $alphaPo->getCryptoPrice($currency);

                    if (!is_null($price)) {
                        $pay_amount = floatval($amount) * floatval($price);
                    
                        Txn::update(
                            $transaction->tnx, 
                            TxnStatus::Success, 
                            $transaction->user_id,
                            'none', 
                            $amount, 
                            $pay_amount, 
                            $txID,
                            $address
                        );

                        $shortcodes = [
                            '[[full_name]]' => $transaction->user->full_name,
                            '[[txn]]' => $transaction->tnx,
                            '[[method_name]]' => $transaction->method,
                            '[[withdraw_amount]]' => $transaction->pay_currency . ' ' . $transaction->final_amount,
                            '[[site_title]]' => setting('site_title', 'global'),
                            '[[site_url]]' => route('home'),
                            '[[message]]' => '', //$transaction->approval_cause,
                            '[[status]]' => 'approved',
                        ];
                
                        $this->mailNotify($transaction->user->email, 'withdraw_request_user', $shortcodes);
                        $this->pushNotify('withdraw_request_user', $shortcodes, route('user.withdraw.log'), $transaction->user->id);
                        $this->smsNotify('withdraw_request_user', $shortcodes, $transaction->user->phone);
                    }
                }
            } else { // Deposit
                $address = $request['crypto_address']['address'];
                $currency = $request['currency_received']['currency'];
                $amount = $request['currency_received']['amount'];
                $txID = $request['transactions'][0]['txid'];

                $transaction = Transaction::where('address', $address)->first();

                if ($transaction && $transaction->status !== TxnStatus::Success) {
                    $alphaPo = new AlphaPo;
                    $price = $alphaPo->getCryptoPrice($currency);

                    if (!is_null($price)) {
                        $pay_amount = floatval($amount) * floatval($price);
                        
                        Txn::update(
                            $transaction->tnx, 
                            TxnStatus::Success, 
                            $transaction->user_id,
                            'none', 
                            $amount, 
                            $pay_amount, 
                            $txID
                        );

                        $shortcodes = [
                            '[[full_name]]' => $transaction->user->full_name,
                            '[[txn]]' => $transaction->tnx,
                            '[[gateway_name]]' => $transaction->method,
                            '[[deposit_amount]]' => $transaction->pay_currency . ' ' . $transaction->final_amount,
                            '[[site_title]]' => setting('site_title', 'global'),
                            '[[site_url]]' => route('home'),
                            '[[message]]' => '', //$transaction->approval_cause,
                            '[[status]]' => 'approved',
                        ];
                
                        $this->mailNotify($transaction->user->email, 'user_manual_deposit_request', $shortcodes);
                        $this->pushNotify('user_manual_deposit_request', $shortcodes, route('user.deposit.log'), $transaction->user->id);
                        $this->smsNotify('user_manual_deposit_request', $shortcodes, $transaction->user->phone);
                
                    }
                }
            }
        }
    }

    public function getMinDeposit($code)
    {
        if (config('app.env') === 'production') {
            $alphapoSetting = config('alphapo.prod');
        } else {
            $alphapoSetting = config('alphapo.sandbox');
        }

        foreach ($alphapoSetting['currencies'] as $currency) {
            if ($currency['currency'] == $code) {
                return $currency['minimum_amount'];
            }
        }

        return null;
    }

    public function testPrice() {
        // $user = User::find(10);
        // $user->increment('balance', floatval(3.835461));

        $address = '2MsfwD76rDycKUoDgjUwyYjqBB5xDvGuL6j';
        $currency = 'BTC';
        $amount = 0.00014243;
        $txID = '7e76f87312ad74bf433e21dff49cd4c1299b6806b72b683e7ffc45cd2fbd893c';
        $userID = 10;

        $transaction = Transaction::where('address', $address)->first();

        $alphaPo = new AlphaPo;
        $price = $alphaPo->getCryptoPrice($currency);
        $pay_amount = $amount * $price;

        Txn::update($transaction->tnx, TxnStatus::Success, $userID, 'none', $amount, $pay_amount, $txID);
    }

    public function testWithdraw(Request $request) {
        $inputData = [            
            // 'amount' => 0.0001,
            // 'currency' => 'BTC',
            // 'address' => 'tb1q9rw8la4zrdypvpz75w4pdra2y8myvuvy2wkz7j',
            // 'foreign_id' => 10,
        ];

        if (config('app.env') === 'production') {
            $setting = config('alphapo.prod');
        } else {
            $setting = config('alphapo.sandbox');
        }

        $requestBody = '{"currency":"BTC","foreign_id":"10","address":"tb1q9rw8la4zrdypvpz75w4pdra2y8myvuvy2wkz7j","amount":"0.0002"}'; 
        //json_encode($inputData);
        $signature = hash_hmac('sha512', $requestBody, $setting['secret']);

        dd ($signature);
    }

    public function testGetBalance(Request $request) {
        $alphaPo = new AlphaPo;
        
        $balance = $alphaPo->getBalance([
            'currency' => 'BTC'
        ]);

        dd ($balance);
    }
}
