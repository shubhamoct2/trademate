<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\WithdrawAccount;
use App\Models\WithdrawalSchedule;
use App\Models\WithdrawMethod;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use App\Traits\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Txn;
use Validator;

use App\Libraries\AlphaPo;
use Illuminate\Support\Facades\Log;

class WithdrawController extends Controller
{
    use ImageUpload, NotifyTrait, Payment;

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $accounts = WithdrawAccount::where('user_id', auth()->id())->get();

        return view('frontend::withdraw.account.index', compact('accounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'withdraw_method_id' => 'required',
            'method_name' => 'required',
            'credentials' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $input = $request->all();

        $credentials = $input['credentials'];
        foreach ($credentials as $key => $value) {

            if (is_file($value['value'])) {
                $credentials[$key]['value'] = self::imageUploadTrait($value['value']);
            }
        }

        $data = [
            'user_id' => auth()->id(),
            'withdraw_method_id' => $input['withdraw_method_id'],
            'method_name' => $input['method_name'],
            'credentials' => json_encode($credentials),
        ];

        WithdrawAccount::create($data);

        notify()->success('Successfully Withdraw Account Created', 'success');

        return redirect()->route('user.withdraw.account.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $withdrawMethods = WithdrawMethod::where('status', true)->get();

        return view('frontend::withdraw.account.create', compact('withdrawMethods'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $withdrawMethods = WithdrawMethod::all();
        $withdrawAccount = WithdrawAccount::find($id);

        return view('frontend::withdraw.account.edit', compact('withdrawMethods', 'withdrawAccount'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'withdraw_method_id' => 'required',
            'method_name' => 'required',
            'credentials' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $input = $request->all();

        $withdrawAccount = WithdrawAccount::find($id);

        $oldCredentials = json_decode($withdrawAccount->credentials, true);

        $credentials = $input['credentials'];
        foreach ($credentials as $key => $value) {

            if (! isset($value['value'])) {
                $credentials[$key]['value'] = $oldCredentials[$key]['value'];
            }

            if (isset($value['value']) && is_file($value['value'])) {
                $credentials[$key]['value'] = self::imageUploadTrait($value['value'], $oldCredentials[$key]['value']);
            }
        }

        $data = [
            'user_id' => auth()->id(),
            'withdraw_method_id' => $input['withdraw_method_id'],
            'method_name' => $input['method_name'],
            'credentials' => json_encode($credentials),
        ];

        $withdrawAccount->update($data);
        notify()->success('Successfully Withdraw Account Updated', 'success');

        return redirect()->route('user.withdraw.account.index');

    }

    /**
     * @return string
     */
    public function withdrawMethod($id)
    {
        $withdrawMethod = WithdrawMethod::find($id);

        if ($withdrawMethod) {
            return view('frontend::withdraw.include.__account', compact('withdrawMethod'))->render();
        }

        return '';
    }

    /**
     * @return array
     */
    public function details($accountId, int $amount = 0)
    {

        $withdrawAccount = WithdrawAccount::find($accountId);

        $credentials = json_decode($withdrawAccount->credentials, true);

        $currency = setting('site_currency', 'global');
        $method = $withdrawAccount->method;
        $charge = $method->charge;
        $name = $withdrawAccount->method_name;
        $processingTime = (int) $method->required_time > 0 ? 'Processing Time: '.$withdrawAccount->method->required_time.$withdrawAccount->method->required_time_format : 'This Is Automatic Method';

        $info = [
            'name' => $name,
            'charge' => $charge,
            'charge_type' => $withdrawAccount->method->charge_type,
            'range' => 'Minimum '.$method->min_withdraw.' '.$currency.' and '.'Maximum '.$method->max_withdraw.' '.$currency,
            'processing_time' => $processingTime,
            'rate' => $method->rate,
            'pay_currency' => $method->currency
        ];

        if ($withdrawAccount->method->charge_type != 'fixed') {
            $charge = ($charge / 100) * $amount;
        }
        $conversionRate = $method->currency != $currency ? $method->rate .' '.$method->currency : null;
        $html = view('frontend::withdraw.include.__details', compact('credentials', 'name', 'charge','conversionRate'))->render();

        return [
            'html' => $html,
            'info' => $info,
        ];
    }

    /**
     * @return string
     */
    public function withdrawNow(Request $request)
    {
        if (! setting('user_withdraw', 'permission') || ! Auth::user()->withdraw_status) {
            abort('403', trans('translation.lock_feature', ['feature' => __('Withdraw')]));
        }

        $user = Auth::user();

        $withdrawOffDays = WithdrawalSchedule::where('status', 0)->pluck('name')->toArray();
        $date = Carbon::now();
        $today = $date->format('l');

        if (in_array($today, $withdrawOffDays)) {
            abort('403', __('Today is the off day of withdraw'));
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'gateway' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');
            return redirect()->back();
        }

        //daily limit
        $todayTransaction = Transaction::where('type', TxnType::Withdraw)->orWhere('type', TxnType::WithdrawAuto)->whereDate('created_at', Carbon::today())->count();
        $dayLimit = (float) Setting('withdraw_day_limit', 'fee');
        if ($todayTransaction >= $dayLimit) {
            notify()->error(__('Today Withdraw limit has been reached'), 'Error');
            return redirect()->back();
        }

        $input = $request->all();
        $amount = (float) $input['amount'];
        $address = json_decode($user->withdrawal_address);

        if (is_null($address)) {
            $message = 'You have to specify withdrawal address.';
            notify()->error($message, 'Error');
            return redirect()->back();
        }

        $withdrawMethod = withdrawMethod::where('gateway_code', $input['gateway'])->first();

        if ($request['gateway'] == 'alphapo') {
            if (config('app.env') === 'production') {
                $alphapoSetting = config('alphapo.prod');
            } else {
                $alphapoSetting = config('alphapo.sandbox');
            }

            $currencySetting = null;
            foreach($alphapoSetting['currencies'] as $currency) {
                if ($currency['currency'] == strtoupper($address->currency)) {
                    $currencySetting = $currency;
                    break;
                }
            }

            if (floatval($amount) < floatval($currencySetting['minimum_withdraw_amount'])) {
                $message = 'Please define the Amount much more than '. $currencySetting['minimum_amount'] . ' ' . $currencySetting['currency'];
                notify()->error($message, 'Error');
                return redirect()->back();
            }

            $charge = $withdrawMethod->charge_type == 'percentage' ? (($withdrawMethod->charge / 100) * $amount) : $withdrawMethod->charge;
            $totalAmount = $amount + (float) $charge;

            // Get price of the currency
            $alphaPo = new AlphaPo;
            $totalPrice = $totalAmount * $alphaPo->getCryptoPrice($currencySetting['currency']);

            // Check user balance with the total amount
            if (floatval($totalPrice) > Auth::user()->balance) {
                $message = __('Insufficient Balance In Your Wallet');
                notify()->error($message, 'Error');

                return redirect()->back();
            }

            // Check merchant balance for the currency
            $alphaPo = new AlphaPo;
            $merchant_balance = $alphaPo->getBalance([
                'currency' => $currencySetting['currency']
            ]);
            if (floatval($totalAmount) > $merchant_balance) {
                $message = __('Insufficient Balance In Merchant Wallet');
                notify()->error($message, 'Error');

                return redirect()->back();
            }

            $alphaPo = new AlphaPo;
            $apiResponse = $alphaPo->createWithdrawRequest([
                'currency' => $currencySetting['currency'],
                'foreign_id' => Auth::user()->id,
                'address' => $address->address,
                'amount' => $totalAmount,
            ]);

            if (!isset($apiResponse['data'])) {
                $message = 'Cannot call payment gateway API functions. Please try again.';
                notify()->error($message, 'Error');

                return redirect()->back();
            }
        }

        $charge = $withdrawMethod->charge_type == 'percentage' ? (($withdrawMethod->charge / 100) * $amount) : $withdrawMethod->charge;
        $finalAmount = (float) $amount + (float) $charge;
        $payAmount = $finalAmount * $withdrawMethod->rate;
        $type = TxnType::Withdraw;

        $txnInfo = Txn::new(
            $input['amount'], 
            $charge, 
            $finalAmount, 
            $withdrawMethod->gateway_code, 
            'Withdraw With '.$withdrawMethod->name, 
            $type, 
            TxnStatus::Pending, 
            $currencySetting['currency'],
            $payAmount, 
            auth()->id(), 
            null, 
            'User', 
            [],
            'none',
            null, 
            null, 
            null, 
            $apiResponse['data']['id']
        );

        $symbol = $currencySetting['currency'];
        $notify = [
            'card-header' => 'Withdraw Money',
            'title' => $symbol.$txnInfo->amount.' Withdrawal Requested Successfully',
            'p' => 'The Withdraw Request has been successfully sent.',
            'strong' => 'Transaction ID: '.$txnInfo->tnx,
            'action' => route('user.withdraw.view'),
            'a' => 'WITHDRAW REQUEST AGAIN',
            'view_name' => 'withdraw',
        ];
        Session::put('user_notify', $notify);
        $shortcodes = [
            '[[full_name]]' => $txnInfo->user->full_name,
            '[[txn]]' => $txnInfo->tnx,
            '[[method_name]]' => $withdrawMethod->name,
            '[[withdraw_amount]]' => $txnInfo->amount.setting('site_currency', 'global'),
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
        ];

        $this->mailNotify(setting('site_email', 'global'), 'withdraw_request', $shortcodes);
        $this->pushNotify('withdraw_request', $shortcodes, route('admin.withdraw.pending'), $user->id);
        $this->smsNotify('withdraw_request', $shortcodes, $user->phone);

        return redirect()->route('user.notify');

    }

    /**
     * @return Application|Factory|View
     */
    public function withdraw()
    {
        $locked = false;

        if (! setting('user_withdraw', 'permission') || ! Auth::user()->withdraw_status) {
            $locked = true;
        }

        $accounts = WithdrawAccount::where('user_id', \Auth::id())->get();
        $accounts = $accounts->reject(function ($value, $key) {
            return ! $value->method->status;
        });

        $gateways = withdrawMethod::where('status', 1)->get();
        $address = [];

        $user = Auth::user();
        if ($user->withdrawal_address) {
            $withdrawal_address = json_decode($user->withdrawal_address);

            if (config('app.env') === 'production') {
                $alphapoSetting = config('alphapo.prod');
            } else {
                $alphapoSetting = config('alphapo.sandbox');
            }
            
            if (isset($withdrawal_address->currency) && isset($withdrawal_address->address)) {
                $address['currency'] = $alphapoSetting['withdrawal']['currencies'][$withdrawal_address->currency];
                if (isset($withdrawal_address->blockchain))
                    $address['blockchain'] = $alphapoSetting['withdrawal']['blockchain'][$withdrawal_address->blockchain];
                $address['address'] = $withdrawal_address->address;
            } 
        }
        
        return view('frontend::withdraw.now', compact('locked', 'accounts', 'gateways', 'address'));
    }

    public function withdrawLog()
    {
        $withdraws = Transaction::search(request('query'), function ($query) {
            $query->where('user_id', auth()->user()->id)
                ->where('type', TxnType::Withdraw)
                ->when(request('date'), function ($query) {
                    $query->whereDay('created_at', '=', Carbon::parse(request('date'))->format('d'));
                });
        })->where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('frontend::withdraw.log', compact('withdraws'));
    }
}
