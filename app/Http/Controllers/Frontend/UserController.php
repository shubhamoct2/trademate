<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Ranking;
use App\Models\Transaction;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Session;
use Txn;
use Validator;
use Auth;
use App\Traits\NotifyTrait;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use NotifyTrait;

    public function userExist($email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            $data = 'Name: '.$user->first_name.' '.$user->last_name;
        } else {
            $data = 'User Not Found';
        }

        return $data;
    }

    public function changePassword()
    {
        return view('frontend::user.change_password');
    }

    public function showLock($feature)
    {
        return view('frontend::user.disabled.index', compact('feature'));
    }

    public function askUnlock(Request $request) {
        $validator = Validator::make($request->all(), [
            'feature' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');
            return redirect()->back();
        }

        $user = Auth::user();
        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[email]]' => $user->email,
            '[[feature]]' => $request->feature,
            '[[site_url]]' => route('home'),
            '[[site_title]]' => setting('site_title', 'global'),
        ];

        $this->mailNotify(setting('support_email', 'global'), 'ask_unlock_feature', $shortcodes);

        notify()->success('Your request was sent successfully', 'success');

        return redirect()->back();
    }

    public function newPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
        User::find(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);
        notify()->success('Password Changed Successfully');

        return redirect()->back();
    }

    public function rankingBadge()
    {
        $alreadyRank = json_decode(auth()->user()->rankings, true);
        
        $rankings = Ranking::where('status', true)->get();

        return view('frontend::ranking.index', compact('rankings', 'alreadyRank'));
    }

    public function walletExchange()
    {
        $isStepOne = 'current';
        $isStepTwo = '';

        return view('frontend::wallet.now', compact('isStepOne', 'isStepTwo'));
    }

    public function walletExchangeNow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_wallet' => ['required', 'different:to_wallet'],
            'to_wallet' => ['required', 'different:from_wallet', Rule::in([1,3])],
            'amount' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $input = $request->all();

        $amount = (float) $input['amount'];
        $chargeType = Setting('wallet_exchange_charge_type', 'fee');
        $charge = (float) Setting('wallet_exchange_charge', 'fee');

        //daily limit
        $todayTransaction = Transaction::where('type', TxnType::Exchange)->whereDate('created_at', Carbon::today())->count();
        $exchangeDayLimit = (float) Setting('wallet_exchange_day_limit', 'fee');
        if ($todayTransaction >= $exchangeDayLimit) {
            notify()->error(__('Today Exchange limit has been reached'), 'Error');

            return redirect()->back();
        }

        if ($chargeType == 'percentage') {
            $charge = $amount * ($charge / 100);
        }

        $totalAmount = $amount + $charge;

        $user = \Auth::user();

        if ((1 == $input['from_wallet'] && $user->balance < $totalAmount) || 
            (2 == $input['from_wallet'] && $user->profit_balance < $totalAmount) ||
            (3 == $input['from_wallet'] && $user->trading_balance < $totalAmount) ||
            (4 == $input['from_wallet'] && $user->commission_balance < $totalAmount)
        ) {
            $walletName = '';
            if ($input['from_wallet'] == 1) {
                $walletName = __('Main Wallet');
            } elseif ($input['from_wallet'] == 2) {
                $walletName = __('Profit Wallet');
            } elseif ($input['from_wallet'] == 3) {
                $walletName = __('Trading Wallet');
            } elseif ($input['from_wallet'] == 4) {
                $walletName = __('Commission Wallet');
            } 

            notify()->error(__('Insufficient Balance In Your ').$walletName, 'Error');

            return redirect()->back();
        }
        
        if ($input['from_wallet'] == 1) {
            $user->decrement('balance', $totalAmount);
            $from_wallet = __('Main Wallet');
        } else if ($input['from_wallet'] == 2) {
            $user->decrement('profit_balance', $totalAmount);
            $from_wallet = __('Profit Wallet');
        } else if ($input['from_wallet'] == 3) {
            $user->decrement('trading_balance', $totalAmount);
            $from_wallet = __('Trading Wallet');
        } else if ($input['from_wallet'] == 4) {
            $user->decrement('commission_balance', $totalAmount);
            $from_wallet = __('Commission Wallet');
        }

        if ($input['to_wallet'] == 1) {
            $to_wallet = __('Main Wallet');
        } else if ($input['to_wallet'] == 2) {
            $to_wallet = __('Profit Wallet');
        } else if ($input['to_wallet'] == 3) {
            $to_wallet = __('Trading Wallet');
        } else if ($input['to_wallet'] == 4) {
            $to_wallet = __('Commission Wallet');
        }

        $sendDescription = trans('translation.exchange_description', [
            'from' => $from_wallet,
            'to' => $to_wallet,
        ]);

        /*
        2 Main => Trading
        4 Profit => Main
        6 Profit => Trading
        8 Trading => Main
        12 Commission => Main
        14 Commission => Trading
        */

        $method = ($input['from_wallet'] - 1) * 4 + ($input['to_wallet'] - 1);

        $txnInfo = Txn::new($amount, $charge, $totalAmount, $method, $sendDescription,
                TxnType::Exchange, TxnStatus::Pending, null, null, $user->id);

        $symbol = setting('currency_symbol', 'global');

        $notify = [
            'card-header' => 'Success Your Exchange Money Request',
            'title' => $symbol.$txnInfo->amount.' Exchange Wallet Money Requested Successfully. Admin will review it.',
            'p' => $sendDescription,
            'strong' => 'Transaction ID: '.$txnInfo->tnx,
            'action' => route('user.wallet-exchange'),
            'a' => 'Exchange Wallet Money again',
            'view_name' => 'wallet',
        ];
        Session::put('user_notify', $notify);
        return redirect()->route('user.notify');
    }

    public function notifyUser()
    {
        $notify = Session::get('user_notify');
        $isStepOne = 'current';
        $isStepTwo = 'current';
        $viewName = $notify['view_name'];

        return view('frontend::'.$viewName.'.success', compact('isStepOne', 'isStepTwo', 'notify'));
    }

    public function latestNotification()
    {
        $notifications = Notification::where('for', 'user')->where('user_id', auth()->user()->id)->latest()->take(10)->get();
        $totalUnread = Notification::where('for', 'user')->where('user_id', auth()->user()->id)->where('read', 0)->count();
        $totalCount = Notification::where('for', 'user')->where('user_id', auth()->user()->id)->get()->count();
        $lucideCall = true;

        return view('global.__notification_data', compact('notifications', 'totalUnread', 'totalCount', 'lucideCall'))->render();
    }

    public function allNotification()
    {
        $notifications = Notification::where('for', 'user')->where('user_id', auth()->user()->id)->latest()->paginate(10);

        return view('frontend::user.notification.index', compact('notifications'));
    }

    public function readNotification($id)
    {

        if ($id == 0) {
            Notification::where('for', 'user')->where('user_id', auth()->user()->id)->update(['read' => 1]);

            return redirect()->back();
        }
        $notification = Notification::find($id);
        if ($notification->read == 0) {
            $notification->read = 1;
            $notification->save();
        }

        return redirect()->to($notification->action_url);
    }
}
