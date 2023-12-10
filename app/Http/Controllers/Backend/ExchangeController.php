<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\LevelReferral;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\NotifyTrait;
use DataTables;
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
use Illuminate\Support\Facades\Log;

class ExchangeController extends Controller
{
    use NotifyTrait;

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:internal-transfer-manage');
    }

    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Transaction::where('type', TxnType::Exchange)->latest();


            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('status', 'backend.exchange.include.__txn_status')
                ->editColumn('description', 'backend.exchange.include.__txn_type')
                ->editColumn('final_amount', 'backend.exchange.include.__txn_amount')
                ->editColumn('charge', function ($request) {
                    return $request->charge.' '.setting('site_currency', 'global');
                })
                ->addColumn('username', 'backend.exchange.include.__user')
                ->addColumn('action', 'backend.exchange.include.__action')
                ->rawColumns(['status', 'description', 'final_amount', 'username', 'action'])
                ->make(true);
        }

        return view('backend.exchange.index');
    }

    /**
     * @return RedirectResponse
     */
    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'review_tnx' => 'required',
            'review_type' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        try {

            $input = [
                'tnx' => $request->review_tnx,
                'type' => $request->review_type,
            ];

            if (isset($input['tnx'])) {
                $transaction = Transaction::where('tnx', $input['tnx'])->first();

                if ($transaction) {
                    $user = $transaction->user;

                    if ($input['type'] == "approve") {
                        $transaction->status = TxnStatus::Success;

                        if ($transaction->description == "Main to Profit Wallet Exchange") {
                            $user->increment('profit_balance', $transaction->amount);
                        } else {
                            $user->increment('balance', $transaction->amount);
                        }
                        
                    } else {
                        $transaction->status = TxnStatus::Rejected;

                        if ($transaction->description == "Main to Profit Wallet Exchange") {
                            $user->increment('balance', $transaction->amount);
                        } else {
                            $user->increment('profit_balance', $transaction->amount);
                        }
                    }

                    $transaction->save();
                }                
            } 

            $status = 'success';
            $message = $input['type'] == "approve" ? __('Exchange Transaction Is Approved Successfully') : __('Exchange Transaction Is Rejected Successfully');

        } catch (Exception $e) {

            $status = 'warning';
            $message = __('something is wrong');
        }

        notify()->$status($message, $status);

        return redirect()->back();
    }
}
