<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\DepositMethod;
use App\Models\Transaction;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Txn;
use Validator;

use App\Libraries\AlphaPo;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class DepositController extends GatewayController
{
    use ImageUpload, NotifyTrait;

    public function deposit()
    {

        if (! setting('user_deposit', 'permission') || ! \Auth::user()->deposit_status) {
            abort('403', 'Deposit Disable Now');
        }

        $isStepOne = 'current';
        $isStepTwo = '';
        $gateways = DepositMethod::where('status', 1)->get();

        return view('frontend::deposit.now', compact('isStepOne', 'isStepTwo', 'gateways'));
    }

    public function depositNow(Request $request)
    {
        if (! setting('user_deposit', 'permission') || ! \Auth::user()->deposit_status) {
            abort('403', 'Deposit Disable Now');
        }

        if ($request['gateway_code'] == 'alphapo') {
            $validator = Validator::make($request->all(), [
                'gateway_code' => 'required',
                'amount' => ['required'],
                'crypto_currency' => ['required', 'string'],
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'gateway_code' => 'required',
                'amount' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            ]);
        }

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $input = $request->all();

        $gatewayInfo = DepositMethod::code($input['gateway_code'])->first();
        $amount = $input['amount'];

        $apiResponse = null;

        if ($request['gateway_code'] == 'alphapo') {
            if (config('app.env') === 'production') {
                $alphapoSetting = config('alphapo.prod');
            } else {
                $alphapoSetting = config('alphapo.sandbox');
            }

            $currencySetting = null;
            foreach($alphapoSetting['currencies'] as $currency) {
                if ($currency['currency'] == $input['crypto_currency']) {
                    $currencySetting = $currency;
                    break;
                }
            }

            if (floatval($amount) < floatval($currencySetting['minimum_amount'])) {
                $message = 'Please Deposit the Amount much more than '. $currencySetting['minimum_amount'] . ' ' . $input['crypto_currency'];
                notify()->error($message, 'Error');

                return redirect()->back();
            }

            $alphaPo = new AlphaPo;
            $apiResponse = $alphaPo->createDepositAddress([
                'currency' => $input['crypto_currency'],
                'foreign_id' => auth()->id(),
            ]);

            if (!isset($apiResponse['data'])) {
                $message = 'Cannot call payment gateway API functions. Please try again.';
                notify()->error($message, 'Error');

                return redirect()->back();
            }
        } else {
            if ($amount < $gatewayInfo->minimum_deposit || $amount > $gatewayInfo->maximum_deposit) {
                $currencySymbol = setting('currency_symbol', 'global');
                $message = 'Please Deposit the Amount within the range '.$currencySymbol.$gatewayInfo->minimum_deposit.' to '.$currencySymbol.$gatewayInfo->maximum_deposit;
                notify()->error($message, 'Error');

                return redirect()->back();
            }
        }

        $charge = $gatewayInfo->charge_type == 'percentage' ? (($gatewayInfo->charge / 100) * $amount) : $gatewayInfo->charge;
        $finalAmount = (float) $amount + (float) $charge;
        $payAmount = $finalAmount * $gatewayInfo->rate;
        $depositType = TxnType::Deposit;

        if (isset($input['manual_data'])) {

            $depositType = TxnType::ManualDeposit;
            $manualData = $input['manual_data'];

            foreach ($manualData as $key => $value) {

                if (is_file($value)) {
                    $manualData[$key] = self::imageUploadTrait($value);
                }
            }

        }

        if ($request['gateway_code'] == 'alphapo') {
            $txnInfo = Txn::new($input['amount'], $charge, $finalAmount, $gatewayInfo->gateway_code, 'Deposit With '.$gatewayInfo->name, $depositType, TxnStatus::Pending, $input['crypto_currency'], $payAmount, auth()->id(), null, 'User', $manualData ?? [],  'none', null, null, null, $apiResponse['data']['address']);
        } else {
            $txnInfo = Txn::new($input['amount'], $charge, $finalAmount, $gatewayInfo->gateway_code, 'Deposit With '.$gatewayInfo->name, $depositType, TxnStatus::Pending, $gatewayInfo->currency, $payAmount, auth()->id(), null, 'User', $manualData ?? []);
        }

        return self::depositAutoGateway($gatewayInfo->gateway_code, $txnInfo, $apiResponse);
    }

    public function depositLog()
    {
        $deposits = Transaction::search(request('query'), function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->when(request('date'), function ($query) {
                    $query->whereDay('created_at', '=', Carbon::parse(request('date'))->format('d'));
                })
                ->whereIn('type', [TxnType::Deposit,TxnType::ManualDeposit]);
        })->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('frontend::deposit.log', compact('deposits'));
    }

    public function alphapoCallback(Request $request) {
        Log::info("Callback detected: " . json_encode($request->all()));

        if (isset($request['status']) && $request['status'] === 'confirmed') {
            $address = $request['crypto_address']['address'];
            $currency = $request['currency_received']['currency'];
            $amount = $request['currency_received']['amount'];
            $txID = $request['transactions'][0]['txid'];
            $userID = intval($request['crypto_address']['foreign_id']);

            $transaction = Transaction::where('address', $address)->first();

            if ($transaction->status !== 'success') {
                $alphaPo = new AlphaPo;
                $price = $alphaPo->getCryptoPrice($currency);
                $pay_amount = $amount * $price;
                
                Txn::update($transaction->tnx, TxnStatus::Success, $userID, 'none', $amount, $pay_amount, $txID);
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
}
