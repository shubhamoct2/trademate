<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use App\Models\Transaction;
use App\Models\User;

use App\Enums\TxnType;
use App\Enums\TxnStatus;

use App\Traits\NotifyTrait;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\DataTables\ProfitsDataTable;
use App\DataTables\ProfitDistributeDataTable;

use DataTables;
use Exception;
use Txn;

class ProfitController extends Controller
{
    use NotifyTrait;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct()
    {
        $this->middleware('permission:profit-list');

    }

    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws Exception
     */
    public function allProfits(ProfitsDataTable $dataTable)
    {
        return $dataTable->render('backend.transaction.profit');
    }

    public function list(ProfitDistributeDataTable $dataTable)
    {
        return $dataTable->render('backend.transaction.distribute');
    }

    public function index() {
        return view('backend.profit.index');
    }

    public function push(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'profit' => 'required|regex:/^\d*(\.\d{2})?$/',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');
            return redirect()->back();
        }

        $total_profit = floatval($input['profit']);
        $total_trading = User::where('status', 1)->sum('trading_balance');
        $active_user_list = User::where('status', 1)->get();

        foreach ($active_user_list as $user) {
            if ($user->trading_balance > 0) {
                $user_profit = round(($total_profit * floatval($user->trading_balance))  / floatval($total_trading), 2);
                $user->increment('profit_balance', $user_profit);

                $transaction = Txn::new (
                    $user_profit, 
                    0, 
                    $user_profit, 
                    'system', 
                    __('Manual Profit Distribution by System'),
                    TxnType::ProfitShare, 
                    TxnStatus::Success, 
                    null, 
                    $user_profit, 
                    $user->id,
                    $user->id,
                    'Admin'
                );
                
                $shortcodes = [
                    '[[full_name]]' => $transaction->user->full_name,
                    '[[txn]]' => $transaction->tnx,
                    '[[method_name]]' => strtoupper($transaction->method),
                    '[[commission_amount]]' =>  $transaction->final_amount . setting('site_currency', 'global'),
                    '[[site_title]]' => setting('site_title', 'global'),
                    '[[site_url]]' => route('home'),
                    '[[message]]' => '',
                    '[[status]]' => 'approved',
                ];
            
                $this->pushNotify('received_profit', $shortcodes, route('user.transactions'), $transaction->user->id);
            }
        }

        notify()->success('Send Profit Successfully', 'success');
        return redirect()->back();
    }
}
