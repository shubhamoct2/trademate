<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\LevelReferral;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\NotifyTrait;
use Exception;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Txn;

use DataTables;

use App\DataTables\UsersDataTable;
use App\DataTables\ActiveUsersDataTable;
use App\DataTables\DisabledUsersDataTable;

use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use NotifyTrait;

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:customer-list|customer-login|customer-mail-send|customer-basic-manage|customer-change-password|all-type-status|customer-balance-add-or-subtract', ['only' => ['index', 'activeUser', 'disabled', 'mailSendAll', 'mailSend']]);
        $this->middleware('permission:customer-basic-manage|customer-change-password|all-type-status|customer-balance-add-or-subtract', ['only' => ['edit']]);
        $this->middleware('permission:customer-login', ['only' => ['userLogin']]);
        $this->middleware('permission:customer-mail-send', ['only' => ['mailSendAll', 'mailSend']]);
        $this->middleware('permission:customer-basic-manage', ['only' => ['update']]);
        $this->middleware('permission:customer-change-password', ['only' => ['passwordUpdate']]);
        $this->middleware('permission:all-type-status', ['only' => ['statusUpdate']]);
        $this->middleware('permission:customer-balance-add-or-subtract', ['only' => ['balanceUpdate']]);
    }

    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws Exception
     */
    public function index(UsersDataTable $dataTable) {
        return $dataTable->render('backend.user.index');
    }


    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws Exception
     */
    public function activeUser(ActiveUsersDataTable $dataTable) {
        return $dataTable->render('backend.user.active');
    }

    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws Exception
     */
    public function disabledUser(DisabledUsersDataTable $dataTable)
    {
        return $dataTable->render('backend.user.disabled');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $user = User::find($id);

        // $level = LevelReferral::where('type', 'investment')->max('the_order') + 1;
        $level = LevelReferral::max('the_order');

        return view('backend.user.edit', compact('user', 'level'));
    }

    /**
     * @return RedirectResponse
     */
    public function statusUpdate($id, Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'status' => 'required',
            'email_verified' => 'required',
            'kyc' => 'required',
            'two_fa' => 'required',
            'deposit_status' => 'required',
            'withdraw_status' => 'required',
            'transfer_status' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $data = [
            'status' => $input['status'],
            'kyc' => $input['kyc'],
            'two_fa' => $input['two_fa'],
            'deposit_status' => $input['deposit_status'],
            'withdraw_status' => $input['withdraw_status'],
            'transfer_status' => $input['transfer_status'],
            'email_verified_at' => $input['email_verified'] == 1 ? now() : null,
        ];

        $user = User::find($id);

        if ($user->status != $input['status'] && ! $input['status']) {

            $shortcodes = [
                '[[full_name]]' => $user->full_name,
                '[[site_title]]' => setting('site_title', 'global'),
                '[[site_url]]' => route('home'),
            ];

            $this->mailNotify($user->email, 'user_account_disabled', $shortcodes);
            $this->smsNotify('user_account_disabled', $shortcodes, $user->phone);
        }

        User::find($id)->update($data);

        notify()->success('Status Updated Successfully', 'success');

        return redirect()->back();

    }

    /**
     * @return RedirectResponse
     */
    public function update($id, Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:users,username,'.$id,
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        User::find($id)->update($input);
        notify()->success('User Info Updated Successfully', 'success');

        return redirect()->back();
    }

    /**
     * @return RedirectResponse
     */
    public function passwordUpdate($id, Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $password = $validator->validated();

        User::find($id)->update([
            'password' => Hash::make($password['new_password']),
        ]);
        notify()->success('User Password Updated Successfully', 'success');

        return redirect()->back();
    }

    /**
     * @return RedirectResponse
     */
    public function rankingUpdate($id, Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_ranking' => ['required', 'exists:rankings,id'],
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $input = $validator->validated();

        $new_rankings = [];
        for ($i = 1; $i <= intval($input['user_ranking']); $i++) {
            $new_rankings[] = $i;
        }

        User::find($id)->update([
            'ranking_id' => $input['user_ranking'],
            'rankings' => $new_rankings
        ]);
        notify()->success('User Ranking Updated Successfully', 'success');

        return redirect()->back();
    }

    /**
     * @return RedirectResponse|void
     */
    public function balanceUpdate($id, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        try {

            $amount = $request->amount;
            $type = $request->type;
            $wallet = $request->wallet;

            $user = User::find($id);
            $adminUser = \Auth::user();

            if ($type == 'add') {

                if ($wallet == 'main') {
                    $user->balance += $amount;
                    $user->save();
                } else {
                    $user->profit_balance += $amount;
                    $user->save();
                }

                Txn::new($amount, 0, $amount, 'system', 'Money added in '.ucwords($wallet).' Wallet from System', TxnType::Deposit, TxnStatus::Success, null, null, $id, $adminUser->id, 'Admin');

                $status = 'success';
                $message = __('Account Balance Update');

            } elseif ($type == 'subtract') {

                if ($wallet == 'main') {
                    $user->balance -= $amount;
                    $user->save();
                } else {
                    $user->profit_balance -= $amount;
                    $user->save();
                }

                Txn::new($amount, 0, $amount, 'system', 'Money subtract in '.ucwords($wallet).' Wallet from System', TxnType::Subtract, TxnStatus::Success, null, null, $id, $adminUser->id, 'Admin');
                $status = 'success';
                $message = __('Account Balance Updated');
            }

            notify()->success($message, $status);

            return redirect()->back();

        } catch (Exception $e) {
            $status = 'warning';
            $message = __('something is wrong');
            $code = 503;
        }

    }

    /**
     * @return Application|Factory|View
     */
    public function mailSendAll()
    {
        return view('backend.user.mail_send_all');
    }

    /**
     * @return RedirectResponse
     */
    public function mailSend(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        try {

            $input = [
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $shortcodes = [
                '[[subject]]' => $input['subject'],
                '[[message]]' => $input['message'],
                '[[site_title]]' => setting('site_title', 'global'),
                '[[site_url]]' => route('home'),
            ];

            if (isset($request->id)) {
                $user = User::find($request->id);

                $shortcodes = array_merge($shortcodes, ['[[full_name]]' => $user->full_name]);

                $this->mailNotify($user->email, 'user_mail', $shortcodes);

            } else {
                $users = User::where('status', 1)->get();

                foreach ($users as $user) {
                    $shortcodes = array_merge($shortcodes, ['[[full_name]]' => $user->full_name]);

                    $this->mailNotify($user->email, 'user_mail', $shortcodes);
                }

            }
            $status = 'success';
            $message = __('Mail Send Successfully');

        } catch (Exception $e) {

            $status = 'warning';
            $message = __('something is wrong');
        }

        notify()->$status($message, $status);

        return redirect()->back();
    }

    /**
     * @return JsonResponse|void
     *
     * @throws Exception
     */
    public function transaction($id, Request $request)
    {
        if ($request->ajax()) {
            $data = Transaction::where('user_id', $id)->latest();
            
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('status', 'backend.user.include.__txn_status')
                ->editColumn('type', 'backend.user.include.__txn_type')
                ->editColumn('final_amount', 'backend.user.include.__txn_amount')
                ->rawColumns(['status', 'type', 'final_amount'])
                ->make(true);
        }
    }

    /**
     * @return RedirectResponse
     */
    public function userLogin($id)
    {
        Auth::guard('web')->loginUsingId($id);

        return redirect()->route('user.dashboard');
    }

    private function getRefferral(User $levelUser, $level, $depth) {
        if ($level >= $depth) {
            $item = [
                'id' => $levelUser->id,
                'text' => $levelUser->full_name . '<span class="ml-2">(' . $levelUser->email . ')</span>',
                'icon' => '',
                'children' => []
            ];

            foreach ($levelUser->referrals as $user) {
                $child = $this->getRefferral($user, $level, $depth + 1);
                if (!is_null($child)) {
                    $item['children'][] = $child;
                }
            }

            return $item;
        }
    }

    /**
     * @return JsonResponse
     */
    public function getReferralTreeJson($id)
    {
        $user = User::find($id);
        $level = LevelReferral::max('the_order');

        $tree_json = [];
        if(setting('site_referral','global') == 'level' && $user->referrals->count() > 0) {
            $tree_json = $this->getRefferral($user, $level, 1);
        }

        return response()->json([$tree_json]);
    }

    private function updateReferral($parent_id, $children) {
        foreach ($children as $child) {
            $childUser = User::find($child['id']);
            
            $childUser->ref_id = $parent_id;
            $childUser->save();

            if (isset($child['children'])) {
                $this->updateReferral($child['id'], $child['children']);    
            }
        }        
    }

    public function saveReferralTree($id, Request $request)  {
        $top_children = $request['data'][0]['children'];

        $this->updateReferral($id, $top_children);                
    }

    /**
     * @return RedirectResponse
     */
    public function sendCommission($id, Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'commission' => 'required|regex:/^[+-]?\d*(\.\d{2})?$/',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');
            return redirect()->back();
        }

        $user = User::find($id);
        $amount = floatval($input['commission']);

        $user->increment('commission_balance', $amount);

        $transaction = Txn::new(
            $amount, 
            0, 
            $amount, 
            'system', 
            $amount > 0 ? __('Money added in Commission Wallet from Admin Manually') : __('Money subtracted in Commission Wallet from Admin Manually'),
            TxnType::SendCommission, 
            TxnStatus::Success, 
            null, 
            $amount,
            $id, 
            \Auth::user()->id,
            'Admin');

        $shortcodes = [
            '[[full_name]]' => $transaction->user->full_name,
            '[[txn]]' => $transaction->tnx,
            '[[method_name]]' => strtoupper($transaction->method),
            '[[commission_amount]]' =>  $transaction->final_amount . setting('site_currency', 'global'),
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
            '[[message]]' => '', //$transaction->approval_cause,
            '[[status]]' => 'approved',
        ];
    
        $this->pushNotify('received_commission', $shortcodes, route('user.transactions'), $transaction->user->id);

        notify()->success('Sent Commission Successfully', 'success');

        return redirect()->back();
    }

}
