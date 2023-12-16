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
use Session;
use Txn;
use Validator;
use App\Traits\Payment;

use App\Libraries\AlphaPo;
use Illuminate\Support\Facades\Log;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class DepositController extends GatewayController
{
    use ImageUpload, NotifyTrait, Payment;

    public function deposit()
    {
        $locked = false;

        if (! setting('user_deposit', 'permission') || ! Auth::user()->deposit_status) {
            $locked = true;
        }

        $isStepOne = 'current';
        $isStepTwo = '';
        $gateways = DepositMethod::where('status', 1)->get();

        return view('frontend::deposit.now', compact('locked', 'isStepOne', 'isStepTwo', 'gateways'));
    }

    public function depositNow(Request $request)
    {
        if (! setting('user_deposit', 'permission') || ! Auth::user()->deposit_status) {
            abort('403', trans('translation.lock_feature', ['feature' => __('Deposit')]));
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
                Log::error(json_encode($apiResponse));
                notify()->error($message, 'Error');

                return redirect()->back();
            }
        } else {
            notify()->error($message, __('Invalid deposit method'));

            return redirect()->back();

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

        // if (isset($input['manual_data'])) {
        //     $depositType = TxnType::ManualDeposit;
        //     $manualData = $input['manual_data'];

        //     foreach ($manualData as $key => $value) {
        //         if (is_file($value)) {
        //             $manualData[$key] = self::imageUploadTrait($value);
        //         }
        //     }
        // }

        if ($request['gateway_code'] == 'alphapo') {
            $txnInfo = Txn::new(
                $input['amount'], 
                $charge, 
                $finalAmount, 
                $gatewayInfo->gateway_code, 
                'Deposit With '.$gatewayInfo->name, 
                $depositType, 
                TxnStatus::Pending, 
                $input['crypto_currency'], 
                $payAmount, 
                auth()->id(), 
                null, 
                'User', 
                $manualData ?? [],  'none', 
                null, 
                null, 
                null, 
                $apiResponse['data']['address']);
        } else {
            $txnInfo = Txn::new($input['amount'], $charge, $finalAmount, $gatewayInfo->gateway_code, 'Deposit With '.$gatewayInfo->name, $depositType, TxnStatus::Pending, $gatewayInfo->currency, $payAmount, auth()->id(), null, 'User', $manualData ?? []);
        }

        $symbol = $currencySetting['currency'];
        $notify = [
            'card-header' => 'Deposit Money',
            'title' => $symbol. ' ' . $txnInfo->amount.' Deposit Requested Successfully',
            'p' => 'The Deposit Request has been successfully sent.',
            'strong' => 'Transaction ID: '.$txnInfo->tnx,
            'action' => route('user.deposit.now'),
            'a' => 'DEPOSIT REQUEST AGAIN',
            'data' => $apiResponse['data'],
            'view_name' => 'deposit',
        ];
        Session::put('user_notify', $notify);
        $shortcodes = [
            '[[full_name]]' => $txnInfo->user->full_name,
            '[[txn]]' => $txnInfo->tnx,
            '[[gateway_name]]' => strtoupper($gatewayInfo->gateway_code),
            '[[deposit_amount]]' => $symbol. ' ' .$txnInfo->amount,
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
        ];

        $this->mailNotify(setting('site_email', 'global'), 'manual_deposit_request', $shortcodes);
        $this->pushNotify('manual_deposit_request', $shortcodes, route('admin.deposit.manual.pending'), $txnInfo->user->id);
        $this->smsNotify('manual_deposit_request', $shortcodes, $txnInfo->user->phone);

        return redirect()->route('user.notify');

        // return self::depositAutoGateway($gatewayInfo->gateway_code, $txnInfo, $apiResponse);
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
}
